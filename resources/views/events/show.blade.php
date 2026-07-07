@extends('layouts.app')

@section('title', 'Event ' . $event->name)

@section('favicon')
@if ($eventDetail && $eventDetail->favicon)
<link rel="icon" href="{{ $eventDetail->favicon }}">
@endif
@endsection

@section('content')
@php
$balance = $totalIncome - $totalExpense;
@endphp

{{-- Desktop outer wrapper with gray sides --}}
<div class="min-h-screen bg-neutral-100">
    {{-- Mobile-width centered container --}}
    <div class="mx-auto min-h-screen sm:max-w-md bg-white shadow-xl">

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

        <main class="px-4 py-4 pb-28 sm:px-6 sm:pb-0">

            {{-- Hero Section --}}
            @if ($eventDetail && ($eventDetail->hero_image || $eventDetail->hero_title || $eventDetail->hero_subtitle))
            <section class="mb-8">
                @if ($eventDetail->hero_image)
                <div class="-mx-4 overflow-hidden sm:mx-0 sm:rounded-xl">
                    <img src="{{ $eventDetail->hero_image }}" alt="{{ $event->name }}" class="w-full">
                </div>
                @endif
                @if ($eventDetail->hero_title || $eventDetail->hero_subtitle)
                <div class="mt-4">
                    @if ($eventDetail->hero_title)
                    <h1 class="text-2xl font-bold tracking-tight sm:text-3xl">{{ $eventDetail->hero_title }}
                    </h1>
                    @endif
                    @if ($eventDetail->hero_subtitle)
                    <p class="mt-2 text-sm leading-relaxed text-neutral-500">{{ $eventDetail->hero_subtitle }}</p>
                    @endif
                </div>
                @endif
            </section>
            @endif

            {{-- Search Error --}}
            @if ($searchQuery !== '' && isset($contributionResults['not_found']))
            <section class="mb-8">
                <div class="rounded-lg border border-neutral-300 bg-neutral-50 px-4 py-4 text-neutral-600">
                    <p class="font-semibold">Pencarian tidak ditemukan</p>
                    <p class="mt-1 text-sm">Tidak ada data iuran untuk "<span class="font-medium">{{
                            $contributionResults['query'] }}</span>". Silakan periksa kembali nama rumah Anda.</p>
                </div>
            </section>
            @endif

            {{-- Search Results --}}
            @if ($searchQuery !== '' && !isset($contributionResults['not_found']))
            <section class="mb-8">
                <div class="rounded-xl border border-neutral-200">
                    <div class="flex items-center justify-between border-b border-neutral-100 px-4 py-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Hasil Pencarian
                            </p>
                            <h2 class="mt-1 text-lg font-bold">"{{ $searchQuery }}"</h2>
                        </div>
                        <span class="rounded-full bg-neutral-100 px-3 py-1 text-xs font-medium text-neutral-500">{{
                            count($contributionResults) }} data</span>
                    </div>

                    {{-- Card list (works at all screen sizes, no horizontal scroll) --}}
                    <div class="divide-y divide-neutral-100">
                        @foreach ($contributionResults as $result)
                        <div class="px-4 py-3">
                            <div class="mb-2">
                                <span class="block text-sm font-bold text-neutral-900">{{ $result['name'] }}</span>
                            </div>
                            @if (count($result['transactions']) > 0)
                            <div class="space-y-1.5">
                                @foreach ($result['transactions'] as $tx)
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-medium text-neutral-700">{{ $tx['date'] }}@if ($tx['description'])
                                        · {{ $tx['description']
                                        }}@endif</span>
                                    <span class="font-semibold text-neutral-900">Rp {{ number_format($tx['amount'], 0,
                                        ',', '.') }}</span>
                                </div>
                                @endforeach
                            </div>
                            @endif
                            <div class="mt-2 flex items-center justify-between border-t border-neutral-200 pt-2">
                                <span class="text-xs font-semibold text-neutral-500">Total</span>
                                <span class="text-sm font-bold text-neutral-900">Rp {{
                                    number_format($result['paid_amount'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </section>
            @endif

            {{-- Cek Iuran Section --}}
            <section id="iuran" class="mb-8">
                <div class="rounded-xl border border-neutral-200 bg-neutral-50 p-5 sm:p-6">
                    <div>
                        <span class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Cek Iuran</span>
                        <h2 class="mt-2 text-xl font-bold tracking-tight">Cek status iuran</h2>
                        <p class="mt-1.5 text-sm leading-relaxed text-neutral-500">Masukkan nama rumah untuk melihat
                            status pembayaran iuran.</p>
                    </div>
                    <div class="mt-4" x-data="checkIuran()">
                        <form @submit.prevent="submitSearch" class="flex gap-2">
                            <div class="relative flex-1">
                                <svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-neutral-400"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input type="text" x-model="searchQuery" placeholder="Nama rumah..."
                                    class="h-11 w-full rounded-lg border border-neutral-300 bg-white pl-9 pr-3 text-sm outline-none transition placeholder:text-neutral-400 focus:border-neutral-800"
                                    :disabled="loading">
                            </div>
                            <button type="submit" :disabled="loading"
                                class="inline-flex h-11 items-center justify-center rounded-lg bg-neutral-800 px-5 text-sm font-medium text-white transition hover:bg-neutral-900 active:scale-[0.98] disabled:opacity-50">
                                <span x-show="loading">
                                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"
                                        aria-hidden="true">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4" />
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                    </svg>
                                </span>
                                <span x-show="!loading">Cari</span>
                            </button>
                        </form>

                        {{-- Result Modal --}}
                        <div x-show="showModal" x-cloak
                            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
                            @click.self="closeModal">
                            <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-bold">Hasil Pencarian</h3>
                                    <button @click="closeModal" class="text-neutral-400 hover:text-neutral-600">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="mt-4">
                                    <template x-if="result.error">
                                        <div
                                            class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                                            <p x-text="result.error"></p>
                                        </div>
                                    </template>

                                    <template x-if="result.found === false">
                                        <div
                                            class="rounded-lg border border-neutral-300 bg-neutral-50 px-4 py-4 text-neutral-600">
                                            <p class="font-semibold">Pencarian tidak ditemukan</p>
                                            <p class="mt-1 text-sm" x-text="result.message"></p>
                                        </div>
                                    </template>

                                    <template x-if="result.found === true">
                                        <div>
                                            <div class="mb-3">
                                                <span
                                                    class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Rumah</span>
                                                <p class="text-xl font-bold" x-text="result.house_code"></p>
                                            </div>

                                            <div class="rounded-lg border border-neutral-100 bg-neutral-50 p-4">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-sm text-neutral-600">Total Iuran Dibayar</span>
                                                    <span class="text-lg font-bold"
                                                        x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(result.total_paid)"></span>
                                                </div>
                                                <div class="mt-2 flex items-center justify-between">
                                                    <span class="text-sm text-neutral-600">Iuran Wajib</span>
                                                    <span class="text-sm font-semibold"
                                                        x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(result.contribution_fee)"></span>
                                                </div>
                                            </div>

                                            <template x-if="result.transactions.length > 0">
                                                <div class="mt-4 rounded-lg border border-neutral-200 bg-white p-4">
                                                    <p class="mb-2 text-xs font-bold text-neutral-700">Riwayat
                                                        Pembayaran</p>
                                                    <div class="space-y-1.5">
                                                        <template x-for="(tx, index) in result.transactions"
                                                            :key="index">
                                                            <div class="flex items-center justify-between text-xs">
                                                                <span class="text-neutral-700" x-text="tx.date"></span>
                                                                <span class="font-semibold text-neutral-900"
                                                                    x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(tx.amount)"></span>
                                                            </div>
                                                        </template>
                                                    </div>
                                                    <div
                                                        class="mt-2 flex items-center justify-between border-t border-neutral-200 pt-2">
                                                        <span
                                                            class="text-xs font-semibold text-neutral-500">Total</span>
                                                        <span class="text-sm font-bold text-neutral-900"
                                                            x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(result.total_paid)"></span>
                                                    </div>
                                                </div>
                                            </template>

                                            <template x-if="result.is_paid">
                                                <div class="mt-3 rounded-lg bg-neutral-900 px-4 py-2.5 text-center">
                                                    <span class="text-xs font-bold text-white/90">✓ Lunas</span>
                                                </div>
                                            </template>

                                            <template x-if="!result.is_paid">
                                                <div class="mt-3 rounded-lg bg-red-600 px-4 py-2.5 text-center">
                                                    <span class="text-xs font-bold text-white">Belum Lunas</span>
                                                </div>
                                            </template>

                                            <template x-if="result.transactions.length === 0 && !result.is_paid">
                                                <div
                                                    class="mt-4 rounded-lg border border-neutral-300 bg-neutral-50 px-4 py-3 text-center text-neutral-600">
                                                    <p class="text-sm font-semibold">Belum ada transaksi</p>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function checkIuran() {
                        return {
                            searchQuery: '',
                            loading: false,
                            showModal: false,
                            result: {},
                            async submitSearch() {
                                if (!this.searchQuery.trim()) return;
                                this.loading = true;
                                this.result = {};
                                try {
                                    const response = await fetch('{{ route('events.check-contribution', $event->subdomain) }}?search=' + encodeURIComponent(this.searchQuery));
                                    this.result = await response.json();
                                    this.showModal = true;
                                } catch (e) {
                                    this.result = { error: 'Terjadi kesalahan. Silakan coba lagi.' };
                                    this.showModal = true;
                                } finally {
                                    this.loading = false;
                                }
                            },
                            closeModal() {
                                this.showModal = false;
                                this.result = {};
                            }
                        };
                    }
                </script>
            </section>

            {{-- Keuangan Section --}}
            <section id="keuangan" class="mb-12">
                <div class="mb-5">
                    <div class="flex items-end justify-between">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Transparansi
                                Keuangan</span>
                            <h1 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">Ringkasan
                                Keuangan</h1>
                            @if ($eventDetail && $eventDetail->hero_subtitle)
                            <p class="mt-2 max-w-xl text-sm leading-relaxed text-neutral-500">{{
                                $eventDetail->hero_subtitle
                                }}</p>
                            @endif
                        </div>
                        <a href="{{ route('events.transactions', $event->subdomain) }}"
                            class="inline-flex h-9 shrink-0 items-center gap-1 rounded-lg border border-neutral-300 px-3.5 text-xs font-medium text-neutral-600 transition hover:bg-neutral-100 active:scale-[0.98]">
                            Lihat
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Single compact overview card --}}
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

            {{-- Post Section --}}
            @php
            $posts = \App\Models\Post::where('event_id', $event->id)
            ->where(function ($q) {
            $q->whereNull('published_at')
            ->orWhere('published_at', '<=', now()); }) ->orderBy('created_at', 'desc')
                ->get();
                @endphp
                @if ($posts->count() > 0)
                <section id="post" class="mb-12">
                    <div class="border-t border-neutral-200 pt-6">
                        <h2 class="text-xl font-bold tracking-tight">Post</h2>
                        <div class="mt-4 space-y-4">
                            @foreach ($posts as $post)
                            <div class="rounded-xl border border-neutral-200 bg-white overflow-hidden">
                                @if ($post->thumbnail)
                                <img src="{{ $post->thumbnail }}" alt="{{ $post->title }}" class="w-full object-cover">
                                @endif
                                <div class="p-4">
                                    <h3 class="font-bold text-neutral-900">{{ $post->title }}</h3>
                                    @if ($post->published_at)
                                    <p class="mt-1 text-xs text-neutral-400">{{ $post->published_at->format('d M Y') }}
                                    </p>
                                    @endif
                                    <div class="mt-2 text-sm leading-relaxed text-neutral-500">
                                        {!! nl2br(e($post->content)) !!}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </section>
                @endif
        </main>

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

        {{-- Footer --}}
        <footer class="border-t border-neutral-200 bg-white">
            <div class="px-4 py-8 sm:px-6">

                {{-- Brand & Description --}}
                <div class="mb-6 text-center">
                    <a href="{{ route('events.show', $event->subdomain) }}" class="inline-flex items-center gap-2">
                        @if ($eventDetail && $eventDetail->logo)
                        <img src="{{ $eventDetail->logo }}" alt="{{ $event->name }} logo" class="h-7 w-7 rounded-full">
                        @else
                        <span class="grid h-7 w-7 place-items-center rounded-full bg-neutral-800 text-white">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 3L20.5 7.8V16.2L12 21L3.5 16.2V7.8L12 3Z" fill="currentColor" />
                            </svg>
                        </span>
                        @endif
                        <span class="text-sm font-semibold tracking-tight text-neutral-800">{{ $event->name }}</span>
                    </a>
                </div>

                {{-- Contact & Social --}}
                <div class="mb-6 space-y-4 text-center text-sm text-neutral-500">

                    @if ($eventDetail && $eventDetail->contacts)
                    <div class="flex flex-wrap justify-center gap-x-6 gap-y-1">
                        @foreach ($eventDetail->contacts as $contact)
                        @if ($contact['name'] ?? $contact['phone'])
                        <p>
                            <span class="font-medium text-neutral-700">{{ $contact['name'] ?? '' }}:</span>
                            {{ $contact['phone'] ?? '' }}
                        </p>
                        @endif
                        @endforeach
                    </div>
                    @elseif ($eventDetail && ($eventDetail->contact_name || $eventDetail->contact_phone))
                    <p>{{ $eventDetail->contact_name }}: {{ $eventDetail->contact_phone }}</p>
                    @endif

                    {{-- Social Media Icons --}}
                    @if (($eventDetail && $eventDetail->facebook_url) || ($eventDetail && $eventDetail->instagram_url))
                    <div class="flex items-center justify-center gap-3 pt-1">
                        @if ($eventDetail && $eventDetail->facebook_url)
                        <a href="{{ $eventDetail->facebook_url }}" target="_blank" rel="noopener noreferrer"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-neutral-100 text-neutral-500 transition hover:bg-neutral-800 hover:text-white active:scale-90"
                            aria-label="Facebook">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" />
                            </svg>
                        </a>
                        @endif
                        @if ($eventDetail && $eventDetail->instagram_url)
                        <a href="{{ $eventDetail->instagram_url }}" target="_blank" rel="noopener noreferrer"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-neutral-100 text-neutral-500 transition hover:bg-neutral-800 hover:text-white active:scale-90"
                            aria-label="Instagram">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path
                                    d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 016.11 2.525c.636-.247 1.363-.416 2.427-.465C8.88 2.013 9.235 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" />
                            </svg>
                        </a>
                        @endif
                    </div>
                    @endif
                </div>

                {{-- Footer Text / Copyright --}}
                @if ($eventDetail && $eventDetail->footer_text)
                <hr class="mb-5 border-neutral-100" />
                <div class="text-center text-xs leading-relaxed text-neutral-400">
                    {!! $eventDetail->footer_text !!}
                </div>
                @endif

                {{-- Powered by --}}
                <div class="mt-6 text-center">
                    <p class="text-[11px] text-neutral-300">
                        Powered by <a href="https://bululand.com" target="_blank" rel="noopener noreferrer"
                            class="font-medium text-neutral-400 transition hover:text-neutral-600">Bululand</a>
                    </p>
                </div>
            </div>
        </footer>
    </div>
</div>
@endsection