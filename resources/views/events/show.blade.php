@extends('layouts.app')

@section('title', 'Event ' . $event->name)

@section('content')
@php
$balance = $totalIncome - $totalExpense;
@endphp

<div class="min-h-screen bg-white text-neutral-900 antialiased">
    {{-- Header --}}
    <header class="sticky top-0 z-50 border-b border-neutral-200 bg-white/90 backdrop-blur-md">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-3 sm:px-6">
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
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-4 py-10 pb-28 sm:px-6 sm:pb-0">

        {{-- Hero Section --}}
        @if ($eventDetail && ($eventDetail->hero_image || $eventDetail->hero_title || $eventDetail->hero_subtitle))
        <section class="mb-16">
            @if ($eventDetail->hero_image)
            <div class="overflow-hidden rounded-xl bg-neutral-100">
                <img src="{{ $eventDetail->hero_image }}" alt="{{ $event->name }}" class="w-full">
            </div>
            @endif
            @if ($eventDetail->hero_title || $eventDetail->hero_subtitle)
            <div class="mt-4">
                @if ($eventDetail->hero_title)
                <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">{{ $eventDetail->hero_title }}</h1>
                @endif
                @if ($eventDetail->hero_subtitle)
                <p class="mt-2 text-base leading-relaxed text-neutral-500">{{ $eventDetail->hero_subtitle }}</p>
                @endif
            </div>
            @endif
        </section>
        @endif

        {{-- Search Error --}}
        @if ($searchQuery !== '' && isset($contributionResults['not_found']))
        <section class="mb-10">
            <div class="rounded-lg border border-neutral-300 bg-neutral-50 px-5 py-4 text-neutral-600">
                <p class="font-semibold">Pencarian tidak ditemukan</p>
                <p class="mt-1 text-sm">Tidak ada data iuran untuk "<span class="font-medium">{{
                        $contributionResults['query'] }}</span>". Silakan periksa kembali nama rumah Anda.</p>
            </div>
        </section>
        @endif

        {{-- Search Results --}}
        @if ($searchQuery !== '' && !isset($contributionResults['not_found']))
        <section class="mb-10">
            <div class="overflow-hidden rounded-xl border border-neutral-200">
                <div class="flex items-center justify-between border-b border-neutral-100 px-5 py-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Hasil Pencarian</p>
                        <h2 class="mt-1 text-xl font-bold">"{{ $searchQuery }}"</h2>
                    </div>
                    <span class="rounded-full bg-neutral-100 px-3 py-1 text-xs font-medium text-neutral-500">{{
                        count($contributionResults) }} data</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-neutral-50 text-xs font-semibold uppercase tracking-wider text-neutral-400">
                            <tr>
                                <th class="px-5 py-3">Nama Rumah</th>
                                <th class="px-5 py-3 text-right">Jumlah</th>
                                <th class="px-5 py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-100">
                            @foreach ($contributionResults as $result)
                            <tr class="transition hover:bg-neutral-50">
                                <td class="px-5 py-4 font-semibold">{{ $result['name'] }}</td>
                                <td class="px-5 py-4 text-right font-medium">Rp {{ number_format($result['paid_amount'],
                                    0, ',', '.') }}</td>
                                <td class="px-5 py-4 text-center">
                                    @if ($result['paid_amount'] > 0)
                                    <span
                                        class="inline-flex rounded-full bg-neutral-800 px-3 py-1 text-xs font-medium text-white">Sudah
                                        Bayar</span>
                                    @else
                                    <span
                                        class="inline-flex rounded-full border border-neutral-300 px-3 py-1 text-xs font-medium text-neutral-500">Belum
                                        Bayar</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        @endif

        {{-- Cek Iuran Section --}}
        <section id="iuran" class="mb-16">
            <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-6 sm:p-8">
                <div class="grid gap-6 sm:grid-cols-2 sm:items-center">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Cek Iuran</span>
                        <h2 class="mt-2 text-2xl font-bold tracking-tight">Cek status iuran</h2>
                        <p class="mt-2 text-sm leading-relaxed text-neutral-500">Masukkan nama rumah untuk melihat
                            status pembayaran iuran.</p>
                    </div>
                    <div>
                        <form method="GET" action="{{ route('events.show', $event->subdomain) }}"
                            class="flex flex-col gap-2 sm:flex-row">
                            <div class="relative flex-1">
                                <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-neutral-400"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input type="text" name="search" placeholder="Nama rumah..." value="{{ $searchQuery }}"
                                    class="h-10 w-full rounded-lg border border-neutral-300 bg-white pl-9 pr-3 text-sm outline-none transition placeholder:text-neutral-400 focus:border-neutral-800">
                            </div>
                            <button type="submit"
                                class="inline-flex h-10 items-center justify-center rounded-lg bg-neutral-800 px-5 text-sm font-medium text-white transition hover:bg-neutral-900">
                                Cari
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        {{-- Keuangan Section --}}
        <section id="keuangan" class="mb-16">
            <div class="mb-6">
                <span class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Transparansi
                    Keuangan</span>
                <h1 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">Ringkasan Keuangan</h1>
                @if ($eventDetail && $eventDetail->hero_subtitle)
                <p class="mt-3 max-w-xl text-sm leading-relaxed text-neutral-500">{{ $eventDetail->hero_subtitle }}</p>
                @endif
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-xl border border-neutral-200 bg-white p-5">
                    <div class="mb-3 flex items-center justify-between">
                        <div class="grid h-10 w-10 place-items-center rounded-lg bg-neutral-100 text-neutral-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6z" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-neutral-400">Pemasukan</span>
                    </div>
                    <p class="text-2xl font-bold">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                </div>

                <div class="rounded-xl border border-neutral-200 bg-white p-5">
                    <div class="mb-3 flex items-center justify-between">
                        <div class="grid h-10 w-10 place-items-center rounded-lg bg-neutral-100 text-neutral-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-neutral-400">Pengeluaran</span>
                    </div>
                    <p class="text-2xl font-bold">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
                </div>

                <div class="rounded-xl border border-neutral-200 bg-white p-5">
                    <div class="mb-3 flex items-center justify-between">
                        <div class="grid h-10 w-10 place-items-center rounded-lg bg-neutral-100 text-neutral-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-neutral-400">Saldo</span>
                    </div>
                    <p class="text-2xl font-bold {{ $balance >= 0 ? 'text-neutral-900' : 'text-neutral-500' }}">Rp {{
                        number_format($balance, 0, ',', '.') }}</p>
                </div>
            </div>
        </section>

        {{-- About Section --}}
        @if ($eventDetail && $eventDetail->about_title)
        <section id="tentang" class="mb-16">
            <div class="border-t border-neutral-200 pt-8">
                <h2 class="text-2xl font-bold tracking-tight">{{ $eventDetail->about_title }}</h2>
                <div class="mt-3 text-sm leading-relaxed text-neutral-500">
                    {!! nl2br(e($eventDetail->about_content)) !!}
                </div>
            </div>
        </section>
        @endif

        {{-- Video Section --}}
        @if ($eventDetail && $eventDetail->youtube_url)
        <section id="video" class="mb-16">
            <div class="overflow-hidden rounded-xl border border-neutral-200">
                <div class="aspect-video">
                    <iframe src="{{ $eventDetail->youtube_url }}" title="YouTube video" class="h-full w-full"
                        allowfullscreen></iframe>
                </div>
            </div>
        </section>
        @endif
    </main>

    {{-- Mobile Bottom Nav --}}
    <nav
        class="fixed inset-x-0 bottom-0 z-50 border-t border-neutral-200 bg-white/95 pb-2 pt-1 backdrop-blur-md md:hidden">
        <div class="mx-auto grid max-w-xs grid-cols-4 gap-1 px-2">
            <a href="#keuangan"
                class="flex flex-col items-center gap-0.5 rounded-lg px-2 py-1.5 text-[10px] font-medium text-neutral-400 transition hover:bg-neutral-100 hover:text-neutral-800">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 8c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2Zm0 4c1.66 0 3 1.34 3 3v3H9v-3c0-1.66 1.34-3 3-3Zm6 2v-3c0-1.66-1.34-3-3-3-.37 0-.72.08-1.04.21C14.41 7.09 13.3 6 12 6c-1.3 0-2.41 1.09-2.96 2.21A2.98 2.98 0 0 0 8 8c-1.66 0-3 1.34-3 3v3H3v4h18v-4h-2Z" />
                </svg>
                <span>Keuangan</span>
            </a>
            <a href="#iuran"
                class="flex flex-col items-center gap-0.5 rounded-lg px-2 py-1.5 text-[10px] font-medium text-neutral-400 transition hover:bg-neutral-100 hover:text-neutral-800">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-3 7h6M9 15h6" />
                </svg>
                <span>Iuran</span>
            </a>
            <a href="#tentang"
                class="flex flex-col items-center gap-0.5 rounded-lg px-2 py-1.5 text-[10px] font-medium text-neutral-400 transition hover:bg-neutral-100 hover:text-neutral-800">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M11 5.882V19.24a1.76 1.76 0 0 1-3.417.592l-2.147-6.15M18 13a3 3 0 1 0 0-6M5.436 13.683A4.001 4.001 0 0 1 7 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 0 1-1.564-.317Z" />
                </svg>
                <span>Tentang</span>
            </a>
            <a href="#video"
                class="flex flex-col items-center gap-0.5 rounded-lg px-2 py-1.5 text-[10px] font-medium text-neutral-400 transition hover:bg-neutral-100 hover:text-neutral-800">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2l1.586-1.586a2 2 0 0 1 2.828 0L20 14m-6-6h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2Z" />
                </svg>
                <span>Video</span>
            </a>
        </div>
    </nav>

    {{-- Footer --}}
    <footer class="border-t border-neutral-200 bg-neutral-50">
        <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6">
            <div class="grid gap-8 sm:grid-cols-3">
                <div>
                    <div class="flex items-center gap-3">
                        @if ($eventDetail && $eventDetail->logo)
                        <img src="{{ $eventDetail->logo }}" alt="{{ $event->name }} logo" class="h-8 w-8 rounded-full">
                        @else
                        <span class="grid h-8 w-8 place-items-center rounded-full bg-neutral-800 text-white">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 3L20.5 7.8V16.2L12 21L3.5 16.2V7.8L12 3Z" fill="currentColor" />
                            </svg>
                        </span>
                        @endif
                        <span class="text-base font-semibold">{{ $event->name }}</span>
                    </div>
                    @if ($eventDetail && $eventDetail->footer_text)
                    <p class="mt-3 text-sm leading-relaxed text-neutral-500">{{ $eventDetail->footer_text }}</p>
                    @endif
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Navigasi</p>
                    <div class="mt-3 space-y-2 text-sm text-neutral-500">
                        <a href="#keuangan" class="block transition hover:text-neutral-800">Keuangan</a>
                        <a href="#iuran" class="block transition hover:text-neutral-800">Iuran</a>
                        @if ($eventDetail && $eventDetail->about_title)
                        <a href="#tentang" class="block transition hover:text-neutral-800">Tentang</a>
                        @endif
                        @if ($eventDetail && $eventDetail->youtube_url)
                        <a href="#video" class="block transition hover:text-neutral-800">Video</a>
                        @endif
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Kontak</p>
                    <div class="mt-3 space-y-2 text-sm text-neutral-500">
                        @if ($eventDetail && $eventDetail->contacts)
                        @foreach (json_decode($eventDetail->contacts, true) as $contact)
                        @if ($contact['name'] ?? $contact['phone'])
                        <p>{{ $contact['name'] ?? '' }}: {{ $contact['phone'] ?? '' }}</p>
                        @endif
                        @endforeach
                        @elseif ($eventDetail && ($eventDetail->contact_name || $eventDetail->contact_phone))
                        <p>{{ $eventDetail->contact_name }}: {{ $eventDetail->contact_phone }}</p>
                        @endif

                        @if ($eventDetail && $eventDetail->facebook_url)
                        <a href="{{ $eventDetail->facebook_url }}" target="_blank"
                            class="block transition hover:text-neutral-800">Facebook</a>
                        @endif
                        @if ($eventDetail && $eventDetail->instagram_url)
                        <a href="{{ $eventDetail->instagram_url }}" target="_blank"
                            class="block transition hover:text-neutral-800">Instagram</a>
                        @endif

                        <p class="pt-2 text-xs text-neutral-400">&copy; {{ date('Y') }} {{ $event->name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div>
@endsection