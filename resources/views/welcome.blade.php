@extends('layouts.app')

@section('title', config('app.name', 'BuluLand') . ' - Management Portal')

@section('content')
<div class="min-h-screen bg-neutral-100">
    {{-- Mobile-width centered container --}}
    <div class="mx-auto min-h-screen sm:max-w-md bg-white shadow-xl">

        {{-- Header --}}
        <header class="sticky top-0 z-50 border-b border-neutral-200 bg-white/90 backdrop-blur-md">
            <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-3 sm:px-6">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <span class="grid h-8 w-8 place-items-center rounded-full bg-neutral-800 text-white">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 3L20.5 7.8V16.2L12 21L3.5 16.2V7.8L12 3Z" fill="currentColor" />
                        </svg>
                    </span>
                    <span class="text-base font-semibold tracking-tight">{{ config('app.name', 'BuluLand') }}</span>
                </a>

                @if (Route::has('login'))
                @auth
                <a href="{{ url('/dashboard') }}"
                    class="inline-flex h-9 items-center justify-center rounded-lg bg-neutral-800 px-4 text-sm font-medium text-white transition hover:bg-neutral-900 active:scale-[0.98]">
                    Dashboard
                </a>
                @else
                <div class="flex items-center gap-2">
                    <a href="{{ route('login') }}"
                        class="inline-flex h-9 items-center justify-center rounded-lg border border-neutral-300 px-4 text-sm font-medium text-neutral-600 transition hover:bg-neutral-100 active:scale-[0.98]">
                        Log in
                    </a>
                    @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                        class="inline-flex h-9 items-center justify-center rounded-lg bg-neutral-800 px-4 text-sm font-medium text-white transition hover:bg-neutral-900 active:scale-[0.98]">
                        Register
                    </a>
                    @endif
                </div>
                @endauth
                @endif
            </div>
        </header>

        <main class="px-4 py-4 pb-28 sm:px-6 sm:pb-0">

            {{-- Hero Section --}}
            <section class="mb-8">
                <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-5 sm:p-6">
                    <div class="flex items-center gap-4">
                        <span class="grid h-14 w-14 shrink-0 place-items-center rounded-xl bg-neutral-800 text-white">
                            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 3L20.5 7.8V16.2L12 21L3.5 16.2V7.8L12 3Z" fill="currentColor" />
                            </svg>
                        </span>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">{{ config('app.name', 'BuluLand')
                                }}</h1>
                            <p class="mt-1 text-sm leading-relaxed text-neutral-500">Management Portal</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Quick Actions --}}
            <section class="mb-8">
                <div class="mb-4">
                    <span class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Menu Utama</span>
                </div>
                <div class="space-y-3">
                    @auth
                    <a href="{{ url('/admin') }}"
                        class="flex items-center gap-4 rounded-xl border border-neutral-200 bg-white p-4 transition hover:border-neutral-300 hover:bg-neutral-50 active:scale-[0.99]">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-neutral-800 text-white">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </span>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-neutral-900">Admin Panel</p>
                            <p class="text-xs text-neutral-500">Kelola pengaturan, event, dan data aplikasi</p>
                        </div>
                        <svg class="h-4 w-4 shrink-0 text-neutral-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    @else
                    <a href="{{ route('login') }}"
                        class="flex items-center gap-4 rounded-xl border border-neutral-200 bg-white p-4 transition hover:border-neutral-300 hover:bg-neutral-50 active:scale-[0.99]">
                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-neutral-800 text-white">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                        </span>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-neutral-900">Masuk ke Admin</p>
                            <p class="text-xs text-neutral-500">Login untuk mengakses panel administrasi</p>
                        </div>
                        <svg class="h-4 w-4 shrink-0 text-neutral-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    @endauth

                    @auth
                    <a href="{{ url('/admin') }}"
                        class="flex items-center gap-4 rounded-xl border border-neutral-200 bg-white p-4 transition hover:border-neutral-300 hover:bg-neutral-50 active:scale-[0.99]">
                        <span
                            class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-neutral-100 text-neutral-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </span>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-neutral-900">Event Management</p>
                            <p class="text-xs text-neutral-500">Atur dan kelola event yang tersedia</p>
                        </div>
                        <svg class="h-4 w-4 shrink-0 text-neutral-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    @endauth
                </div>
            </section>

            {{-- Info Card --}}
            <section class="mb-12">
                <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-5 sm:p-6">
                    <div class="flex items-start gap-3">
                        <span
                            class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-neutral-200 text-neutral-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-neutral-900">Area Internal</p>
                            <p class="mt-1 text-xs leading-relaxed text-neutral-500">
                                Halaman ini khusus untuk pengelolaan dan administrasi {{ config('app.name', 'BuluLand')
                                }}.
                                Silakan login menggunakan akun yang terdaftar untuk mengakses panel admin.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

        </main>

        {{-- Footer --}}
        <footer class="border-t border-neutral-200 bg-neutral-50">
            <div class="px-4 py-8 sm:px-6">
                <div class="flex flex-col gap-6 sm:grid sm:grid-cols-2">
                    <div>
                        <div class="flex items-center gap-3">
                            <span
                                class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-neutral-800 text-white">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 3L20.5 7.8V16.2L12 21L3.5 16.2V7.8L12 3Z" fill="currentColor" />
                                </svg>
                            </span>
                            <span class="text-base font-semibold">{{ config('app.name', 'BuluLand') }}</span>
                        </div>
                        <p class="mt-3 text-sm leading-relaxed text-neutral-500">Management Portal — Kelola event,
                            pantau keuangan, dan atur semuanya dari satu tempat.</p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Navigasi</p>
                        <div class="mt-3 space-y-2 text-sm text-neutral-500">
                            @auth
                            <a href="{{ url('/admin') }}" class="block transition hover:text-neutral-800">Admin
                                Panel</a>
                            <a href="{{ url('/dashboard') }}"
                                class="block transition hover:text-neutral-800">Dashboard</a>
                            @else
                            <a href="{{ route('login') }}" class="block transition hover:text-neutral-800">Login</a>
                            @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="block transition hover:text-neutral-800">Register</a>
                            @endif
                            @endauth
                        </div>
                        <p class="mt-6 text-xs text-neutral-400">&copy; {{ date('Y') }} {{ config('app.name',
                            'BuluLand') }}. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</div>
@endsection