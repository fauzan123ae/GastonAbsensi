<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Location;
use App\Models\PermissionRequest;
use App\Models\SessionParticipant;
use App\Models\Sport;
use App\Models\SportSession;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            return;
        }

        // 1. MASTER ADMIN & ADMIN BIASA
        $masterAdmin = User::updateOrCreate(
            ['email' => 'master@gaston.test'],
            [
                'member_id' => 'GST-MSTR0',
                'name' => 'Master Admin Gaston',
                'phone' => '081299990001',
                'password' => 'password',
                'qr_token' => User::generateQrToken(),
                'role' => 'master_admin',
                'status' => 'active',
            ]
        );

        // Alias admin@gaston.test tetap dipertahankan sebagai Master Admin untuk kompatibilitas
        $adminGaston = User::updateOrCreate(
            ['email' => 'admin@gaston.test'],
            [
                'member_id' => 'GST-00000',
                'name' => 'Admin Gaston',
                'phone' => '081234567890',
                'password' => 'password',
                'qr_token' => User::generateQrToken(),
                'role' => 'master_admin',
                'status' => 'active',
            ]
        );

        // Admin Biasa
        $staffAdmin = User::updateOrCreate(
            ['email' => 'staff@gaston.test'],
            [
                'member_id' => 'GST-ADM01',
                'name' => 'Admin Staff (Operasional)',
                'phone' => '081234567891',
                'password' => 'password',
                'qr_token' => User::generateQrToken(),
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        $admin = $masterAdmin;

        // 2. MEMBER USERS
        $dummyMembers = [
            ['name' => 'Fauzan', 'email' => 'fauzan@gaston.test', 'phone' => '08121111001'],
            ['name' => 'Andi', 'email' => 'andi@gaston.test', 'phone' => '08121111002'],
            ['name' => 'Budi', 'email' => 'budi@gaston.test', 'phone' => '08121111003'],
            ['name' => 'Rizky', 'email' => 'rizky@gaston.test', 'phone' => '08121111004'],
            ['name' => 'Dimas', 'email' => 'dimas@gaston.test', 'phone' => '08121111005'],
            ['name' => 'Fajar', 'email' => 'fajar@gaston.test', 'phone' => '08121111006'],
            ['name' => 'Adit', 'email' => 'adit@gaston.test', 'phone' => '08121111007'],
            ['name' => 'Rian', 'email' => 'rian@gaston.test', 'phone' => '08121111008'],
            ['name' => 'Bayu', 'email' => 'bayu@gaston.test', 'phone' => '08121111009'],
            ['name' => 'Ilham', 'email' => 'ilham@gaston.test', 'phone' => '08121111010'],
            ['name' => 'Nonaktif Eko', 'email' => 'eko@gaston.test', 'phone' => '08121111011', 'status' => 'inactive'],
        ];

        $createdMembers = [];
        $index = 1;
        foreach ($dummyMembers as $data) {
            $memberId = sprintf('GST-%05d', $index++);
            $token = 'GST-MBR-' . strtoupper(Str::random(10));
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'member_id' => $memberId,
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'password' => 'password',
                    'qr_token' => $token,
                    'role' => 'member',
                    'status' => $data['status'] ?? 'active',
                ]
            );
            $createdMembers[] = $user;
        }

        // 3. SPORTS
        $sportsData = [
            ['name' => 'Futsal', 'description' => 'Olahraga futsal beregu 5 vs 5'],
            ['name' => 'Basket', 'description' => 'Olahraga bola basket lapangan'],
            ['name' => 'Badminton', 'description' => 'Bulu tangkis ganda & tunggal'],
            ['name' => 'Voli', 'description' => 'Bola voli lapangan'],
            ['name' => 'Tenis', 'description' => 'Tenis lapangan outdoor/indoor'],
        ];
        $sports = [];
        foreach ($sportsData as $s) {
            $sports[] = Sport::firstOrCreate(['name' => $s['name']], ['description' => $s['description'], 'status' => 'active']);
        }

        // 4. LOCATIONS
        $locationsData = [
            ['name' => 'GOR Manahan', 'address' => 'Jl. Adi Sucipto No. 1, Manahan, Solo', 'description' => 'Kompleks Olahraga Manahan'],
            ['name' => 'GOR Kartasura', 'address' => 'Jl. Ahmad Yani, Kartasura, Sukoharjo', 'description' => 'GOR Bulutangkis & Futsal'],
            ['name' => 'GOR Klaten', 'address' => 'Jl. Pemuda, Klaten', 'description' => 'GOR Serbaguna Klaten'],
        ];
        $locations = [];
        foreach ($locationsData as $l) {
            $locations[] = Location::firstOrCreate(['name' => $l['name']], ['address' => $l['address'], 'description' => $l['description'], 'status' => 'active']);
        }

        // 5. SPORT SESSIONS
        // Sesi 1: Hari ini (Open/Ongoing)
        $todaySession = SportSession::firstOrCreate(
            [
                'sport_id' => $sports[0]->id,
                'location_id' => $locations[0]->id,
                'date' => now()->toDateString(),
                'start_time' => '19:00:00',
            ],
            [
                'end_time' => '21:00:00',
                'status' => 'open',
                'notes' => 'Sesi Futsal Rutin Mingguan',
                'created_by' => $admin->id,
            ]
        );

        // Tambah peserta ke Sesi 1 (8 member pertama)
        for ($i = 0; $i < 8; $i++) {
            $member = $createdMembers[$i];
            SessionParticipant::firstOrCreate(
                ['session_id' => $todaySession->id, 'user_id' => $member->id],
                ['registration_source' => 'whatsapp', 'status' => 'registered', 'registered_at' => now()]
            );
        }

        // Simulasi 3 orang sudah absen di sesi 1
        for ($i = 0; $i < 3; $i++) {
            $member = $createdMembers[$i];
            Attendance::firstOrCreate(
                ['session_id' => $todaySession->id, 'user_id' => $member->id],
                [
                    'checked_in_at' => now()->subMinutes(15 * (3 - $i)),
                    'checked_in_by' => $admin->id,
                    'method' => $i === 0 ? 'qr' : 'manual',
                    'status' => 'present',
                ]
            );
        }

        // Simulasi 1 orang izin
        PermissionRequest::firstOrCreate(
            ['session_id' => $todaySession->id, 'user_id' => $createdMembers[3]->id],
            [
                'reason' => 'Sedang demam dan istirahat dokter',
                'status' => 'pending',
            ]
        );

        // Sesi 2: Badminton Mendatang
        SportSession::firstOrCreate(
            [
                'sport_id' => $sports[2]->id,
                'location_id' => $locations[1]->id,
                'date' => now()->addDays(2)->toDateString(),
                'start_time' => '16:00:00',
            ],
            [
                'end_time' => '18:00:00',
                'status' => 'draft',
                'notes' => 'Latihan bulutangkis ganda',
                'created_by' => $admin->id,
            ]
        );
    }
}
