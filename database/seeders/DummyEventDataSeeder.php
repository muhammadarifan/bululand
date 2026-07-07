<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventDetail;
use App\Models\EventMoneyTransaction;
use App\Models\House;
use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyEventDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // ─── 1. Houses: A1-A10, B1-B10, ... I1-I10 ───
            $houseCodes = [];
            foreach (range('A', 'I') as $letter) {
                for ($i = 1; $i <= 10; $i++) {
                    $houseCodes[] = $letter.$i;
                }
            }

            $houses = [];
            foreach ($houseCodes as $code) {
                $houses[$code] = House::firstOrCreate(['code' => $code]);
            }

            // ─── 2. Event Dummy (id=2) ───
            $event = Event::updateOrCreate(
                ['id' => 1],
                [
                    'name' => 'Dummy',
                    'subdomain' => 'dummy',
                    'is_active' => true,
                    'active_until' => now()->addYear(),
                ]
            );

            // ─── 3. Event Detail ───
            EventDetail::updateOrCreate(
                ['event_id' => $event->id],
                [
                    'contribution_fee' => 50000,
                    'logo' => 'https://placehold.co/200x200?text=Logo',
                    'favicon' => 'https://placehold.co/32x32?text=F',
                    'hero_image' => 'https://placehold.co/1920x1080?text=Hero+Dummy',
                    'hero_title' => 'Selamat Datang di Event Dummy',
                    'hero_subtitle' => 'Event ini dibuat untuk keperluan testing dan demonstrasi fitur.',
                    'about_title' => 'Tentang Event Dummy',
                    'about_content' => 'Event Dummy adalah event percobaan yang dibuat untuk mengisi data secara acak pada seluruh tabel yang berelasi. Data yang ditampilkan bersifat simulasi dan tidak merepresentasikan data nyata.',
                    'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'contacts' => [
                        ['name' => 'Panitia Dummy', 'phone' => '081234567890'],
                        ['name' => 'Sekretaris', 'phone' => '081298765432'],
                    ],
                    'facebook_url' => 'https://facebook.com/dummyevent',
                    'instagram_url' => 'https://instagram.com/dummyevent',
                    'footer_text' => '© 2026 Event Dummy. All rights reserved.',
                ]
            );

            // ─── 6. Event Money Transactions ───
            $amounts = [50000, 100000, 150000, 200000, 250000, 300000, 500000, 750000, 1000000];
            $houseValues = array_values($houses);
            $donorNames = ['Donatur Lepas', 'Yayasan Peduli', 'Komunitas Sahabat', 'Dermawan Nusantara', 'Corporation Pulse', 'Warga Dermawan', 'Sponsor Inspiratif'];
            $txDescriptions = [
                'Pemasukan dari iuran warga',
                'Sponsor dari PT Maju Jaya',
                'Donasi dari donatur',
                'Penjualan tiket masuk',
                'Pembayaran pajak acara',
                'Biaya operasional harian',
                'Dana darurat untuk perbaikan',
                'Setoran dari penjual makanan',
                'Biaya sewa lapangan',
                'Pengembalian dana',
            ];

            $inCategories = ['donation', 'contribution', 'sponsorship', 'ticket_sales', 'merchandise', 'others'];
            $outCategories = ['consumption', 'administration', 'decoration', 'documentation', 'transport', 'venue_rental', 'sound_system', 'printing', 'others'];

            for ($i = 0; $i < 40; $i++) {
                $type = rand(0, 1) ? 'in' : 'out';
                $category = $type === 'in'
                    ? $inCategories[array_rand($inCategories)]
                    : $outCategories[array_rand($outCategories)];

                // Rule: if category is 'contribution', house_id must be filled
                // Rule: if house_id is null, donor_name must be filled
                $includeHouse = ($category === 'contribution') ? true : (bool) rand(0, 1);
                $includeName = $includeHouse ? (bool) rand(0, 1) : true;

                EventMoneyTransaction::create([
                    'event_id' => $event->id,
                    'house_id' => $includeHouse ? $houseValues[array_rand($houseValues)]->id : null,
                    'donor_name' => $includeName ? $donorNames[array_rand($donorNames)] : null,
                    'description' => $txDescriptions[array_rand($txDescriptions)],
                    'type' => $type,
                    'category' => $category,
                    'amount' => $amounts[array_rand($amounts)],
                    'attachment' => rand(0, 1) ? 'transactions/bukti_'.($i + 1).'.jpg' : null,
                ]);
            }

            // ─── 7. Posts ───
            $postTypes = ['announcement', 'news', 'update', 'article'];
            $postsData = [
                ['title' => 'Pengumuman Pembukaan Event Dummy', 'content' => 'Kami dengan bangga mengumumkan bahwa event Dummy akan segera dimulai. Silakan hadir tepat waktu.'],
                ['title' => 'Jadwal Acara Lengkap', 'content' => 'Berikut adalah jadwal lengkap acara event Dummy: Pembukaan pada pukul 08.00, lomba pada pukul 10.00, dan penutupan pada pukul 17.00.'],
                ['title' => 'Hasil Lomba Hari Pertama', 'content' => 'Selamat kepada para pemenang lomba hari pertama! Berikut adalah daftar lengkap pemenang dari setiap kategori lomba.'],
                ['title' => 'Update Pendaftaran Terbaru', 'content' => 'Jumlah peserta yang telah mendaftar mencapai 500 orang. Pendaftaran masih dibuka hingga akhir minggu ini.'],
                ['title' => 'Galeri Foto Opening Ceremony', 'content' => 'Berikut kami sajikan galeri foto dari acara opening ceremony event Dummy yang telah berlangsung dengan meriah.'],
                ['title' => 'Info Sponsor dan Donatur', 'content' => 'Terima kasih kepada seluruh sponsor dan donatur yang telah mendukung event Dummy ini.'],
                ['title' => 'Tips dan Trik Ikut Lomba', 'content' => 'Bagi yang akan mengikuti lomba, berikut beberapa tips yang bisa membantu kalian meraih kemenangan.'],
                ['title' => 'Pengumuman Pemenang Grand Prize', 'content' => 'Pemenang grand prize event Dummy telah ditentukan. Silakan cek daftar pemenang di pengumuman resmi.'],
                ['title' => 'Dokumentasi Acara Malam', 'content' => 'Acara malam kemarin berlangsung sangat meriah. Berikut adalah dokumentasi lengkapnya.'],
                ['title' => 'Terima Kasih untuk Semua Pihak', 'content' => 'Event Dummy telah berhasil diselenggarakan berkat kerja sama dan dukungan dari semua pihak. Terima kasih!'],
            ];

            foreach ($postsData as $idx => $post) {
                Post::create([
                    'event_id' => $event->id,
                    'title' => $post['title'],
                    'content' => $post['content'],
                    'type' => $postTypes[array_rand($postTypes)],
                    'published_at' => now()->subDays(rand(0, 30)),
                    'thumbnail' => rand(0, 1) ? 'posts/thumbnail_'.($idx + 1).'.jpg' : null,
                ]);
            }
        });

        $this->command->info('✅ Dummy event data seeded successfully!');
    }
}
