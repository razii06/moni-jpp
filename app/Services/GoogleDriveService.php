<?php

namespace App\Services;

use Google\Client;
use Google\Http\MediaFileUpload;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;

class GoogleDriveService
{
    protected Drive $driveService;
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setClientId(env('GOOGLE_DRIVE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_DRIVE_CLIENT_SECRET'));

        $token = $this->client->fetchAccessTokenWithRefreshToken(env('GOOGLE_DRIVE_REFRESH_TOKEN'));

        if (isset($token['error'])) {
            throw new \Exception("Gagal Otentikasi Google Drive: " . ($token['error_description'] ?? $token['error']));
        }

        $this->driveService = new Drive($this->client);
    }

    public function getOrCreateJobFolder(string $parentFolderId, string $folderName): string
    {
        $safeFolderName = preg_replace('/[\/\\\:\*\?"<>\|]/', '_', trim($folderName));

        $query = sprintf(
            "'%s' in parents and name = '%s' and mimeType = 'application/vnd.google-apps.folder' and trashed = false",
            $parentFolderId,
            addslashes($safeFolderName)
        );

        $results = $this->driveService->files->listFiles([
            'q' => $query,
            'fields' => 'files(id, name)'
        ]);

        if (count($results->getFiles()) > 0) {
            return $results->getFiles()[0]->getId();
        }

        $folderMetadata = new DriveFile([
            'name'     => $safeFolderName,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents'  => array_filter([$parentFolderId])
        ]);

        $folder = $this->driveService->files->create($folderMetadata, ['fields' => 'id']);

        $this->makePublicIfConfigured($folder->id);

        return $folder->id;
    }

    public function deleteFolderByJobPackage(?string $folderId, ?string $parentFolderId, string $folderName): void
    {
        if ($folderId) {
            try {
                $this->driveService->files->delete($folderId);
                return;
            } catch (\Exception $e) {}
        }

        $safeFolderName = preg_replace('/[\/\\\:\*\?"<>\|]/', '_', trim($folderName));

        $query = sprintf(
            "'%s' in parents and name = '%s' and mimeType = 'application/vnd.google-apps.folder' and trashed = false",
            $parentFolderId,
            addslashes($safeFolderName)
        );

        $results = $this->driveService->files->listFiles([
            'q' => $query,
            'fields' => 'files(id, name)'
        ]);

        foreach ($results->getFiles() as $folder) {
            $this->driveService->files->delete($folder->getId());
        }
    }

    public function deleteFile(string $fileId): void
    {
        try {
            $this->driveService->files->delete($fileId);
        } catch (\Exception $e) {}
    }

    public function uploadFile(string $filename, string $mimeType, string $realPath, int $fileSize, string $targetFolderId): ?object
    {
        if (empty($realPath) || !file_exists($realPath)) {
            return null;
        }

        $fileMetadata = new DriveFile([
            'name'    => $filename,
            'parents' => array_filter([$targetFolderId]),
        ]);

        $this->client->setDefer(true);
        $requestFile = $this->driveService->files->create($fileMetadata, ['fields' => 'id, webViewLink']);

        $chunkSizeBytes = 1 * 1024 * 1024;
        $media = new MediaFileUpload($this->client, $requestFile, $mimeType, null, true, $chunkSizeBytes);
        $media->setFileSize($fileSize);

        $status = false;
        $handle = fopen($realPath, 'rb');

        if (!$handle) {
            $this->client->setDefer(false);
            return null;
        }

        while (!$status && !feof($handle)) {
            $chunk = fread($handle, $chunkSizeBytes);
            $status = $media->nextChunk($chunk);
        }

        fclose($handle);
        $this->client->setDefer(false);

        $uploadedFile = $status;

        if ($uploadedFile && isset($uploadedFile->id)) {
            $this->makePublicIfConfigured($uploadedFile->id);
        }

        return $uploadedFile ?: null;
    }

    private function makePublicIfConfigured(string $fileId): void
    {
        if (!filter_var(config('services.google_drive.public_links', false), FILTER_VALIDATE_BOOL)) {
            return;
        }

        $permission = new Drive\Permission(['type' => 'anyone', 'role' => 'reader']);
        $this->driveService->permissions->create($fileId, $permission);
    }

}