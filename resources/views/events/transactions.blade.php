@extends('layouts.app')

@php
$balance = $totalIncome - $totalExpense;
$realExpense = $totalExpense + $totalItemDonation;
$realBalance = $totalIncome - $realExpense;
$categoryLabels = [
    'donation' => 'Donasi',
    'contribution' => 'Iuran Wajib',
    'sponsorship' => 'Sponsorship',
    'ticket_sales' => 'Penjualan Tiket',
    'merchandise' => 'Merchandise',
    'consumption' => 'Konsumsi',
    'administration' => 'Administrasi',
    'decoration' => 'Dekorasi',
    'documentation' => 'Dokumentasi',
    'transport' => 'Transportasi',
    'venue_rental' => 'Sewa Tempat',
    'sound_system' => 'Sound System',
    'printing' => 'Percetakan',
    'others' => 'Lainnya',
];
@endphp

@section('title', 'Daftar Transaksi - ' . $event->name)

@section('content')
{{-- Desktop outer wrapper with gray sides --}}
<div class="min-h-screen bg-white lg:bg-neutral-100">
    {{-- Mobile-width centered container --}}
    <div class="mx-auto min-h-screen bg-white lg:max-w-md lg:shadow-xl">

        {{-- Header --}}
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
                <div class="flex items-center gap-2">
                    <a href="{{ route('events.print', $event->subdomain) }}" target="_blank"
                        class="inline-flex h-9 items-center rounded-lg border border-neutral-300 px-3.5 text-xs font-medium text-neutral-600 transition hover:bg-neutral-100 active:scale-[0.98]">
                        <svg class="mr-1.5 h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
                        </svg>
                        Cetak PDF
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
            </div>
        </header>

        <main class="px-4 py-6 pb-28 sm:px-6 sm:pb-0 lg:px-4 lg:py-8">
            {{-- Title --}}
            <div class="mb-6">
                <span class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Transparansi
                    Keuangan</span>
                <h1 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl lg:text-2xl">Daftar Transaksi</h1>
                <p class="mt-2 max-w-xl text-sm leading-relaxed text-neutral-500">Berikut adalah seluruh catatan
                    pemasukan
                    dan pengeluaran keuangan {{ $event->name }}.</p>
            </div>

            {{-- Single compact overview card --}}
            <section class="mb-8">
                <div class="rounded-xl border border-neutral-200 bg-white">
                    <div class="grid grid-cols-3 divide-x divide-neutral-100">
                        <div class="px-3 py-4 text-center">
                            <span class="text-[10px] font-medium text-neutral-400">Pemasukan</span>
                            <p class="mt-0.5 text-sm font-bold text-neutral-900">Rp {{ number_format($totalIncome, 0,
                                ',', '.') }}</p>
                        </div>
                        <div class="px-3 py-4 text-center">
                            <span class="text-[10px] font-medium text-neutral-400">Pengeluaran</span>
                            <p class="mt-0.5 text-sm font-bold text-neutral-900">Rp {{ number_format($totalExpense, 0,
                                ',', '.') }}</p>
                        </div>
                        <div class="px-3 py-4 text-center">
                            <span class="text-[10px] font-medium text-neutral-400">Saldo</span>
                            <p
                                class="mt-0.5 text-sm font-bold {{ $balance >= 0 ? 'text-neutral-900' : 'text-neutral-500' }}">
                                Rp {{ number_format($balance, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Overview card (angka real, termasuk donasi barang) --}}
                <div class="mt-3 rounded-xl border border-neutral-200 bg-white">
                    <div class="grid grid-cols-3 divide-x divide-neutral-100">
                        <div class="px-3 py-4 text-center">
                            <span class="text-[10px] font-medium text-neutral-400">Pemasukan</span>
                            <p class="mt-0.5 text-sm font-bold text-neutral-900">Rp {{ number_format($totalIncome, 0,
                                ',', '.') }}</p>
                        </div>
                        <div class="px-3 py-4 text-center">
                            <span class="text-[10px] font-medium text-neutral-400">Pengeluaran Asli</span>
                            <p class="mt-0.5 text-sm font-bold text-neutral-900">Rp {{ number_format($realExpense, 0,
                                ',', '.') }}</p>
                        </div>
                        <div class="px-3 py-4 text-center">
                            <span class="text-[10px] font-medium text-neutral-400">Saldo Asli</span>
                            <p
                                class="mt-0.5 text-sm font-bold {{ $realBalance >= 0 ? 'text-neutral-900' : 'text-neutral-500' }}">
                                Rp {{ number_format($realBalance, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Tabs --}}
            <div x-data="{ activeTab: 'recap' }" class="mb-12">
                {{-- Tab Navigation --}}
                <div class="mb-5 -mx-4 flex gap-2 overflow-x-auto px-4 pb-1 sm:mx-0 sm:px-0" style="scrollbar-width: none;">
                    @foreach ([
                        'recap' => 'Rekap per Rumah (' . $houseRecap->count() . ')',
                        'iuran' => 'Iuran Wajib (' . $iuranTransactions->count() . ')',
                        'donasi_uang' => 'Donasi Uang (' . $donasiTransactions->count() . ')',
                        'barang' => 'Donasi Barang (' . $itemDonations->count() . ')',
                        'expense' => 'Pengeluaran (' . $expenseTransactions->count() . ')',
                    ] as $tabValue => $tabLabel)
                    <button type="button" @click="activeTab = '{{ $tabValue }}'"
                        :class="activeTab === '{{ $tabValue }}' ? 'bg-neutral-800 text-white border-neutral-800' : 'bg-white text-neutral-600 border-neutral-300 hover:bg-neutral-100'"
                        class="h-9 shrink-0 whitespace-nowrap rounded-full border px-3.5 text-xs font-medium transition active:scale-[0.98]">
                        {{ $tabLabel }}
                    </button>
                    @endforeach
                </div>

                {{-- Rekap Sumbangan Per Rumah Tab --}}
                <div x-data="{ search: '' }" x-show="activeTab === 'recap'" x-cloak role="tabpanel">

                    {{-- Realtime filter input --}}
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
                            <h3 class="text-sm font-bold tracking-tight">Total Sumbangan per Rumah</h3>
                            <p class="mt-0.5 text-xs text-neutral-400">Iuran wajib + donasi uang + donasi barang bernominal</p>
                        </div>

                        <div class="divide-y divide-neutral-100">
                            @forelse ($houseRecap as $item)
                            @php $label = $item->house->display_name ?? '-'; @endphp
                            <div x-data="{ label: @js($label) }"
                                x-show="!search || label.toLowerCase().includes(search.toLowerCase())"
                                class="flex items-center justify-between px-4 py-3.5">
                                <p class="text-sm font-medium text-neutral-700">
                                    {{ $label }}
                                </p>
                                <p class="shrink-0 text-sm font-semibold text-neutral-800">
                                    Rp {{ number_format($item->grand_total, 0, ',', '.') }}
                                </p>
                            </div>
                            @empty
                            <div class="px-4 py-12 text-center text-sm text-neutral-400">
                                Belum ada sumbangan.
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Iuran Wajib Tab --}}
                <div x-data="{ search: '' }" x-show="activeTab === 'iuran'" x-cloak role="tabpanel">

                    {{-- Realtime filter input --}}
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

                    {{-- Grouped iuran list with realtime filter --}}
                    <div class="rounded-xl border border-neutral-200">
                        <div class="border-b border-neutral-100 px-4 py-3">
                            <h3 class="text-sm font-bold tracking-tight">Iuran Wajib per Rumah</h3>
                        </div>

                        <div class="divide-y divide-neutral-100">
                            @forelse ($iuranByHouse as $item)
                            @php
                            $label = $item->is_anonymous
                                ? 'Orang Baik ' . ($anonymousNumbers[$item->first_id] ?? '')
                                : ($item->house->display_name ?? $item->donor_name ?? '-');
                            @endphp
                            <div x-data="{ label: @js($label) }"
                                x-show="!search || label.toLowerCase().includes(search.toLowerCase())"
                                class="flex items-center justify-between px-4 py-3.5">
                                <p class="text-sm font-medium text-neutral-700">
                                    {{ $label }}
                                </p>
                                <p class="shrink-0 text-sm font-semibold text-neutral-800">
                                    Rp {{ number_format($item->total_amount, 0, ',', '.') }}
                                </p>
                            </div>
                            @empty
                            <div class="px-4 py-12 text-center text-sm text-neutral-400">
                                Belum ada iuran wajib.
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Donasi Uang Tab --}}
                <div x-show="activeTab === 'donasi_uang'" x-cloak role="tabpanel">
                    <div class="rounded-xl border border-neutral-200">
                        <div class="divide-y divide-neutral-100">
                            @forelse ($donasiTransactions as $transaction)
                            <div class="px-4 py-3.5">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-bold truncate">
                                            {{ $transaction->is_anonymous
                                                ? 'Orang Baik ' . ($anonymousNumbers[$transaction->id] ?? '')
                                                : ($transaction->house->display_name ?? $transaction->donor_name ?? '-') }}
                                        </p>
                                    </div>
                                    <p class="shrink-0 text-sm font-semibold text-neutral-800">
                                        Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                            @empty
                            <div class="px-4 py-12 text-center text-sm text-neutral-400">
                                Belum ada donasi uang.
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Pengeluaran Tab (no tables, card layout) --}}
                <div x-show="activeTab === 'expense'" x-cloak role="tabpanel">
                    <div class="rounded-xl border border-neutral-200">
                        {{-- Card list for expense (no horizontal scroll) --}}
                        <div class="divide-y divide-neutral-100">
                            @forelse ($expenseTransactions as $transaction)
                            @php
                            $donorLabel = $transaction->is_anonymous
                                ? 'Orang Baik ' . ($anonymousNumbers[$transaction->id] ?? '')
                                : ($transaction->donor_name ?? $transaction->house->display_name ?? '-');
                            @endphp
                            <div class="px-4 py-3.5">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-bold truncate">{{ $donorLabel }}</p>
                                        @if ($transaction->description)
                                        <p class="mt-0.5 text-sm text-neutral-600">{{ $transaction->description }}</p>
                                        @endif
                                    </div>
                                    <p class="shrink-0 text-sm font-semibold text-neutral-800">
                                        Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                            @empty
                            <div class="px-4 py-12 text-center text-sm text-neutral-400">
                                Belum ada pengeluaran.
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Barang Tab (item/goods donations) --}}
                <div x-show="activeTab === 'barang'" x-cloak role="tabpanel">
                    <div class="rounded-xl border border-neutral-200">
                        <div class="divide-y divide-neutral-100">
                            @forelse ($itemDonations as $donation)
                            @php
                            $donorLabel = $donation->is_anonymous
                                ? 'Orang Baik ' . ($anonymousItemNumbers[$donation->id] ?? '')
                                : ($donation->house->display_name ?? $donation->donor_name ?? '-');
                            @endphp
                            <div class="px-4 py-3.5">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-bold truncate">{{ $donation->item_name }}</p>
                                        <p class="mt-0.5 text-sm text-neutral-600 truncate">{{ $donorLabel }}</p>
                                        @if ($donation->description)
                                        <p class="mt-0.5 text-xs text-neutral-400">
                                            {{ $donation->description }}
                                        </p>
                                        @endif
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <p class="text-sm font-semibold text-neutral-800">
                                            {{ $donation->quantity }} {{ $donation->unit }}
                                        </p>
                                        @if ($donation->price)
                                        <p class="mt-0.5 text-xs text-neutral-400">
                                            Rp {{ number_format($donation->price, 0, ',', '.') }}
                                        </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="px-4 py-12 text-center text-sm text-neutral-400">
                                Belum ada sumbangan barang.
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

{{-- Scroll to Top Button --}}
<button id="scroll-to-top" onclick="window.scrollTo({ top: 0, behavior: 'smooth' })"
    class="fixed bottom-4 right-4 z-50 grid h-12 w-12 place-items-center rounded-full bg-neutral-800 text-white shadow-lg transition hover:bg-neutral-700 active:scale-90 md:bottom-4 md:right-8 lg:right-4"
    style="display: none;">
    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
    </svg>
</button>

<script>
    (function() {
        var btn = document.getElementById('scroll-to-top');
        if (!btn) return;
        var threshold = 400;
        function toggle() {
            if (window.scrollY > threshold) {
                btn.style.display = '';
            } else {
                btn.style.display = 'none';
            }
        }
        toggle();
        window.addEventListener('scroll', toggle, { passive: true });
    })();
</script>

{{-- Alpine.js for tabs --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection