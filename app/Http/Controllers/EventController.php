<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function show(Request $request, $event = null)
    {
        $eventSlug = $event;

        $isPaid = $request->query('paid') === '1';

        $searchQuery = $request->query('search', '');

        $dummyHouses = [
            ['name' => 'Rumah A', 'paid_amount' => 500000],
            ['name' => 'Rumah B', 'paid_amount' => 750000],
            ['name' => 'Rumah C', 'paid_amount' => 250000],
            ['name' => 'Rumah D', 'paid_amount' => 1000000],
            ['name' => 'Rumah E', 'paid_amount' => 0],
            ['name' => 'Rumah F', 'paid_amount' => 500000],
        ];

        $contributionResults = [];
        if ($searchQuery !== '') {
            $searchLower = Str::lower($searchQuery);
            foreach ($dummyHouses as $house) {
                if (Str::contains(Str::lower($house['name']), $searchLower)) {
                    $contributionResults[] = $house;
                }
            }

            if (empty($contributionResults)) {
                $contributionResults = ['not_found' => true, 'query' => $searchQuery];
            }
        }

        $totalIncome = 30000000;
        $totalExpense = 22500000;

        $galleryPhotos = [
            ['url' => 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=600&h=400&fit=crop', 'caption' => 'Pemandangan acara'],
            ['url' => 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?w=600&h=400&fit=crop', 'caption' => 'Kegiatan panitia'],
            ['url' => 'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?w=600&h=400&fit=crop', 'caption' => 'Foto bersama'],
            ['url' => 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=600&h=400&fit=crop', 'caption' => 'Acara tahunan'],
        ];

        $galleryEvents = [
            ['url' => 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?w=600&h=400&fit=crop', 'title' => 'Pertemuan Awal 2024', 'date' => '12 Januari 2024'],
            ['url' => 'https://images.unsplash.com/photo-1475721027785-f74eccf877e2?w=600&h=400&fit=crop', 'title' => 'Pelantikan Pengurus', 'date' => '5 Maret 2024'],
            ['url' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=600&h=400&fit=crop', 'title' => 'Rapat Koordinasi', 'date' => '20 April 2024'],
            ['url' => 'https://images.unsplash.com/photo-1559223607-a43c990c692c?w=600&h=400&fit=crop', 'title' => 'Kegiatan Bakti Sosial', 'date' => '15 Mei 2024'],
            ['url' => 'https://images.unsplash.com/photo-1511578314322-379afb476865?w=600&h=400&fit=crop', 'title' => 'Halal Bihalal', 'date' => '2 Juni 2024'],
        ];

        $announcements = [
            ['title' => 'Pembayaran Iuran Bulan Juli', 'date' => '1 Juli 2024', 'content' => 'Mohon untuk melakukan pembayaran iuran bulan Juli sebesar Rp 50.000 sebelum tanggal 10 Juli 2024. Pembayaran dapat dilakukan ke bendahara masing-masing rumah.'],
            ['title' => 'Jadwal Rapat Bulanan', 'date' => '15 Juli 2024', 'content' => 'Rapat bulanan akan dilaksanakan pada hari Minggu, 15 Juli 2024 pukul 09.00 WIB di aula RT 05. Kehadiran seluruh warga sangat diharapkan.'],
            ['title' => 'Pengumuman Pemilihan Ketua RT', 'date' => '22 Juli 2024', 'content' => 'Pemilihan ketua RT baru akan dilaksanakan pada tanggal 22 Juli 2024. Pendaftaran calon dibuka mulai tanggal 1-15 Juli 2024.'],
        ];

        $committees = [
            ['position' => 'Ketua', 'name' => 'Ahmad Hidayat', 'phone' => '0812-3456-7890', 'photo' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=200&h=200&fit=crop&crop=face', 'house' => 'Rumah A'],
            ['position' => 'Wakil Ketua', 'name' => 'Siti Rahayu', 'phone' => '0813-4567-8901', 'photo' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&h=200&fit=crop&crop=face', 'house' => 'Rumah B'],
            ['position' => 'Sekretaris', 'name' => 'Budi Santoso', 'phone' => '0814-5678-9012', 'photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop&crop=face', 'house' => 'Rumah C'],
            ['position' => 'Bendahara', 'name' => 'Dewi Lestari', 'phone' => '0815-6789-0123', 'photo' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=200&h=200&fit=crop&crop=face', 'house' => 'Rumah D'],
            ['position' => 'Kepala Seksi Keamanan', 'name' => 'Eko Prasetyo', 'phone' => '0816-7890-1234', 'photo' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200&h=200&fit=crop&crop=face', 'house' => 'Rumah E'],
            ['position' => 'Kepala Seksi Kebersihan', 'name' => 'Fitri Handayani', 'phone' => '0817-8901-2345', 'photo' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=200&h=200&fit=crop&crop=face', 'house' => 'Rumah F'],
            ['position' => 'Kepala Seksi Kegiatan', 'name' => 'Gunawan Wijaya', 'phone' => '0818-9012-3456', 'photo' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=200&h=200&fit=crop&crop=face', 'house' => 'Rumah G'],
            ['position' => 'Kepala Seksi Keagamaan', 'name' => 'Hasan Basri', 'phone' => '0819-0123-4567', 'photo' => 'https://images.unsplash.com/photo-1463453091185-61582044d556?w=200&h=200&fit=crop&crop=face', 'house' => 'Rumah H'],
        ];

        return view('events.show', [
            'event' => $eventSlug,
            'isPaid' => $isPaid,
            'searchQuery' => $searchQuery,
            'contributionResults' => $contributionResults,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'galleryPhotos' => $galleryPhotos,
            'galleryEvents' => $galleryEvents,
            'announcements' => $announcements,
            'committees' => $committees,
        ]);
    }
}
