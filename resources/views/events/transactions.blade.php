@extends('layouts.app')

@php
$balance = $totalIncome - $totalExpense;
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
            </section>

            {{-- Tabs --}}
            <div x-data="{ activeTab: '{{ request()->has('expense_page') ? 'expense' : 'income' }}' }" class="mb-12">
                {{-- Tab Navigation --}}
                <div class="mb-5 border-b border-neutral-200">
                    <nav class="-mb-px flex gap-4 sm:gap-6" role="tablist">
                        <button @click="activeTab = 'income'"
                            :class="activeTab === 'income' ? 'border-neutral-800 text-neutral-900' : 'border-transparent text-neutral-400 hover:text-neutral-600'"
                            class="inline-flex items-center gap-1.5 border-b-2 px-1 py-2.5 text-sm font-medium transition"
                            role="tab" type="button">
                            Pemasukan
                            <span
                                class="rounded-full bg-neutral-100 px-2 py-0.5 text-[10px] font-medium text-neutral-500">{{
                                $incomeTransactions->total() }}</span>
                        </button>
                        <button @click="activeTab = 'expense'"
                            :class="activeTab === 'expense' ? 'border-neutral-800 text-neutral-900' : 'border-transparent text-neutral-400 hover:text-neutral-600'"
                            class="inline-flex items-center gap-1.5 border-b-2 px-1 py-2.5 text-sm font-medium transition"
                            role="tab" type="button">
                            Pengeluaran
                            <span
                                class="rounded-full bg-neutral-100 px-2 py-0.5 text-[10px] font-medium text-neutral-500">{{
                                $expenseTransactions->total() }}</span>
                        </button>
                    </nav>
                </div>

                {{-- Pemasukan Tab --}}
                <div x-data="{ activeSubTab: '{{ request()->has('search_house') ? 'search' : 'summary' }}' }"
                    x-show="activeTab === 'income'" x-cloak role="tabpanel">

                    {{-- Contribution Summary Card --}}
                    @if ($totalContribution > 0)
                    <div class="mb-5 rounded-xl border border-neutral-200 bg-white p-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="grid h-11 w-11 shrink-0 place-items-center rounded-lg bg-neutral-100 text-neutral-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[10px] font-semibold uppercase tracking-widest text-neutral-400">Total
                                    Iuran Terkumpul</p>
                                <p class="text-xl font-bold truncate">Rp {{ number_format($totalContribution, 0, ',',
                                    '.') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Sub Tab Navigation --}}
                    <div class="mb-5 border-b border-neutral-200">
                        <nav class="-mb-px flex gap-4 sm:gap-6" role="tablist">
                            <button @click="activeSubTab = 'summary'"
                                :class="activeSubTab === 'summary' ? 'border-neutral-800 text-neutral-900' : 'border-transparent text-neutral-400 hover:text-neutral-600'"
                                class="inline-flex items-center gap-1.5 border-b-2 px-1 py-2.5 text-sm font-medium transition"
                                role="tab" type="button">
                                Ringkasan
                            </button>
                            <button @click="activeSubTab = 'search'"
                                :class="activeSubTab === 'search' ? 'border-neutral-800 text-neutral-900' : 'border-transparent text-neutral-400 hover:text-neutral-600'"
                                class="inline-flex items-center gap-1.5 border-b-2 px-1 py-2.5 text-sm font-medium transition"
                                role="tab" type="button">
                                Cek Iuran Rumah
                            </button>
                        </nav>
                    </div>

                    {{-- Summary Sub Tab (no tables, card layout at all sizes) --}}
                    <div x-show="activeSubTab === 'summary'" x-cloak role="tabpanel">
                        <div class="rounded-xl border border-neutral-200">
                            <div class="border-b border-neutral-100 px-4 py-3">
                                <h3 class="text-sm font-bold tracking-tight">Pemasukan Lainnya</h3>
                            </div>

                            {{-- Card list for income (no horizontal scroll) --}}
                            <div class="divide-y divide-neutral-100">
                                @forelse ($incomeTransactions as $transaction)
                                <div class="px-4 py-3.5">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium truncate">{{ $transaction->description }}</p>
                                            <p class="mt-0.5 text-xs text-neutral-400">
                                                {{ $transaction->created_at->format('d M Y') }}
                                                @if ($transaction->house)
                                                · Rumah: {{ $transaction->house->code }}
                                                @endif
                                            </p>
                                            @if ($transaction->category || $transaction->donor_name)
                                            <p class="mt-0.5 text-xs text-neutral-400">
                                                {{ $transaction->category ?? '-' }}
                                                @if ($transaction->donor_name)
                                                · {{ $transaction->donor_name }}
                                                @endif
                                            </p>
                                            @endif
                                        </div>
                                        <p class="shrink-0 text-sm font-semibold text-neutral-800">
                                            Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                                @empty
                                <div class="px-4 py-12 text-center text-sm text-neutral-400">
                                    Belum ada pemasukan.
                                </div>
                                @endforelse
                            </div>

                            @if ($incomeTransactions->hasPages())
                            <div class="border-t border-neutral-200 px-4 py-3">
                                {{ $incomeTransactions->links() }}
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Cek Iuran Sub Tab --}}
                    <div x-show="activeSubTab === 'search'" x-cloak role="tabpanel">
                        {{-- Search Form --}}
                        <div class="mb-5 rounded-xl border border-neutral-200 bg-neutral-50 p-5">
                            <div>
                                <h3 class="text-lg font-bold tracking-tight">Cek Iuran Rumah</h3>
                                <p class="mt-1 text-sm leading-relaxed text-neutral-500">Masukkan kode rumah untuk
                                    mengecek apakah rumah tersebut sudah membayar iuran.</p>
                            </div>
                            <div class="mt-4">
                                <form method="GET" action="{{ route('events.transactions', $event->subdomain) }}"
                                    class="flex gap-2 flex-row">
                                    <div class="relative flex-1">
                                        <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-neutral-400"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                        <input type="text" name="search_house" placeholder="Nama rumah..."
                                            value="{{ $searchHouse }}"
                                            class="h-11 w-full rounded-lg border border-neutral-300 bg-white pl-9 pr-3 text-sm outline-none transition placeholder:text-neutral-400 focus:border-neutral-800">
                                    </div>
                                    <button type="submit"
                                        class="inline-flex h-11 items-center justify-center rounded-lg bg-neutral-800 px-5 text-sm font-medium text-white transition hover:bg-neutral-900 active:scale-[0.98]">
                                        Cari
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Search Error --}}
                        @if ($searchHouse !== '' && isset($houseResult['not_found']))
                        <div class="rounded-lg border border-neutral-300 bg-neutral-50 px-4 py-4 text-neutral-600">
                            <p class="font-semibold">Pencarian tidak ditemukan</p>
                            <p class="mt-1 text-sm">Tidak ada data iuran untuk "<span class="font-medium">{{
                                    $searchHouse
                                    }}</span>". Silakan periksa kembali kode rumah Anda.</p>
                        </div>
                        @endif

                        {{-- Search Result (no tables, card layout) --}}
                        @if ($searchHouse !== '' && !isset($houseResult['not_found']))
                        <div class="rounded-xl border border-neutral-200">
                            <div class="flex items-center justify-between border-b border-neutral-100 px-4 py-3">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Hasil
                                        Pencarian</p>
                                    <h2 class="mt-1 text-lg font-bold">"{{ $houseResult['house_code'] }}"</h2>
                                </div>
                            </div>

                            {{-- Card list for house transactions --}}
                            <div class="divide-y divide-neutral-100">
                                @forelse ($houseResult['transactions'] as $tx)
                                <div class="flex items-center justify-between px-4 py-2.5">
                                    <span class="text-sm font-medium text-neutral-700">
                                        {{ $tx->created_at->format('d M Y') }}
                                        @if ($tx->description)
                                        <span class="text-neutral-400">· {{ $tx->description }}</span>
                                        @endif
                                    </span>
                                    <span class="text-sm font-semibold text-neutral-900">
                                        Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                    </span>
                                </div>
                                @empty
                                <div class="px-4 py-12 text-center text-sm text-neutral-400">
                                    Belum ada transaksi iuran.
                                </div>
                                @endforelse
                            </div>

                            <div class="flex items-center justify-between border-t border-neutral-200 px-4 py-3">
                                <span class="text-xs font-semibold text-neutral-500">Total</span>
                                <span class="text-sm font-bold text-neutral-900">Rp {{
                                    number_format($houseResult['total'], 0,
                                    ',', '.') }}</span>
                            </div>
                            @if ($houseResult['total'] > 0)
                            <div class="border-t border-neutral-200 px-4 py-3 text-center">
                                <span
                                    class="inline-block rounded-full bg-neutral-900 px-3 py-1 text-[11px] font-bold text-white">✓
                                    Lunas</span>
                            </div>
                            @else
                            <div class="border-t border-neutral-200 px-4 py-3 text-center">
                                <span
                                    class="inline-block rounded-full bg-red-600 px-3 py-1 text-[11px] font-bold text-white">Belum
                                    Lunas</span>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Pengeluaran Tab (no tables, card layout) --}}
                <div x-show="activeTab === 'expense'" x-cloak role="tabpanel">
                    <div class="rounded-xl border border-neutral-200">
                        {{-- Card list for expense (no horizontal scroll) --}}
                        <div class="divide-y divide-neutral-100">
                            @forelse ($expenseTransactions as $transaction)
                            <div class="px-4 py-3.5">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium truncate">{{ $transaction->description }}</p>
                                        <p class="mt-0.5 text-xs text-neutral-400">
                                            {{ $transaction->created_at->format('d M Y') }}
                                            @if ($transaction->house)
                                            · Rumah: {{ $transaction->house->code }}
                                            @endif
                                        </p>
                                        @if ($transaction->category || $transaction->donor_name)
                                        <p class="mt-0.5 text-xs text-neutral-400">
                                            {{ $transaction->category ?? '-' }}
                                            @if ($transaction->donor_name)
                                            · {{ $transaction->donor_name }}
                                            @endif
                                        </p>
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

                        @if ($expenseTransactions->hasPages())
                        <div class="border-t border-neutral-200 px-4 py-3">
                            {{ $expenseTransactions->links() }}
                        </div>
                        @endif
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
