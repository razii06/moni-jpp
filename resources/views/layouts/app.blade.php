<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Animasi Pop-Up Halaman & Smooth Scroll -->
        <style>
            html {
                scroll-behavior: smooth;
            }
            .page-animate {
                animation: pagePopIn 0.45s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }
            @keyframes pagePopIn {
                0% {
                    opacity: 0;
                    transform: translateY(16px) scale(0.98);
                }
                100% {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content dengan Animasi Pop-Up -->
            <main class="page-animate">
                {{ $slot }}
            </main>
        </div>

        <!-- SweetAlert2 untuk Flash Messages Interaktif -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });

                @if(session('status') === 'profile-updated')
                    Toast.fire({
                        icon: 'success',
                        title: 'Profil berhasil diperbarui!'
                    });
                @elseif(session('status') === 'password-updated')
                    Toast.fire({
                        icon: 'success',
                        title: 'Kata sandi berhasil diubah!'
                    });
                @elseif(session('status') === 'verification-link-sent')
                    Toast.fire({
                        icon: 'success',
                        title: 'Tautan verifikasi telah dikirim.'
                    });
                @elseif(session('success'))
                    Toast.fire({
                        icon: 'success',
                        title: '{{ session("success") }}'
                    });
                @elseif(session('status') && !in_array(session('status'), ['profile-updated', 'password-updated', 'verification-link-sent']))
                    Toast.fire({
                        icon: 'success',
                        title: '{{ session("status") }}'
                    });
                @endif
                
                @if(session('error'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: '{{ session("error") }}',
                        confirmButtonColor: '#0f2b5c',
                    });
                @endif
            });
        </script>
    </body>
</html>