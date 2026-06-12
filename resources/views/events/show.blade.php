@extends('layouts.app')

@section('title', 'Event ' . ucfirst($event))

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-4 px-6 shadow-md">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Halaman Event: {{ ucfirst($event) }}</h1>
            <span class="text-sm bg-white/20 px-4 py-1 rounded-full">bululand.web.id</span>
        </div>
    </div>

    @if ($searchQuery !== '' && isset($contributionResults['not_found']))
    <div class="max-w-7xl mx-auto mt-2 px-4">
        <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg">
            <p class="font-medium text-lg">Pencarian tidak ditemukan</p>
            <p class="mt-1">Tidak ada data kontribusi untuk "<span class="font-semibold">{{ $contributionResults['query'] }}</span>". Silakan periksa kembali nama rumah Anda.</p>
        </div>
    </div>
    @endif

    {{-- HERO IMAGE --}}
    <div class="relative w-full h-[400px] bg-gray-800">
        <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?w=1600&h=400&fit=crop" alt="Hero Image" class="w-full h-full object-cover opacity-80">
        <div class="absolute inset-0 bg-gradient-to-b from-blue-900/60 to-blue-900/40"></div>
        <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-4">
            <h2 class="text-4xl md:text-5xl font-bold text-white mb-4">Selamat Datang di Event {{ ucfirst($event) }}</h2>
            <p class="text-xl text-blue-100 mb-2">Kelurahan Bululand</p>
            <p class="text-blue-200">Tahun 2024</p>
        </div>
    </div>

    {{-- SEARCH BAR --}}
    <div class="-mt-8 relative z-10 max-w-2xl mx-auto px-4">
        <div class="bg-white rounded-xl shadow-lg p-2">
            <form method="GET" action="{{ route('events.show', $event) }}" class="flex items-center">
                <div class="flex-1 relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" placeholder="Masukkan nama rumah untuk cek pembayaran..." value="{{ $searchQuery }}" class="w-full pl-12 pr-4 py-3.5 text-gray-700 bg-gray-50 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-blue-500 focus:bg-white transition text-lg">
                </div>
                <button type="submit" class="ml-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3.5 rounded-lg transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Cari
                </button>
            </form>
        </div>
    </div>

    {{-- CONTRIBUTION SEARCH RESULTS --}}
    @if ($searchQuery !== '' && !isset($contributionResults['not_found']))
    <div class="max-w-7xl mx-auto px-4 mt-8">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Hasil Pencarian: "{{ $searchQuery }}"</h3>
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-blue-50">
                        <th class="px-6 py-3 border-b text-gray-700 font-semibold">Nama Rumah</th>
                        <th class="px-6 py-3 border-b text-gray-700 font-semibold text-right">Jumlah Pembayaran</th>
                        <th class="px-6 py-3 border-b text-gray-700 font-semibold text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($contributionResults as $result)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 text-gray-800 font-medium">{{ $result['name'] }}</td>
                        <td class="px-6 py-4 text-gray-700 text-right">Rp {{ number_format($result['paid_amount'], 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-center">
                            @if ($result['paid_amount'] > 0)
                            <span class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium">Sudah Bayar</span>
                            @else
                            <span class="inline-block bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-medium">Belum Bayar</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- MAIN CONTENT --}}
    <main class="max-w-7xl mx-auto px-4 py-12 space-y-16">

        {{-- FINANCIAL SUMMARY --}}
        <section>
            <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center flex items-center justify-center gap-3">
                <span class="w-10 h-1 bg-blue-600 rounded"></span>
                Ringkasan Keuangan
                <span class="w-10 h-1 bg-blue-600 rounded"></span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-green-500">
                    <div class="flex items-center gap-4 mb-3">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-700">Total Pemasukan</h3>
                    </div>
                    <p class="text-3xl font-bold text-green-600">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-6 border-t-4 border-red-500">
                    <div class="flex items-center gap-4 mb-3">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-700">Total Pengeluaran</h3>
                    </div>
                    <p class="text-3xl font-bold text-red-600">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6 mt-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="text-lg font-semibold text-gray-700">Saldo</span>
                </div>
                <p class="text-3xl font-bold {{ ($totalIncome - $totalExpense) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    Rp {{ number_format($totalIncome - $totalExpense, 0, ',', '.') }}
                </p>
            </div>
        </section>

        {{-- GALLERY PHOTO --}}
        <section>
            <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center flex items-center justify-center gap-3">
                <span class="w-10 h-1 bg-blue-600 rounded"></span>
                Galeri Foto
                <span class="w-10 h-1 bg-blue-600 rounded"></span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($galleryPhotos as $photo)
                <div class="group relative bg-white rounded-xl shadow-md overflow-hidden cursor-pointer transform hover:scale-105 transition duration-300">
                    <div class="aspect-[4/3] overflow-hidden">
                        <img src="{{ $photo['url'] }}" alt="{{ $photo['caption'] }}" class="w-full h-full object-cover group-hover:opacity-90 transition">
                    </div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition flex items-end p-4">
                        <p class="text-white font-medium text-sm">{{ $photo['caption'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        {{-- GALLERY EVENT --}}
        <section>
            <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center flex items-center justify-center gap-3">
                <span class="w-10 h-1 bg-blue-600 rounded"></span>
                Galeri Kegiatan
                <span class="w-10 h-1 bg-blue-600 rounded"></span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($galleryEvents as $eventItem)
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition">
                    <div class="aspect-[4/3] overflow-hidden">
                        <img src="{{ $eventItem['url'] }}" alt="{{ $eventItem['title'] }}" class="w-full h-full object-cover hover:scale-110 transition duration-500">
                    </div>
                    <div class="p-5">
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">{{ $eventItem['title'] }}</h3>
                        <div class="flex items-center gap-2 text-gray-500 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>{{ $eventItem['date'] }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        {{-- PENGUMUMAN --}}
        <section>
            <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center flex items-center justify-center gap-3">
                <span class="w-10 h-1 bg-red-500 rounded"></span>
                Pengumuman Penting
                <span class="w-10 h-1 bg-red-500 rounded"></span>
            </h2>
            <div class="space-y-4 max-w-4xl mx-auto">
                @foreach ($announcements as $announcement)
                <div class="bg-white rounded-lg shadow-md border-l-4 border-red-500 p-6 hover:shadow-lg transition">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $announcement['title'] }}</h3>
                            <div class="text-sm text-gray-500 mb-3">{{ $announcement['date'] }}</div>
                            <p class="text-gray-700 leading-relaxed">{{ $announcement['content'] }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        {{-- SUSUNAN PANITIA --}}
        <section>
            <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center flex items-center justify-center gap-3">
                <span class="w-10 h-1 bg-blue-600 rounded"></span>
                Susunan Panitia
                <span class="w-10 h-1 bg-blue-600 rounded"></span>
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($committees as $committee)
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition group">
                    <div class="aspect-square overflow-hidden bg-gray-100">
                        <img src="{{ $committee['photo'] }}" alt="{{ $committee['name'] }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    </div>
                    <div class="p-5 text-center">
                        <h3 class="text-lg font-bold text-gray-800 mb-1">{{ $committee['name'] }}</h3>
                        <p class="text-blue-600 font-medium text-sm mb-2">{{ $committee['position'] }}</p>
                        <p class="text-gray-500 text-sm mb-3">{{ $committee['house'] }}</p>
                        <div class="flex items-center justify-center gap-2 text-gray-600 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span>{{ $committee['phone'] }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>
    </main>

    <footer class="bg-gray-800 text-white py-8 mt-16">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="mb-2">Halaman Event {{ ucfirst($event) }}</p>
            <p class="text-gray-400 text-sm">Kelurahan Bululand</p>
            <p class="text-gray-500 text-xs mt-2">&copy; {{ date('Y') }} Bululand Web. All rights reserved.</p>
        </div>
    </footer>
</div>
@endsection
