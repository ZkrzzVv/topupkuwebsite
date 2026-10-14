USE `topupku`;

-- HAPUS SEMUA RIWAYAT TRANSAKSI UJI COBA.
-- Tidak menghapus akun pengguna, saldo, atau daftar produk.
-- Jalankan hanya jika memang ingin mengosongkan seluruh riwayat transaksi.
DELETE FROM `transactions`;

-- Reset nomor ID transaksi berikutnya (opsional dan aman setelah tabel kosong).
ALTER TABLE `transactions` AUTO_INCREMENT = 1;
