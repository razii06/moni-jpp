<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Google\Client;
use Google\Service\Drive;
use Masbug\Flysystem\GoogleDriveAdapter;
use League\Flysystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Mempertahankan penanganan HTTPS proxy
        if (request()->header('x-forwarded-proto') == 'https') {
            URL::forceScheme('https');
        }

        // Mendaftarkan Custom Storage Driver 'google'
        Storage::extend('google', function ($app, $config) {
            $client = new Client();
            $client->setClientId($config['clientId']);
            $client->setClientSecret($config['clientSecret']);
            $client->refreshToken($config['refreshToken']);

            $service = new Drive($client);
            $adapter = new GoogleDriveAdapter($service, $config['folder'] ?? '/');
            $driver = new Filesystem($adapter);

            return new FilesystemAdapter($driver, $adapter);
        });

        // --- DEFINISI HAK AKSES (GATES) ---
        
        // 1. Akses Hapus (Hanya Admin)
        Gate::define('delete-data', function ($user) {
            return $user->role === 'admin';
        });

        // 2. Akses Tambah (Admin & PIC JPP)
        Gate::define('create-data', function ($user) {
            return in_array($user->role, ['admin', 'pic_jpp', 'staff']);
        });

        // 3. Akses Edit (Semua bisa)
        Gate::define('edit-data', function ($user) {
            return in_array($user->role, ['admin', 'pic_jpp', 'staff', 'super_vc']);
        });
    }
}