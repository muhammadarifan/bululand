@extends('layouts.app')

@section('title', 'Kode Akses - ' . $event->name)

@section('content')
<div class="min-h-screen bg-white lg:bg-neutral-100">
    <div class="mx-auto min-h-screen bg-white lg:max-w-md lg:shadow-xl">

        <header class="sticky top-0 z-50 border-b border-neutral-200 bg-white/90 backdrop-blur-md">
            <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-3 sm:px-6 lg:px-4">
                <a href="{{ route('events.show', $event->subdomain) }}" class="flex items-center gap-3">
                    @if ($eventDetail && $eventDetail->logo)
                    <img src="{{ $eventDetail->logo }}" alt="{{ $event->name }} logo" class="h-8 w-8 rounded-full">
                    @else
                    <span class="grid h-8 w-8 place-items-center rounded-full bg-neutral-800 text-white">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 3L20.5 7.8V16.2L12 21L3.5 16.2V7.8L12 3Z" fill="currentColor" />
                        </svg>
                    </span>
                    @endif
                    <span class="text-base font-semibold tracking-tight">{{ $event->name }}</span>
                </a>
                <a href="{{ route('events.show', $event->subdomain) }}"
                    class="inline-flex h-9 items-center rounded-lg border border-neutral-300 px-3.5 text-xs font-medium text-neutral-600 transition hover:bg-neutral-100 active:scale-[0.98]">
                    <svg class="mr-1.5 h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali
                </a>
            </div>
        </header>

        <main class="flex flex-col items-center px-4 py-16 sm:px-6 lg:px-4">
            <div class="grid h-12 w-12 place-items-center rounded-full bg-neutral-100">
                <svg class="h-6 w-6 text-neutral-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 10-8 0v4h8z" />
                </svg>
            </div>

            <h1 class="mt-4 text-xl font-bold tracking-tight">Halaman Terbatas</h1>
            <p class="mt-2 max-w-xs text-center text-sm leading-relaxed text-neutral-500">
                Masukkan kode akses untuk melihat daftar rumah yang belum membayar uang kontribusi.
            </p>

            <form method="POST" action="{{ route('events.unpaid-contributions.unlock', $event->subdomain) }}"
                class="mt-8 w-full max-w-xs">
                @csrf

                <label for="access_code" class="sr-only">Kode Akses</label>
                <input type="password" id="access_code" name="access_code" autocomplete="off" autofocus
                    placeholder="Kode akses"
                    class="h-11 w-full rounded-lg border border-neutral-300 bg-white px-3.5 text-center text-sm tracking-widest outline-none transition placeholder:text-neutral-400 focus:border-neutral-800">

                @error('access_code')
                <p class="mt-2 text-center text-xs font-medium text-red-500">{{ $message }}</p>
                @enderror

                <button type="submit"
                    class="mt-4 h-11 w-full rounded-lg bg-neutral-800 text-sm font-medium text-white transition hover:bg-neutral-700 active:scale-[0.98]">
                    Buka
                </button>
            </form>
        </main>
    </div>
</div>
@endsection
