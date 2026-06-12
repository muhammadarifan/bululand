@extends('layouts.app')

@section('title', 'Event ' . ucfirst($event))

@section('content')
@php
    $balance = $totalIncome - $totalExpense;
    $eventTitle = ucfirst($event);
@endphp

<div class="min-h-screen bg-[#FDFDFC] text-[#1B1B18] font-[Instrument_Sans]">
    <header class="sticky top-0 z-50 border-b border-stone-200/70 bg-[#FDFDFC]/85 backdrop-blur-xl">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('events.show', $event) }}" class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-2xl bg-[#FF7A1A] text-white shadow-lg shadow-orange-500/25">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 3L20.5 7.8V16.2L12 21L3.5 16.2V7.8L12 3Z" fill="currentColor" opacity="0.95"/>
                        <path d="M12 7.2L16.2 9.6V14.4L12 16.8L7.8 14.4V9.6L12 7.2Z" fill="#1B1B18"/>
                    </svg>
                </span>
                <span class="text-xl font-bold tracking-tight text-[#1B1B18]">Bululand</span>
            </a>

            <nav class="hidden items-center gap-2 md:flex">
                <a href="#keuangan" class="rounded-full px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-100 hover:text-stone-950">Keuangan</a>
                <a href="#iuran" class="rounded-full px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-100 hover:text-stone-950">Iuran</a>
                <a href="#pengumuman" class="rounded-full px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-100 hover:text-stone-950">Pengumuman</a>
                <a href="#galeri" class="rounded-full px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-100 hover:text-stone-950">Galeri</a>
            </nav>

            <a href="#iuran" class="rounded-full bg-[#111827] px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-stone-900/15 transition hover:bg-[#030712]">Cek Iuran</a>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <section id="keuangan" class="mb-10">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <span class="inline-flex rounded-full bg-orange-100 px-4 py-2 text-sm font-bold text-orange-700">Transparansi Keuangan</span>
                    <h1 class="mt-5 text-4xl font-bold tracking-tight text-[#111827] sm:text-5xl">Ringkasan Pemasukan & Pengeluaran</h1>
                    <p class="mt-4 max-w-2xl text-lg leading-8 text-stone-600">Informasi keuangan Event {{ $eventTitle }} ditampilkan paling atas agar warga bisa melihat ringkasan iuran dengan cepat.</p>
                </div>
                <div class="inline-flex items-center gap-3 rounded-full border border-stone-200 bg-white px-4 py-3 shadow-sm">
                    <div class="flex -space-x-2">
                        <span class="grid h-8 w-8 place-items-center rounded-full border-2 border-white bg-orange-500 text-xs font-bold text-white">B</span>
                        <span class="grid h-8 w-8 place-items-center rounded-full border-2 border-white bg-blue-500 text-xs font-bold text-white">R</span>
                        <span class="grid h-8 w-8 place-items-center rounded-full border-2 border-white bg-emerald-500 text-xs font-bold text-white">W</span>
                    </div>
                    <span class="text-sm font-medium text-stone-600">Update terbaru</span>
                </div>
            </div>

            <div class="mt-8 grid gap-5 md:grid-cols-3">
                <div class="rounded-[2rem] border border-emerald-100 bg-white p-6 shadow-[0_24px_80px_rgba(15,23,42,0.08)]">
                    <div class="mb-5 flex items-center justify-between">
                        <div class="grid h-12 w-12 place-items-center rounded-2xl bg-emerald-100 text-emerald-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6z"/>
                            </svg>
                        </div>
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-700">Pemasukan</span>
                    </div>
                    <p class="text-sm font-semibold text-stone-500">Total Pemasukan</p>
                    <p class="mt-2 text-3xl font-black text-emerald-600">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                </div>

                <div class="rounded-[2rem] border border-red-100 bg-white p-6 shadow-[0_24px_80px_rgba(15,23,42,0.08)]">
                    <div class="mb-5 flex items-center justify-between">
                        <div class="grid h-12 w-12 place-items-center rounded-2xl bg-red-100 text-red-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </div>
                        <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-bold text-red-700">Pengeluaran</span>
                    </div>
                    <p class="text-sm font-semibold text-stone-500">Total Pengeluaran</p>
                    <p class="mt-2 text-3xl font-black text-red-600">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
                </div>

                <div class="rounded-[2rem] border border-blue-100 bg-white p-6 shadow-[0_24px_80px_rgba(15,23,42,0.08)]">
                    <div class="mb-5 flex items-center justify-between">
                        <div class="grid h-12 w-12 place-items-center rounded-2xl bg-blue-100 text-blue-600">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">Saldo</span>
                    </div>
                    <p class="text-sm font-semibold text-stone-500">Sisa Saldo</p>
                    <p class="mt-2 text-3xl font-black {{ $balance >= 0 ? 'text-emerald-600' : 'text-red-600' }}">Rp {{ number_format($balance, 0, ',', '.') }}</p>
                </div>
            </div>
        </section>

        <section id="iuran" class="mb-10">
            <div class="overflow-hidden rounded-[2.5rem] bg-[#111827] p-6 shadow-[0_32px_90px_rgba(15,23,42,0.18)] sm:p-8 lg:p-10">
                <div class="grid gap-8 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
                    <div class="text-white">
                        <span class="inline-flex rounded-full bg-white/10 px-4 py-2 text-sm font-bold text-orange-300">Cek Iuran</span>
                        <h2 class="mt-5 text-3xl font-bold tracking-tight sm:text-4xl">Cek status iuran dan pembayaran</h2>
                        <p class="mt-4 text-lg leading-8 text-stone-300">Masukkan nama rumah untuk melihat status pembayaran iuran secara cepat.</p>
                    </div>

                    <div class="rounded-[2rem] bg-white p-3 shadow-2xl shadow-stone-900/20">
                        <form method="GET" action="{{ route('events.show', $event) }}" class="flex flex-col gap-3 sm:flex-row">
                            <div class="relative flex-1">
                                <svg class="absolute left-5 top-1/2 h-5 w-5 -translate-y-1/2 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text" name="search" placeholder="Masukkan nama rumah..." value="{{ $searchQuery }}" class="h-14 w-full rounded-[1.25rem] border border-stone-200 bg-stone-50 pl-14 pr-5 text-base font-medium text-stone-800 outline-none transition placeholder:text-stone-400 focus:border-orange-400 focus:bg-white focus:ring-4 focus:ring-orange-100">
                            </div>
                            <button type="submit" class="inline-flex h-14 items-center justify-center gap-2 rounded-[1.25rem] bg-[#111827] px-7 text-sm font-bold text-white transition hover:bg-[#030712] sm:h-auto">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Cek Iuran
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        @if ($searchQuery !== '' && isset($contributionResults['not_found']))
        <section class="mb-10">
            <div class="rounded-[1.75rem] border border-red-200 bg-red-50 px-6 py-5 text-red-700 shadow-sm">
                <p class="font-bold text-lg">Pencarian tidak ditemukan</p>
                <p class="mt-1 text-sm leading-6">Tidak ada data iuran untuk "<span class="font-semibold">{{ $contributionResults['query'] }}</span>". Silakan periksa kembali nama rumah Anda.</p>
            </div>
        </section>
        @endif

        @if ($searchQuery !== '' && !isset($contributionResults['not_found']))
        <section class="mb-10">
            <div class="overflow-hidden rounded-[2rem] border border-stone-200 bg-white shadow-sm">
                <div class="flex flex-col gap-3 border-b border-stone-100 p-6 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-orange-600">Hasil Pencarian</p>
                        <h2 class="mt-1 text-2xl font-bold text-[#111827]">"{{ $searchQuery }}"</h2>
                    </div>
                    <span class="w-fit rounded-full bg-stone-100 px-4 py-2 text-sm font-semibold text-stone-600">{{ count($contributionResults) }} data ditemukan</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-stone-50 text-sm font-semibold text-stone-500">
                            <tr>
                                <th class="px-6 py-4">Nama Rumah</th>
                                <th class="px-6 py-4 text-right">Jumlah Pembayaran</th>
                                <th class="px-6 py-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @foreach ($contributionResults as $result)
                            <tr class="transition hover:bg-stone-50">
                                <td class="px-6 py-5 text-base font-bold text-[#111827]">{{ $result['name'] }}</td>
                                <td class="px-6 py-5 text-right text-base font-semibold text-stone-700">Rp {{ number_format($result['paid_amount'], 0, ',', '.') }}</td>
                                <td class="px-6 py-5 text-center">
                                    @if ($result['paid_amount'] > 0)
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-4 py-2 text-sm font-bold text-emerald-700">Sudah Bayar</span>
                                    @else
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-4 py-2 text-sm font-bold text-red-700">Belum Bayar</span>
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

        <section id="pengumuman" class="mb-10">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <span class="inline-flex rounded-full bg-red-100 px-4 py-2 text-sm font-bold text-red-700">Pengumuman</span>
                    <h2 class="mt-5 text-3xl font-bold tracking-tight text-[#111827] sm:text-4xl">Pengumuman Penting</h2>
                    <p class="mt-4 max-w-2xl text-lg leading-8 text-stone-600">Informasi terbaru untuk warga seputar iuran, kegiatan, dan agenda lingkungan.</p>
                </div>
            </div>

            <div class="mt-8 grid gap-4 lg:grid-cols-2">
                @foreach ($announcements as $announcement)
                <article class="rounded-[2rem] border border-stone-200 bg-white p-6 shadow-sm transition hover:shadow-xl">
                    <div class="flex gap-4">
                        <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-red-100 text-red-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="mb-2 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                <h3 class="text-xl font-bold text-[#111827]">{{ $announcement['title'] }}</h3>
                                <span class="text-sm font-semibold text-stone-500">{{ $announcement['date'] }}</span>
                            </div>
                            <p class="leading-7 text-stone-600">{{ $announcement['content'] }}</p>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
        </section>

        <section id="galeri">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <span class="inline-flex rounded-full bg-blue-100 px-4 py-2 text-sm font-bold text-blue-700">Galeri</span>
                    <h2 class="mt-5 text-3xl font-bold tracking-tight text-[#111827] sm:text-4xl">Galeri Kegiatan</h2>
                    <p class="mt-4 max-w-2xl text-lg leading-8 text-stone-600">Dokumentasi momen kegiatan dan kebersamaan warga Bululand.</p>
                </div>
                <a href="#keuangan" class="inline-flex items-center justify-center rounded-full border border-stone-200 bg-white px-5 py-3 text-sm font-bold text-stone-700 transition hover:bg-stone-50">Kembali ke atas</a>
            </div>

            <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($galleryEvents as $eventItem)
                <article class="group overflow-hidden rounded-[2rem] border border-stone-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                    <div class="relative aspect-[4/3] overflow-hidden">
                        <img src="{{ $eventItem['url'] }}" alt="{{ $eventItem['title'] }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-110">
                        <div class="absolute inset-x-0 bottom-0 bg-linear-to-t from-[#111827]/70 to-transparent p-5 text-white">
                            <p class="font-bold">{{ $eventItem['title'] }}</p>
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="inline-flex items-center gap-2 rounded-full bg-stone-100 px-3 py-2 text-sm font-semibold text-stone-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $eventItem['date'] }}
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
        </section>
    </main>

    <footer class="mt-16 bg-[#111827] text-white">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 md:grid-cols-3 lg:px-8">
            <div>
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 place-items-center rounded-2xl bg-[#FF7A1A] text-white">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 3L20.5 7.8V16.2L12 21L3.5 16.2V7.8L12 3Z" fill="currentColor"/>
                        </svg>
                    </span>
                    <span class="text-xl font-bold">Bululand</span>
                </div>
                <p class="mt-4 max-w-sm leading-7 text-stone-400">Platform informasi kegiatan dan iuran warga Bululand.</p>
            </div>

            <div>
                <p class="font-bold">Navigasi</p>
                <div class="mt-4 space-y-3 text-stone-400">
                    <a href="#keuangan" class="block transition hover:text-white">Keuangan</a>
                    <a href="#iuran" class="block transition hover:text-white">Iuran</a>
                    <a href="#pengumuman" class="block transition hover:text-white">Pengumuman</a>
                    <a href="#galeri" class="block transition hover:text-white">Galeri</a>
                </div>
            </div>

            <div>
                <p class="font-bold">Kontak</p>
                <div class="mt-4 space-y-3 text-stone-400">
                    <p>Kelurahan Bululand</p>
                    <p>bululand.web.id</p>
                    <p>&copy; {{ date('Y') }} Bululand Web</p>
                </div>
            </div>
        </div>
    </footer>
</div>
@endsection
