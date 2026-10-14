TOPUPKU V2 - MIDNIGHT NEON
================================

STACK
- Native PHP
- MySQL / MariaDB
- HTML + CSS + JavaScript
- phpMyAdmin untuk mengelola database

FITUR
- Homepage: hero, search, kategori, produk terbaru, produk terlaris
- Kategori produk dari kolom products.category
- Produk: search, filter, sort, quick buy
- Dashboard user: saldo, total transaksi, transaksi terakhir, quick action
- Transaksi: riwayat, filter status, detail, invoice, print
- Admin: dashboard, produk, user, saldo, transaksi
- Admin saldo: tambah saldo dan tarik/kurangi saldo
- User tidak dapat mengubah saldo sendiri
- Login user + admin satu pintu di /login.php
- Login admin: username admin, password admin123
- /admin/ hanya dapat dibuka dengan session admin
- Dark mode, responsive, animasi, toast
- Top Up Saldo manual melalui QRIS + verifikasi admin

DATABASE
- Database: topupku
- Tidak ada tabel withdrawal.
- Saldo tetap memakai users.saldo.
- Struktur database tetap:
  users(id, name, email, email_verified_at, password, remember_token, saldo, created_at, updated_at)
  products(id, name, category, nominal, price, created_at, updated_at)
  transactions(id, user_id, product_id, target_number, price, status, created_at, updated_at)

MEMBERSIHKAN PRODUK DEMO
- Paket ini tidak memasukkan produk demo ke SQL.
- Jika database lokalmu sudah berisi produk lama, hapus produk tersebut
  dari phpMyAdmin sebelum mulai mengisi katalog baru.
- Jangan menghapus products jika masih ada transaksi yang mereferensikannya.
  Untuk reset total data demo, backup database terlebih dahulu.

QRIS / UANG ASLI
- Fitur QRIS pada topup.php adalah alur manual, bukan payment gateway.
- Paket sengaja tidak membuat QRIS palsu.
- Ganti placeholder QRIS dengan QRIS resmi milikmu sendiri sebelum
  menerima uang sungguhan.
- Setelah teman membayar, admin memverifikasi pembayaran lalu
  menambah saldo lewat Admin > Saldo.
- Jangan menganggap transfer berhasil hanya berdasarkan screenshot;
  verifikasi pembayaran dari sumber pembayaran yang benar.
- Untuk skala publik/komersial, gunakan penyedia payment gateway/QRIS
  resmi dan perhatikan aturan serta kewajiban yang berlaku.

XAMPP LINUX
1. Aktifkan Apache dan MySQL XAMPP.
2. Pastikan database topupku tersedia.
3. Jalankan dari folder project:
   /opt/lampp/bin/php -S localhost:8080
4. Buka:
   http://localhost:8080

Jika memakai htdocs:
   /opt/lampp/htdocs/topupku_v2

CATATAN ADMIN
Username: admin
Password: admin123

Untuk proyek lokal/sekolah, credential admin masih ditulis langsung
di login.php. Untuk penggunaan sungguhan, pindahkan credential ke
konfigurasi yang aman dan gunakan autentikasi yang lebih kuat.

LOGIN
- User: email + password yang tersimpan di users.
- Admin: username "admin" + password "admin123".
- Keduanya masuk melalui /login.php.


REVISI MIDNIGHT DARK UI
- Tema situs dibuat dark-only (tidak kembali ke light mode).
- File BERSIHKAN_HISTORY_TRANSAKSI.sql disediakan terpisah untuk menghapus riwayat transaksi uji coba saja.
- Jalankan SQL tersebut melalui phpMyAdmin pada database topupku HANYA jika yakin ingin menghapus semua baris transaksi.
- Script tersebut tidak menghapus users, saldo, atau products. Backup database sebelum menjalankannya.
