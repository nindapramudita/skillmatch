<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoProjectSeeder extends Seeder
{
    public function run(): void
    {
        $tharisha = User::firstOrCreate(
            ['email' => 'tharisha.demo@skillmatch.test'],
            [
                'name' => 'Tharisha Nur Fadhilah',
                'password' => Hash::make('SkillMatch123!'),
            ]
        );

        $tharisha->forceFill([
            'email_verified_at' => now(),
            'university' => 'Universitas Pancasila',
            'study_program' => 'Teknik Informatika',
            'semester' => 6,
            'team_status' => 'active',
            'skills' => ['Laravel', 'Figma', 'MySQL', 'UI/UX Design'],
        ])->save();

        $jesen = User::firstOrCreate(
            ['email' => 'jesen.demo@skillmatch.test'],
            [
                'name' => 'Jesen Saputra',
                'password' => Hash::make('SkillMatch123!'),
            ]
        );

        $jesen->forceFill([
            'email_verified_at' => now(),
            'university' => 'Universitas Pancasila',
            'study_program' => 'Teknik Informatika',
            'semester' => 6,
            'team_status' => 'active',
            'skills' => ['React JS', 'Tailwind CSS', 'REST API', 'Node.js'],
        ])->save();

        $projects = [
            [
                'owner' => $tharisha,
                'title' => 'AI Deteksi Sampah',
                'description' => 'Membangun model AI untuk mengenali jenis sampah melalui kamera secara otomatis dan membantu proses pemilahan sampah.',
                'deadline' => '2026-11-20',
                'roles' => [
                    ['name' => 'Python', 'criteria' => 'Memahami pengolahan data dan dasar machine learning.'],
                    ['name' => 'Laravel', 'criteria' => 'Mampu mengembangkan backend dan integrasi API.'],
                    ['name' => 'Figma', 'criteria' => 'Mampu membuat rancangan antarmuka aplikasi.'],
                ],
                'milestones' => [
                    'Persiapan dataset sampah',
                    'Pelatihan model AI',
                    'Integrasi model dengan aplikasi',
                    'Testing dan evaluasi',
                ],
            ],
            [
                'owner' => $jesen,
                'title' => 'Kampanye Edukasi Gizi Remaja',
                'description' => 'Membuat kampanye edukasi tentang pola makan sehat untuk mahasiswa dan remaja melalui poster, booklet, dan media sosial.',
                'deadline' => '2026-10-28',
                'roles' => [
                    ['name' => 'Desain Grafis', 'criteria' => 'Mampu membuat materi visual edukatif.'],
                    ['name' => 'Copywriting', 'criteria' => 'Mampu menulis pesan kampanye yang mudah dipahami.'],
                    ['name' => 'Riset', 'criteria' => 'Mampu mengolah referensi materi gizi remaja.'],
                ],
                'milestones' => [
                    'Riset materi gizi',
                    'Pembuatan konsep kampanye',
                    'Produksi konten',
                    'Publikasi dan evaluasi',
                ],
            ],
            [
                'owner' => $tharisha,
                'title' => 'Majalah Digital Budaya',
                'description' => 'Membuat majalah digital berisi cerita budaya, tradisi, kuliner, dan tokoh lokal dengan tampilan visual yang menarik.',
                'deadline' => '2026-10-20',
                'roles' => [
                    ['name' => 'Fotografi', 'criteria' => 'Mampu mengambil dokumentasi visual.'],
                    ['name' => 'Editing', 'criteria' => 'Mampu menyunting foto dan konten publikasi.'],
                    ['name' => 'Ilustrasi', 'criteria' => 'Mampu membuat ilustrasi pendukung.'],
                ],
                'milestones' => [
                    'Riset tema budaya',
                    'Pengumpulan konten',
                    'Desain dan penyuntingan',
                    'Publikasi majalah digital',
                ],
            ],
            [
                'owner' => $jesen,
                'title' => 'Branding Coffee Shop Lokal',
                'description' => 'Membuat identitas visual dan media promosi digital untuk membantu UMKM coffee shop membangun citra merek yang konsisten.',
                'deadline' => '2026-12-15',
                'roles' => [
                    ['name' => 'Figma', 'criteria' => 'Mampu membuat desain identitas visual.'],
                    ['name' => 'Illustrator', 'criteria' => 'Mampu membuat aset grafis dan logo.'],
                    ['name' => 'Branding', 'criteria' => 'Memahami dasar strategi dan identitas merek.'],
                ],
                'milestones' => [
                    'Riset brand dan target pasar',
                    'Konsep identitas visual',
                    'Desain media promosi',
                    'Finalisasi brand guideline',
                ],
            ],
        ];

        foreach ($projects as $item) {
            /** @var \App\Models\User $owner */
            $owner = $item['owner'];

            $project = Project::updateOrCreate(
                [
                    'owner_id' => $owner->id,
                    'title' => $item['title'],
                ],
                [
                    'description' => $item['description'],
                    'deadline' => $item['deadline'],
                    'roles' => $item['roles'],
                    'milestones' => $item['milestones'],
                    'documents' => [],
                    'status' => 'ongoing',
                ]
            );

            $project->members()->syncWithoutDetaching([
                $owner->id => ['role' => 'Pemilik'],
            ]);
        }
    }
}
