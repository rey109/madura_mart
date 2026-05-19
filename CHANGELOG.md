# CHANGELOG / PATCH NOTES

Dokumen ini digunakan untuk melacak setiap perubahan (patch) yang dilakukan dan diunggah (push) ke GitHub.

---

## [Patch 1.0.2] - 2026-05-19
### Menambahkan (Added)
- Menambahkan fitur **Login Otentikasi** menggunakan sistem `Auth` bawaan Laravel. Pengguna kini wajib login sebelum bisa mengakses halaman manapun.
- Membuat `AuthController.php` dengan fungsi `login()`, `loginProcess()`, dan `logout()`.
- Membuat halaman UI Login baru (`resources/views/be/auth/login.blade.php`) dengan desain gradien gelap yang modern dan premium.
- Menambahkan tombol **Logout** beserta informasi nama dan role pengguna yang sedang aktif pada sidebar menu.
- Membuat 2 akun pengguna untuk demo: `admin@gmail.com` (role: admin) dan `atasan@gmail.com` (role: owner).

### Mengubah (Changed)
- Mengubah `routes/web.php`: Semua rute aplikasi utama sekarang dibungkus dalam grup `Route::middleware('auth')` agar hanya dapat diakses pengguna yang sudah login.
- Mengubah tombol **Edit** dan **Delete** di halaman `purchase/index.blade.php` agar bersifat **role-based**:
  - Pengguna dengan role `owner`: langsung bisa Edit/Hapus tanpa memasukkan password tambahan.
  - Pengguna dengan role `admin`: harus memasukkan password atasan terlebih dahulu melalui modal SweetAlert.
- Memperbarui `penjelasan_video.md` dengan naskah penjelasan yang lengkap mencakup fitur Login dan sistem role.

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
