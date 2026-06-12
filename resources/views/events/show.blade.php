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
                <span
                    class="grid h-10 w-10 place-items-center rounded-2xl bg-[#FF7A1A] text-white shadow-lg shadow-orange-500/25">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 3L20.5 7.8V16.2L12 21L3.5 16.2V7.8L12 3Z" fill="currentColor" opacity="0.95" />
                        <path d="M12 7.2L16.2 9.6V14.4L12 16.8L7.8 14.4V9.6L12 7.2Z" fill="#1B1B18" />
                    </svg>
                </span>
                <span class="text-xl font-bold tracking-tight text-[#1B1B18]">Kindora</span>
            </a>

            <nav class="hidden items-center gap-2 md:flex">
                <a href="#ringkasan"
                    class="rounded-full px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-100 hover:text-stone-950">Ringkasan</a>
                <a href="#galeri"
                    class="rounded-full px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-100 hover:text-stone-950">Galeri</a>
                <a href="#pengumuman"
                    class="rounded-full px-4 py-2 text-sm font-medium text-stone-600 hover:bg-stone-100 hover:text-stone-950">Pengumuman</a>
            </nav>

            <a href="#pencarian"
                class="rounded-full bg-[#111827] px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-stone-900/15 transition hover:bg-[#030712]">Cek
                Data</a>
        </div>
    </header>

    <section class="relative overflow-hidden">
        <div
            class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(circle_at_10%_10%,rgba(255,122,26,0.16),transparent_32%),radial-gradient(circle_at_88%_18%,rgba(37,99,235,0.14),transparent_34%),linear-gradient(135deg,#FDFDFC_0%,#FFF7ED_46%,#EFF6FF_100%)]">
        </div>

        <div
            class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-12 sm:px-6 lg:grid-cols-[0.92fr_1.08fr] lg:px-8 lg:py-16">
            <div class="relative">
                <div
                    class="inline-flex items-center gap-3 rounded-full border border-stone-200 bg-white/80 px-4 py-2 shadow-sm backdrop-blur">
                    <div class="flex -space-x-2">
                        <span
                            class="grid h-8 w-8 place-items-center rounded-full border-2 border-white bg-orange-500 text-xs font-bold text-white">K</span>
                        <span
                            class="grid h-8 w-8 place-items-center rounded-full border-2 border-white bg-blue-500 text-xs font-bold text-white">B</span>
                        <span
                            class="grid h-8 w-8 place-items-center rounded-full border-2 border-white bg-emerald-500 text-xs font-bold text-white">R</span>
                    </div>
                    <span class="text-sm font-medium text-stone-600">1000+ Warga aktif</span>
                </div>

                <h1 class="mt-7 text-5xl font-bold tracking-tight text-[#111827] sm:text-6xl lg:text-7xl">
                    Together for making a brighter future
                </h1>

                <p class="mt-6 max-w-xl text-lg leading-8 text-stone-600">
                    Bersama, kita bisa memberi dampak nyata di lingkungan sekitar. Pantau kegiatan Kelurahan Bululand
                    melalui pencarian data, transparansi keuangan, galeri kegiatan, dan pengumuman penting.
                </p>

                <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                    <a href="#pencarian"
                        class="inline-flex items-center justify-center gap-2 rounded-full bg-[#111827] px-7 py-4 text-sm font-bold text-white shadow-xl shadow-stone-900/20 transition hover:bg-[#030712]">
                        Cek Data Warga
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 12H19M13 6L19 12L13 18" stroke="currentColor" stroke-width="2.2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </a>
                    <a href="#ringkasan"
                        class="inline-flex items-center justify-center gap-2 rounded-full border border-stone-200 bg-white px-7 py-4 text-sm font-bold text-stone-700 transition hover:border-stone-300 hover:bg-stone-50">
                        Pelajari Selengkapnya
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M9 6L15 12L9 18" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </a>
                </div>

                <div
                    class="mt-10 grid max-w-md grid-cols-3 gap-3 rounded-4xl border border-stone-200 bg-white/70 p-3 shadow-sm backdrop-blur">
                    <div class="rounded-2xl bg-stone-50 px-3 py-4 text-center">
                        <p class="text-2xl font-bold text-[#111827]">{{ $eventTitle }}</p>
                        <p class="mt-1 text-xs text-stone-500">Event</p>
                    </div>
                    <div class="rounded-2xl bg-stone-50 px-3 py-4 text-center">
                        <p class="text-2xl font-bold text-[#111827]">{{ date('Y') }}</p>
                        <p class="mt-1 text-xs text-stone-500">Tahun</p>
                    </div>
                    <div class="rounded-2xl bg-stone-50 px-3 py-4 text-center">
                        <p class="text-2xl font-bold text-[#111827]">RT</p>
                        <p class="mt-1 text-xs text-stone-500">Warga</p>
                    </div>
                </div>
            </div>

            <div
                class="relative min-h-155 overflow-hidden rounded-[2.5rem] bg-stone-200 shadow-[0_40px_120px_rgba(15,23,42,0.18)] lg:min-h-180">
                <img src="https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=1400&q=80"
                    alt="Kegiatan komunitas Bululand" class="h-full w-full object-cover">
                <div class="absolute inset-0 bg-linear-to-t from-[#111827]/70 via-transparent to-[#111827]/10"></div>

                <div class="absolute left-6 right-6 top-6 flex items-center justify-between">
                    <span
                        class="rounded-full bg-white/90 px-4 py-2 text-sm font-semibold text-stone-700 shadow-sm backdrop-blur">All
                        Pages</span>
                    <div class="flex gap-2">
                        <span
                            class="rounded-full bg-white/90 px-4 py-2 text-sm font-semibold text-stone-700 shadow-sm backdrop-blur">Causes</span>
                        <span
                            class="rounded-full bg-white/90 px-4 py-2 text-sm font-semibold text-stone-700 shadow-sm backdrop-blur">Blog</span>
                    </div>
                </div>

                <div
                    class="absolute left-6 top-24 max-w-xs rounded-[1.75rem] bg-white/95 p-5 shadow-2xl shadow-stone-900/15 backdrop-blur-xl">
                    <div class="flex items-center gap-3">
                        <span class="grid h-11 w-11 place-items-center rounded-full bg-orange-100 text-lg">😊</span>
                        <div>
                            <p class="text-sm font-semibold text-stone-500">Testimoni Warga</p>
                            <p class="font-semibold text-[#111827]">“Karena program ini, kami merasa lebih terbantu.”
                            </p>
                        </div>
                    </div>
                </div>

                <a href="#galeri"
                    class="absolute right-7 top-1/2 inline-flex -translate-y-1/2 items-center gap-3 rounded-full bg-white/95 px-5 py-3 text-sm font-bold text-[#111827] shadow-2xl shadow-stone-900/15 backdrop-blur transition hover:bg-white">
                    <span class="grid h-10 w-10 place-items-center rounded-full bg-[#111827] text-white">
                        <svg class="ml-0.5 h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M8 5.5V18.5L18 12L8 5.5Z" />
                        </svg>
                    </span>
                    Watch story reel
                </a>

                <div
                    class="absolute bottom-7 right-7 max-w-xs rounded-4xl bg-white/95 p-6 shadow-2xl shadow-stone-900/20 backdrop-blur-xl">
                    <div class="mb-4 flex items-center gap-3">
                        <span class="grid h-11 w-11 place-items-center rounded-2xl bg-blue-100 text-xl">🤝</span>
                        <div>
                            <p class="font-bold text-[#111827]">Dedicated team</p>
                            <p class="text-sm text-stone-500">Kelurahan Bululand</p>
                        </div>
                    </div>
                    <p class="text-sm leading-6 text-stone-600">
                        Memberikan informasi, sumber daya, dan dukungan penting bagi warga yang membutuhkan.
                    </p>
                </div>
            </div>
        </div>
    </section>

    @if ($searchQuery !== '' && isset($contributionResults['not_found']))
    <div class="mx-auto mb-8 max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-3xl border border-red-200 bg-red-50 px-6 py-5 text-red-700 shadow-sm">
            <p class="font-bold text-lg">Pencarian tidak ditemukan</p>
            <p class="mt-1 text-sm leading-6">Tidak ada data pembayaran untuk "<span class="font-semibold">{{
                    $contributionResults['query'] }}</span>". Silakan periksa kembali nama rumah Anda.</p>
        </div>
    </div>
    @endif

    <section id="pencarian" class="relative -mt-4 z-20 mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-4xl border border-stone-200 bg-white p-3 shadow-[0_24px_80px_rgba(15,23,42,0.12)]">
            <form method="GET" action="{{ route('events.show', $event) }}" class="flex flex-col gap-3 sm:flex-row">
                <div class="relative flex-1">
                    <svg class="absolute left-5 top-1/2 h-5 w-5 -translate-y-1/2 text-stone-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" name="search" placeholder="Masukkan nama rumah untuk cek data..."
                        value="{{ $searchQuery }}"
                        class="h-14 w-full rounded-[1.25rem] border border-stone-200 bg-stone-50 pl-14 pr-5 text-base font-medium text-stone-800 outline-none transition placeholder:text-stone-400 focus:border-orange-400 focus:bg-white focus:ring-4 focus:ring-orange-100">
                </div>
                <button type="submit"
                    class="inline-flex h-14 items-center justify-center gap-2 rounded-[1.25rem] bg-[#111827] px-7 text-sm font-bold text-white transition hover:bg-[#030712] sm:h-auto">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Cek Data Rumah
                </button>
            </form>
        </div>
    </section>

    @if ($searchQuery !== '' && !isset($contributionResults['not_found']))
    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-4xl border border-stone-200 bg-white shadow-sm">
            <div
                class="flex flex-col gap-3 border-b border-stone-100 p-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold text-orange-600">Hasil Pencarian</p>
                    <h2 class="mt-1 text-2xl font-bold text-[#111827]">"{{ $searchQuery }}"</h2>
                </div>
                <span class="w-fit rounded-full bg-stone-100 px-4 py-2 text-sm font-semibold text-stone-600">{{
                    count($contributionResults) }} data ditemukan</span>
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
                            <td class="px-6 py-5 text-right text-base font-semibold text-stone-700">Rp {{
                                number_format($result['paid_amount'], 0, ',', '.') }}</td>
                            <td class="px-6 py-5 text-center">
                                @if ($result['paid_amount'] > 0)
                                <span
                                    class="inline-flex items-center rounded-full bg-emerald-100 px-4 py-2 text-sm font-bold text-emerald-700">Sudah
                                    Bayar</span>
                                @else
                                <span
                                    class="inline-flex items-center rounded-full bg-red-100 px-4 py-2 text-sm font-bold text-red-700">Belum
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

    <main class="mx-auto max-w-7xl space-y-20 px-4 py-16 sm:px-6 lg:px-8">
        <section id="ringkasan" class="rounded-[2.5rem] border border-stone-200 bg-white p-6 shadow-sm sm:p-8 lg:p-10">
            <div class="mx-auto max-w-3xl text-center">
                <span
                    class="inline-flex rounded-full bg-orange-100 px-4 py-2 text-sm font-bold text-orange-700">Transparansi
                    Keuangan</span>
                <h2 class="mt-5 text-3xl font-bold tracking-tight text-[#111827] sm:text-4xl">Ringkasan Keuangan</h2>
                <p class="mt-4 text-lg leading-8 text-stone-600">Pantau pemasukan, pengeluaran, dan saldo kegiatan
                    secara ringkas dan mudah dipahami.</p>
            </div>

            <div class="mt-8 grid gap-5 md:grid-cols-3">
                <div class="rounded-[1.75rem] border border-emerald-100 bg-emerald-50/70 p-6">
                    <div class="mb-4 grid h-12 w-12 place-items-center rounded-2xl bg-white text-emerald-600 shadow-sm">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6z" />
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-emerald-700">Total Pemasukan</p>
                    <p class="mt-2 text-3xl font-black text-emerald-600">Rp {{ number_format($totalIncome, 0, ',', '.')
                        }}</p>
                </div>

                <div class="rounded-[1.75rem] border border-red-100 bg-red-50/70 p-6">
                    <div class="mb-4 grid h-12 w-12 place-items-center rounded-2xl bg-white text-red-600 shadow-sm">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-red-700">Total Pengeluaran</p>
                    <p class="mt-2 text-3xl font-black text-red-600">Rp {{ number_format($totalExpense, 0, ',', '.') }}
                    </p>
                </div>

                <div class="rounded-[1.75rem] border border-blue-100 bg-blue-50/70 p-6">
                    <div class="mb-4 grid h-12 w-12 place-items-center rounded-2xl bg-white text-blue-600 shadow-sm">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-blue-700">Saldo</p>
                    <p class="mt-2 text-3xl font-black {{ $balance >= 0 ? 'text-emerald-600' : 'text-red-600' }}">Rp {{
                        number_format($balance, 0, ',', '.') }}</p>
                </div>
            </div>
        </section>

        <section id="galeri" class="grid gap-8 lg:grid-cols-[0.85fr_1.15fr] lg:items-end">
            <div>
                <span class="inline-flex rounded-full bg-blue-100 px-4 py-2 text-sm font-bold text-blue-700">Galeri
                    Kegiatan</span>
                <h2 class="mt-5 text-3xl font-bold tracking-tight text-[#111827] sm:text-4xl">Momen terbaik bersama
                    warga</h2>
                <p class="mt-4 max-w-xl text-lg leading-8 text-stone-600">Dokumentasi kegiatan, gotong royong, dan
                    program yang telah berjalan di lingkungan Bululand.</p>
            </div>

            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($galleryEvents as $eventItem)
                <article
                    class="group overflow-hidden rounded-4xl border border-stone-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                    <div class="relative aspect-4/3 overflow-hidden">
                        <img src="{{ $eventItem['url'] }}" alt="{{ $eventItem['title'] }}"
                            class="h-full w-full object-cover transition duration-500 group-hover:scale-110">
                        <div
                            class="absolute inset-x-0 bottom-0 bg-linear-to-t from-[#111827]/70 to-transparent p-5 text-white">
                            <p class="font-bold">{{ $eventItem['title'] }}</p>
                        </div>
                    </div>
                    <div class="p-5">
                        <div
                            class="inline-flex items-center gap-2 rounded-full bg-stone-100 px-3 py-2 text-sm font-semibold text-stone-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $eventItem['date'] }}
                        </div>
                    </div>
                </article>
                @endforeach
            </div>
        </section>

        <section id="pengumuman" class="grid gap-8 lg:grid-cols-[0.8fr_1.2fr]">
            <div class="rounded-[2.5rem] bg-[#111827] p-8 text-white shadow-2xl shadow-stone-900/20">
                <span
                    class="inline-flex rounded-full bg-white/10 px-4 py-2 text-sm font-bold text-orange-300">Pengumuman
                    Penting</span>
                <h2 class="mt-5 text-3xl font-bold tracking-tight sm:text-4xl">Informasi terbaru untuk warga</h2>
                <p class="mt-4 text-lg leading-8 text-stone-300">Simak pengumuman terkini seputar kegiatan, jadwal, dan
                    hal-hal penting di lingkungan Bululand.</p>
            </div>

            <div class="space-y-4">
                @foreach ($announcements as $announcement)
                <article class="rounded-4xl border border-stone-200 bg-white p-6 shadow-sm transition hover:shadow-xl">
                    <div class="flex gap-4">
                        <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-red-100 text-red-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
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

        <section class="rounded-4xl border border-stone-200 bg-white p-6 shadow-sm">
            <p class="text-center text-sm font-bold uppercase tracking-[0.25em] text-stone-400">Mitra Pilihan</p>
            <div class="mt-6 grid gap-3 sm:grid-cols-2 md:grid-cols-5">
                <div class="rounded-2xl bg-stone-50 px-5 py-4 text-center font-bold text-stone-500">Karang Taruna</div>
                <div class="rounded-2xl bg-stone-50 px-5 py-4 text-center font-bold text-stone-500">PKK Bululand</div>
                <div class="rounded-2xl bg-stone-50 px-5 py-4 text-center font-bold text-stone-500">RT / RW</div>
                <div class="rounded-2xl bg-stone-50 px-5 py-4 text-center font-bold text-stone-500">UMKM Lokal</div>
                <div class="rounded-2xl bg-stone-50 px-5 py-4 text-center font-bold text-stone-500">Relawan</div>
            </div>
        </section>
    </main>

    <footer class="mt-16 bg-[#111827] text-white">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 md:grid-cols-3 lg:px-8">
            <div>
                <div class="flex items-center gap-3">
                    <span class="grid h-10 w-10 place-items-center rounded-2xl bg-[#FF7A1A] text-white">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 3L20.5 7.8V16.2L12 21L3.5 16.2V7.8L12 3Z" fill="currentColor" />
                        </svg>
                    </span>
                    <span class="text-xl font-bold">Kindora</span>
                </div>
                <p class="mt-4 max-w-sm leading-7 text-stone-400">Platform transparansi kegiatan dan informasi warga
                    Bululand.</p>
            </div>

            <div>
                <p class="font-bold">Navigasi</p>
                <div class="mt-4 space-y-3 text-stone-400">
                    <a href="#ringkasan" class="block transition hover:text-white">Ringkasan Keuangan</a>
                    <a href="#galeri" class="block transition hover:text-white">Galeri Kegiatan</a>
                    <a href="#pengumuman" class="block transition hover:text-white">Pengumuman Penting</a>
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
