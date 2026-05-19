# Panduan Penjelasan Video: Fitur Login + Password Validasi (Role-Based)

Berikut adalah panduan teks yang bisa kamu baca atau jadikan acuan saat merekam video penjelasan tugasmu.

---

## 1. Pembukaan — Pengenalan Fitur

**Tampilkan di layar:** Buka browser, akses `http://127.0.0.1:8000`

**Yang bisa kamu katakan:**
> "Halo semuanya, pada video kali ini saya akan menjelaskan perubahan yang saya lakukan pada aplikasi Madura Mart, khususnya pada modul **Purchase**. Ada dua fitur utama yang saya tambahkan: **Fitur Login dengan sistem otentikasi**, dan **validasi password berbasis role (hak akses)** sebelum melakukan edit atau hapus data Purchase."

---

## 2. Demo Fitur Login

**Tampilkan di layar:** Akses langsung `/purchase` tanpa login → otomatis diarahkan ke `/login`

> "Pertama, saya akan menunjukkan fitur Login. Ketika saya mencoba mengakses halaman Purchase tanpa login, sistem otomatis mengarahkan saya ke halaman Login. Ini karena semua rute aplikasi sekarang dilindungi oleh *middleware* `auth` dari Laravel."

*(Masuk login sebagai Admin)*
> "Saya login menggunakan akun **Admin/Staf** dengan email `admin@gmail.com` dan password `admin123`."

---

## 3. Demo Validasi Password — Login sebagai Admin (Staf)

**Tampilkan di layar:** Halaman `/purchase`, lalu klik tombol Edit di salah satu baris

> "Setelah login sebagai Admin, perhatikan ketika saya menekan tombol **Edit** atau **Hapus** pada data Purchase. Sistem akan memunculkan pop-up SweetAlert yang meminta password atasan terlebih dahulu."
> "Jika saya masukkan password yang salah, muncul pesan 'Password salah!'. Jika password benar (yaitu `123`), sistem memberikan konfirmasi lalu mengarahkan ke halaman Edit."

*(Lakukan hal yang sama untuk tombol Delete)*
> "Untuk tombol **Hapus**, setelah password benar, sistem akan meminta konfirmasi sekali lagi: 'Are you sure want to delete?' sebelum data benar-benar dihapus."

---

## 4. Demo Validasi Password — Login sebagai Owner (Atasan)

**Tampilkan di layar:** Logout, lalu login ulang sebagai Owner

> "Sekarang saya logout, lalu masuk kembali menggunakan akun **Owner (Atasan)** dengan email `atasan@gmail.com` dan password `owner123`."

*(Setelah login, klik tombol Edit atau Hapus di Purchase)*
> "Perhatikan perbedaannya! Ketika login sebagai **Owner**, sistem **tidak meminta password tambahan**. Tombol Edit langsung membuka halaman form, dan tombol Hapus hanya memunculkan konfirmasi penghapusan biasa tanpa harus memasukkan password atasan. Ini karena Owner sudah memiliki hak penuh."

---

## 5. Penjelasan Kodingan

**Tampilkan di layar:** VS Code, buka file `routes/web.php`

### a. File: `routes/web.php`
> "Perubahan pertama ada di file `routes/web.php`. Saya menambahkan tiga rute untuk autentikasi: `GET /login` untuk menampilkan form, `POST /login` untuk proses login, dan `POST /logout` untuk logout."
> "Yang terpenting, semua rute aplikasi seperti `dashboard`, `purchase`, `product`, dll. sekarang saya bungkus dalam grup `Route::middleware('auth')`. Artinya, Laravel akan otomatis memaksa pengguna untuk login sebelum bisa mengakses halaman manapun."

**Tampilkan di layar:** File `app/Http/Controllers/AuthController.php`

### b. File: `AuthController.php`
> "Saya membuat *controller* baru bernama `AuthController`. Di dalamnya ada tiga fungsi utama:"
> "- `login()`: untuk menampilkan halaman form login."
> "- `loginProcess()`: untuk memvalidasi email & password, lalu memanggil `Auth::attempt()` milik Laravel. Jika berhasil, sesi pengguna dibuat dan diarahkan ke Dashboard."
> "- `logout()`: untuk menghapus sesi dan mengarahkan kembali ke halaman Login."

**Tampilkan di layar:** File `resources/views/be/auth/login.blade.php`

### c. File: `login.blade.php`
> "Ini adalah tampilan halaman Login yang saya buat. Desainnya menggunakan CSS custom dengan warna gelap gradien agar terlihat premium dan selaras dengan tema Soft UI Dashboard."

**Tampilkan di layar:** File `resources/views/purchase/index.blade.php`, bagian tombol Edit/Delete

### d. File: `purchase/index.blade.php` — Logika Role
> "Perubahan terakhir dan yang paling kunci ada di bagian tombol Edit dan Delete di halaman daftar Purchase. Saya menggunakan kondisi Blade `@if(auth()->user()->role === 'owner')`."
> "Jika pengguna yang login memiliki role `owner`, tombol Edit langsung berupa link biasa dan tombol Delete hanya memunculkan konfirmasi biasa. Namun, jika role-nya adalah `admin` atau lainnya, tombol tersebut akan memanggil fungsi `requirePasswordForEdit()` dan `requirePasswordForDelete()` yang meminta input password atasan terlebih dahulu."

---

## 6. Penutup

> "Dengan implementasi ini, sistem menjadi lebih aman dan terstruktur. Staf atau kasir biasa tidak dapat sembarangan menghapus atau mengubah data Purchase tanpa persetujuan atasan, sementara atasan sendiri bisa langsung melakukan perubahan karena memiliki hak akses penuh. Sekian penjelasan dari saya, terima kasih!"

---

## Akun untuk Demo:
| Role  | Email              | Password  |
|-------|--------------------|-----------|
| Admin | admin@gmail.com    | admin123  |
| Owner | atasan@gmail.com   | owner123  |
| Boss Pass (modal) | — | 123 |
