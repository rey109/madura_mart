# CHANGELOG / PATCH NOTES

Dokumen ini digunakan untuk melacak setiap perubahan (patch) yang dilakukan dan diunggah (push) ke GitHub.

---

## [Patch 1.0.1] - 2026-05-19
### Menambahkan (Added)
- Menambahkan script SweetAlert pada halaman `purchase/index.blade.php` untuk memunculkan modal validasi password atasan sebelum melakukan proses Edit dan Delete.
- Menambahkan desain notifikasi berhasil (*"Nice! Your password is correct!"*) ketika password diinputkan dengan benar pada halaman indeks Purchase.
- Menambahkan desain konfirmasi peringatan penghapusan (*"Are you sure want to delete?"*) untuk aksi Hapus Data (Delete) setelah validasi password.

### Mengubah (Changed)
- Mendesain ulang seluruh halaman `purchase/create.blade.php` (form pengisian) dan `purchase/index.blade.php` (tabel data) agar tata letaknya persis seperti instruksi modul (termasuk tombol warna pink keunguan / Soft UI default).
- Mengubah fungsi tombol Edit dan Delete di halaman Purchase menggunakan atribut `onclick` untuk memicu fungsi Javascript `requirePasswordForEdit` dan `requirePasswordForDelete`.

### Menghapus (Removed)
- Menghapus fitur-fitur dan halaman yang berkaitan dengan AI Assistant, termasuk di `web.php` dan `master.blade.php` (Pekerjaan sebelumnya).

---

*(Tambahkan Patch berikutnya di bagian atas setiap kali ada perubahan kode sebelum push ke GitHub)*
