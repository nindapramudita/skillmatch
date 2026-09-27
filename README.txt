PATCH NOTIFIKASI + WORKSPACE SKILLMATCH

FILE YANG DIREPLACE:
1. vite.config.js
2. routes/web.php
3. resources/views/v_notifications/index.blade.php
4. resources/views/v_notifications/show.blade.php
5. resources/views/v_projects/workspace.blade.php
6. resources/css/notifications.css
7. resources/css/projects.css

PERUBAHAN UTAMA:
- notifications.css ditambahkan ke Vite input agar error "Unable to locate file in Vite manifest" hilang.
- Klik notifikasi membuka detail pelamar.
- Terima Join Tim memasukkan pelamar ke members lalu owner diarahkan ke workspace/kelola projek.
- Tolak kembali ke daftar notifikasi.
- Workspace menampilkan owner dan anggota aktual.
- Owner dapat mengeluarkan anggota melalui popup konfirmasi.
- Owner dapat menambah capaian, menambah file, dan menandai projek selesai.
- Saat anggota dikeluarkan, membership dan TeamRequest terkait dihapus agar user dapat mengajukan lagi di kemudian hari.

SETELAH REPLACE FILE:
php artisan optimize:clear
npm.cmd run build
php artisan serve

Untuk development, bisa memakai:
npm.cmd run dev

Catatan: jangan jalankan php artisan migrate:fresh.
