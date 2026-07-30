@extends('layouts.app')

@section('title', 'Belum Bayar Kontribusi - ' . $event->name)

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

        <main class="px-4 py-6 pb-16 sm:px-6 lg:px-4 lg:py-8">
            <div class="mb-6">
                <span class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Transparansi
                    Keuangan</span>
                <h1 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl lg:text-2xl">Belum Bayar Kontribusi</h1>
                <p class="mt-2 max-w-xl text-sm leading-relaxed text-neutral-500">
                    @if ($contributionFee > 0)
                    Daftar rumah yang belum melunasi kontribusi sebesar Rp {{ number_format($contributionFee, 0, ',', '.') }}
                    untuk {{ $event->name }}.
                    @else
                    Biaya kontribusi untuk event ini belum diatur.
                    @endif
                </p>
            </div>

            <div x-data="{ search: '' }">
                <div class="mb-5">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-neutral-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" x-model="search" placeholder="Cari nomor rumah..."
                            class="h-11 w-full rounded-lg border border-neutral-300 bg-white pl-9 pr-3 text-sm outline-none transition placeholder:text-neutral-400 focus:border-neutral-800">
                    </div>
                </div>

                <div class="rounded-xl border border-neutral-200">
                    <div class="border-b border-neutral-100 px-4 py-3">
                        <h3 class="text-sm font-bold tracking-tight">
                            Rumah Belum Lunas
                            <span
                                class="rounded-full bg-neutral-100 px-2 py-0.5 text-[10px] font-medium text-neutral-500">{{
                                $unpaidHouses->count() }}</span>
                        </h3>
                    </div>

                    <div class="divide-y divide-neutral-100">
                        @forelse ($unpaidHouses as $house)
                        <div x-data="{ code: @js($house['code']) }"
                            x-show="!search || code.toLowerCase().includes(search.toLowerCase())"
                            class="flex items-center justify-between px-4 py-3.5">
                            <p class="text-sm font-medium text-neutral-700">{{ $house['code'] }}</p>
                            <div class="text-right">
                                @if ($house['paid'] > 0)
                                <p class="text-xs text-neutral-400">Sudah bayar Rp {{ number_format($house['paid'], 0, ',', '.') }}</p>
                                @endif
                                <p class="text-sm font-semibold text-neutral-800">
                                    Sisa Rp {{ number_format($house['remaining'], 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        @empty
                        <div class="px-4 py-12 text-center text-sm text-neutral-400">
                            Semua rumah sudah melunasi kontribusi.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection
