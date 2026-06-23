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
        <footer class="border-t border-neutral-200 bg-neutral-50">
            <div class="px-4 py-8 sm:px-6">
                <div class="flex flex-col gap-6 sm:grid sm:grid-cols-3">
                    <div>
                        <div class="flex items-center gap-3">
                            @if ($eventDetail && $eventDetail->logo)
                            <img src="{{ $eventDetail->logo }}" alt="{{ $event->name }} logo"
                                class="h-8 w-8 rounded-full">
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
                        <p class="mt-3 text-sm leading-relaxed text-neutral-500">{!! $eventDetail->footer_text !!}</p>
                        @endif
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Navigasi</p>
                        <div class="mt-3 space-y-2 text-sm text-neutral-500">
                            <a href="#keuangan" class="block transition hover:text-neutral-800">Keuangan</a>
                            <a href="#iuran" class="block transition hover:text-neutral-800">Iuran</a>
                            @if ($posts->count() > 0)
                            <a href="#post" class="block transition hover:text-neutral-800">Post</a>
                            @endif
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-neutral-400">Kontak</p>
                        <div class="mt-3 space-y-2 text-sm text-neutral-500">
                            @if ($eventDetail && $eventDetail->contacts)
                            @foreach ($eventDetail->contacts as $contact)
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
</div>
@endsection