<?php

namespace App\Console\Commands;

use App\Models\House;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:import-house-names')]
#[Description('Fill in house names for existing house codes from "Data Warga BuluLand"')]
class ImportHouseNames extends Command
{
    /**
     * Blok (house code) => Nama, from "Data Warga BuluLand.xlsx" (Sheet2).
     */
    private const NAMES = [
        'A5' => 'FAJAR',
        'A7' => 'JOKO',
        'A9' => 'WIWIK / BILAL',
        'B1' => 'RIZAL',
        'B2' => 'RAHMAN',
        'B3' => 'MEGA',
        'B4' => 'MUSTOFA',
        'B5' => 'FIAN/TUTUT',
        'B6' => 'AAN',
        'B7' => 'ARIS',
        'B8' => 'FENDI',
        'B9' => 'FUAD',
        'C1' => 'HARIS',
        'C2' => 'ANDY/HAR',
        'C3' => 'MASHUDI',
        'C4' => 'HAMDAN/HENDRA (Kontrak)',
        'C5' => 'SRI WAHYU N (Kontrak)',
        'C6' => 'NUR CHOLIS/ANA',
        'D1' => 'MASKUR',
        'D2' => 'NURI',
        'D4' => 'SUYANTO / Nanda (Kontrak)',
        'D5' => 'MASKUR/IQBAL',
        'D6' => 'AHIL',
        'D7' => 'DAYAT',
        'D8' => 'Dedi / Fakih (Kontrak)',
        'D9' => 'Prisma',
        'D10' => 'IWAN / ALFIA (Kontrak)',
        'E1' => 'DONI',
        'E2' => 'ERIK',
        'E3' => 'EKA (Kontrak)',
        'E4' => 'Dwi Ajeng (Kontrak)',
        'E5' => 'AGUS',
        'E7' => 'ANGGA/EVA',
        'E8' => 'FAIZ',
        'E9' => 'ROMLI',
        'E11' => 'AGUS',
        'E12' => 'NURCHOLIS',
        'E14' => 'Ruslan',
        'E15' => 'HAVID',
        'E17' => 'AYIEK',
        'E20' => 'AHMAD RIDLA',
        'E21' => 'P.BAMBANG',
        'E22' => 'SARIF',
        'E24' => 'SUNYOTO',
        'E25' => 'Hendra',
        'E26' => 'MATSUNI/SA\'DIAH (Kontrak)',
        'E27' => 'ACHMAD',
        'E28' => 'DIDIK',
        'E29' => 'EDDY',
        'E30' => 'SONY',
        'F1' => 'LUTFI / NININ (Kontrak)',
        'F2' => 'BAROS',
        'F3' => 'FADIL',
        'F4' => 'SUGENG S',
        'F5' => 'IBU RATNA',
        'F6' => 'MISNAJI',
        'G1' => 'RIDWAN',
        'G2' => 'SOLEH',
        'G3' => 'BAPAK SYAKUR',
        'G4' => 'ADE',
        'G5' => 'FERRY',
        'G6' => 'EVAN',
        'G7' => 'FAISAL',
        'G8' => 'MUJINO',
        'H1' => 'USMAN',
        'H2' => 'ANGGA',
        'H3' => 'TOMI',
        'H4' => 'KAMAL',
        'H5' => 'Andika',
        'H6' => 'EDI',
        'H7' => 'Dika',
        'H8' => 'ARIF',
        'H10' => 'Nur Halimah (diKontrak) Didik',
        'H11' => 'Salehuddin',
        'H14' => 'HENDRA',
        'H15' => 'REY',
        'H16' => 'INTAN/Sukron',
        'H17' => 'MAS LUTFI',
        'H19' => 'Rizal',
        'H20' => 'HUDA',
        'H21' => 'Arya Pangestu',
        'H23' => 'Reza',
        'H24' => 'Rachman',
        'H26' => 'Aak',
        'H27' => 'SEPTI',
        'H28' => 'Heri Gunawan',
        'H30' => 'Risman',
        'H31' => 'Rofik',
        'H32' => 'Busri',
        'H35' => 'Deny',
        'H36' => 'Imron',
        'H37' => 'Deva',
        'H38' => 'Asnan',
        'H39' => 'Kholis',
        'H43' => 'Hendrik',
        'I1' => 'WAHID (P.Gun-Kontrak)',
        'I2' => 'FIKRI',
        'I5' => 'SAIFUL',
        'I6' => 'Pak Yogi',
        'I7' => 'HASAN',
        'I11' => 'FITRI',
        'I12' => 'BERIN',
        'I15' => 'IRMAN',
        'I16' => 'Novia',
        'I18' => 'M.KUSAIRI',
        'I19' => 'YUDI SALAM',
        'I20' => 'SUPARMAN',
        'I23' => 'FAIZ',
        'I24' => 'MESRI',
        'I25' => 'SUPANDRI',
        'I26' => 'TAROM',
        'I27' => 'SITI MAIMUNA',
        'I29' => 'M. Quraisy',
        'I31' => 'UBED',
        'I33' => 'Lutfi',
        'I34' => 'Moh. Efendy',
        'I35' => 'Agus Salim',
        'I37' => 'MUSDALIFAH',
        'I38' => 'Noval',
        'I39' => 'Abdurrahman',
        'I40' => 'NISRINA (Dion) Kontrak',
        'I42' => 'YUNUS',
        'I43' => 'AZIZAH',
        'I44' => 'Koperasi',
        'I45' => 'IRFAN',
        'I46' => 'FAUZI',
        'I48' => 'SYAIFUL',
        'I49' => 'SAMPIT',
        'I50' => 'SATRIO',
        'I51' => 'Ridwan',
        'I52' => 'Yanti',
        'I53' => 'Veria',
        'I54' => 'Aldi',
        'I58' => 'Feri',
        'I60' => 'Sumiati',
        'I61' => 'Yuli',
        'I62' => 'Pandu',
        'I63' => 'Risky',
        'I64' => 'Sofia',
        'I65' => 'Dodik',
        'I66' => 'Halimatus',
        'I67' => 'Malik',
        'I70' => 'Sholehudin',
        'I71' => 'Josa',
        'I72' => 'ABDUL ROZAK',
        'I74' => 'Wiwik',
        'I75' => 'Roy Margiono',
        'I77' => 'Galih',
        'I78' => 'Ika Yuliana',
        'I79' => 'Arum',
        'I80' => 'Abd. Rasid',
        'I82' => 'Faisal',
        'I83' => 'Anisa',
        'I85' => 'Iqbal',
        'I87' => 'Zaki',
        'I88' => 'Umi/Rohim',
        'I89' => 'Supriyadi',
        'I90' => 'May Susilowati',
        'I91' => 'Humaidi',
        'I92' => 'Dewi Purwati',
        'I94' => 'Mahsun',
        'I96' => 'Habiburrahmah',
        'I107' => 'Fandi',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $updated = 0;
        $skipped = 0;

        foreach (self::NAMES as $code => $name) {
            $house = House::whereRaw('upper(code) = ?', [strtoupper($code)])->first();

            if (! $house) {
                $skipped++;

                continue;
            }

            $house->update(['name' => $name]);
            $updated++;
        }

        $this->info("Updated {$updated} house(s), skipped {$skipped} code(s) not found in the database.");
    }
}
