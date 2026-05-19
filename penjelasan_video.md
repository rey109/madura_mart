# Panduan Penjelasan Video: Fitur Login, Otentikasi, dan Role-Based Action Validation

Dokumen ini berisi panduan lengkap untuk merekam video penjelasan tugas/fitur yang baru saja ditambahkan (sesuai dengan Changelog Patch 1.0.1 & 1.0.2). Panduan ini dibagi menjadi dua bagian utama:
1. **Skenario Demo Aplikasi (UI/UX)** - Langkah demi langkah apa yang harus ditunjukkan di layar dan apa yang harus diucapkan.
2. **Penjelasan Kode & Logika Fungsi (Code walkthrough)** - Bedah kode program secara detail agar Anda bisa menjelaskan cara kerja teknis di balik layar dengan percaya diri dan benar.

---

# BAGIAN 1: SKENARIO DEMO APLIKASI (UI/UX)

## Langkah 1: Uji Coba Keamanan Rute (Middleware Auth)
*   **Aksi di Layar:**
    *   Buka browser dalam mode *Incognito* atau pastikan Anda dalam kondisi belum login.
    *   Coba ketik URL langsung ke halaman purchase: `http://127.0.0.1:8000/purchase` atau ketik `http://127.0.0.1:8000/dashboard`.
    *   Tunjukkan bahwa sistem secara otomatis mengalihkan (redirect) halaman ke `/login`.
*   **Naskah Penjelasan:**
    > *"Pertama, saya akan mendemonstrasikan aspek keamanan sistem. Di sini saya mencoba mengakses halaman Purchase atau Dashboard secara langsung tanpa melakukan login terlebih dahulu. Seperti yang terlihat di layar, sistem langsung menolak akses tersebut dan mengarahkan saya ke halaman login `/login`. Ini membuktikan bahwa seluruh rute aplikasi kita sekarang aman karena telah dilindungi oleh middleware otentikasi."*

---

## Langkah 2: Demo Form Login (Halaman Login Premium)
*   **Aksi di Layar:**
    *   Tampilkan form login yang memiliki gradien gelap modern.
    *   Coba masukkan email atau password asal-asalan lalu klik **Sign In** untuk memicu pesan error.
    *   Tunjukkan pesan error SweetAlert atau Alert Danger di atas form login.
*   **Naskah Penjelasan:**
    > *"Ini adalah halaman Login Madura Mart yang baru saja saya rancang ulang dengan desain gradien warna gelap yang premium agar selaras dengan tema Soft UI Dashboard. Form ini sudah dilengkapi dengan validasi input. Jika saya memasukkan email atau password yang salah, sistem akan menampilkan pesan peringatan 'Email atau Password salah.' sehingga user tidak bisa masuk."*

---

## Langkah 3: Login Sebagai Admin (Akses Terbatas / Staf)
*   **Aksi di Layar:**
    *   Masukkan email `admin@gmail.com` dan password `admin123`. Klik **Sign In**.
    *   Tunjukkan notifikasi SweetAlert sukses masuk: *"Berhasil Login!"*.
    *   Arahkan kursor ke sidebar kiri bawah, tunjukkan nama user yang aktif: **Staf** dengan role **Admin**.
    *   Masuk ke menu **Purchase**.
*   **Naskah Penjelasan:**
    > *"Sekarang, saya akan login menggunakan akun Admin atau Staf. Emailnya adalah `admin@gmail.com` dan password-nya `admin123`. Setelah masuk, sistem menampilkan alert sukses dan di pojok kiri bawah menu sidebar sekarang muncul informasi nama user yang sedang aktif beserta role-nya, yaitu 'admin'. Kita masuk ke halaman Purchase."*

---

## Langkah 4: Demo Validasi Password Atasan (Role Admin)
*   **Aksi di Layar:**
    *   Pada tabel Purchase, klik salah satu tombol **Edit** (ikon pensil).
    *   Akan muncul modal SweetAlert input password: *"Password required! Write your boss's password:"*.
    *   Ketik password salah (misal: `abc` atau `999`), lalu klik **OK**. Tunjukkan pesan error input *"Password salah!"*.
    *   Klik **Edit** lagi, masukkan password yang benar: `123`. Klik **OK**. Tunjukkan alert sukses *"Nice! Your password is correct!"*, lalu sistem akan mengarahkan ke form edit purchase.
    *   Kembali ke halaman index, klik tombol **Delete** (ikon tempat sampah).
    *   Masukkan password atasan `123`. Klik **OK**.
    *   Tunjukkan bahwa setelah password benar, SweetAlert memunculkan konfirmasi kedua: *"Are you sure want to delete? Your will not be able to recover this data!"*. Klik **CANCEL** agar data tidak terhapus.
*   **Naskah Penjelasan:**
    > *"Karena saya saat ini login sebagai Admin atau staf biasa, saya tidak diizinkan untuk mengedit atau menghapus data pembelian secara sembarangan. Ketika saya mengklik tombol Edit, sistem memicu modal SweetAlert yang meminta password atasan/owner. Jika password yang dimasukkan salah, akses ditolak. Namun jika saya masukkan password atasan yang benar, yaitu '123', barulah sistem mengizinkan saya masuk ke halaman edit. Hal yang sama juga berlaku untuk tombol Hapus. Setelah memasukkan password atasan dengan benar, sistem akan menanyakan konfirmasi penghapusan sekali lagi untuk memastikan aspek keamanan data."*

---

## Langkah 5: Login Sebagai Owner/Atasan (Akses Penuh)
*   **Aksi di Layar:**
    *   Klik tombol **Logout** di bagian bawah sidebar.
    *   Login kembali menggunakan akun Owner: email `atasan@gmail.com` dan password `owner123`.
    *   Masuk ke menu **Purchase**.
    *   Klik tombol **Edit** pada salah satu baris. Tunjukkan bahwa halaman edit langsung terbuka **tanpa** pop-up password.
    *   Kembali ke halaman index, klik tombol **Delete**. Tunjukkan bahwa sistem hanya memunculkan konfirmasi hapus biasa (*"Are you sure want to delete?"*) tanpa meminta password atasan.
*   **Naskah Penjelasan:**
    > *"Sekarang saya akan logout dan masuk kembali menggunakan akun Owner atau Atasan, yaitu `atasan@gmail.com` dengan password `owner123`. Pada menu sidebar terlihat role aktif saya sekarang adalah 'owner'. Perhatikan perbedaannya, ketika saya yang memiliki akses Owner menekan tombol Edit, halaman form edit langsung terbuka tanpa ada verifikasi password tambahan. Begitupun tombol Hapus, sistem hanya memunculkan satu kali modal konfirmasi penghapusan langsung tanpa meminta password lagi. Ini karena Owner adalah pemegang hak akses tertinggi di sistem."*

---

# BAGIAN 2: PENJELASAN KODE & LOGIKA FUNGSI (TECHNICAL WALKTHROUGH)

Berikut adalah penjelasan teknis mengenai baris kode dan fungsi-fungsi spesifik yang telah diimplementasikan. Bagian ini sangat penting untuk menjelaskan sisi kodingan Anda dengan tepat dan terstruktur.

---

## 1. Perlindungan Rute (Routing & Middleware)
### File: `routes/web.php`
*   **Potongan Kode:**
    ```php
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginProcess'])->name('login.process');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('auth')->group(function () {
        Route::resource('dashboard', DashboardController::class);
        Route::resource('purchase', PurchaseController::class);
        // ... rute lainnya ...
    });
    ```
*   **Fungsi & Cara Kerja:**
    *   **Rute Auth (Guest):** Rute `/login` didefinisikan di luar middleware agar dapat diakses oleh publik (pengguna yang belum terautentikasi).
    *   **`Route::middleware('auth')->group(...)`:** Ini adalah fitur grup rute bawaan Laravel. Middleware `auth` akan memeriksa apakah session user sudah terdaftar di sistem. Jika user belum login, Laravel secara otomatis melempar user ke rute bernama `login` (sesuai spesifikasi standard Laravel). Semua resource controller seperti `dashboard`, `product`, `purchase`, dsb., diletakkan di dalam grup ini agar terproteksi penuh.

---

## 2. Pengendali Autentikasi (Authentication Controller)
### File: `app/Http/Controllers/AuthController.php`
Di dalam controller ini terdapat 3 function utama yang menangani siklus hidup session user:

*   **Fungsi `login()`**
    ```php
    public function login()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard.index');
        }
        return view('be.auth.login');
    }
    ```
    *   *Penjelasan:* Fungsi ini bertugas menampilkan halaman login. Terdapat pengkondisian `Auth::check()`. Jika terdeteksi user sudah login tapi tidak sengaja mengakses rute `/login`, sistem secara cerdas akan langsung mengarahkannya kembali ke halaman Dashboard utama agar tidak perlu login ulang.

*   **Fungsi `loginProcess(Request $request)`**
    ```php
    public function loginProcess(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard.index')->with('simpan', 'Berhasil Login!');
        }

        return back()->withErrors([
            'email' => 'Email atau Password salah.',
        ])->onlyInput('email');
    }
    ```
    *   *Penjelasan:* Fungsi ini memproses data form login yang dikirim via metode `POST`. Pertama, input divalidasi menggunakan `$request->validate()` untuk memastikan format email benar dan password tidak kosong. Kemudian, fungsi bawaan Laravel `Auth::attempt($credentials, $remember)` dipanggil untuk mencocokkan data inputan dengan hash password di database tabel `users`.
    *   Jika cocok, `session()->regenerate()` dipanggil untuk menghindari serangan keamanan *Session Fixation*, lalu mengarahkan user ke Dashboard dengan membawa alert sukses. Jika gagal, user dikembalikan ke halaman login dengan input email yang tetap terisi (`onlyInput('email')`) serta pesan kesalahan.

*   **Fungsi `logout(Request $request)`**
    ```php
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('simpan', 'Berhasil Logout!');
    }
    ```
    *   *Penjelasan:* Fungsi ini mengakhiri sesi pengguna. `Auth::logout()` menghapus informasi autentikasi user dari guard. `session()->invalidate()` mengosongkan seluruh data session saat ini, dan `session()->regenerateToken()` memperbarui token CSRF untuk mencegah serangan CSRF eksploitasi setelah session berakhir.

---

## 3. Sidebar dan Informasi Pengguna Aktif
### File: `resources/views/be/menu.blade.php`
*   **Potongan Kode Informasi User:**
    ```html
    <h6 class="mb-0 text-sm">{{ auth()->user()->name ?? 'User' }}</h6>
    <p class="mb-0 text-xs text-muted text-capitalize">{{ auth()->user()->role ?? '' }}</p>
    ```
    *   *Penjelasan:* Kita menggunakan helper `auth()->user()` untuk mengambil objek model User yang sedang aktif saat ini. Kita menampilkan properti `name` dan `role` secara dinamis pada bagian bawah sidebar.
*   **Potongan Kode Form Logout:**
    ```html
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn bg-gradient-dark w-100 mb-0 mt-2">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
        </button>
    </form>
    ```
    *   *Penjelasan:* Aksi logout diwajibkan menggunakan metode `POST` dan dilindungi oleh direktif `@csrf`. Hal ini sangat penting untuk mencegah kerentanan keamanan di mana pihak ketiga memaksa user logout secara tidak sengaja melalui link `GET`.

---

## 4. Logika Validasi Berbasis Peran (Role-Based Blade & JavaScript)
### File: `resources/views/purchase/index.blade.php`
Bagian ini merupakan inti dari logika pengkondisian aksi pengeditan dan penghapusan data purchase.

*   **Logika Kondisional Tombol (Blade Template):**
    ```html
    @if(auth()->user()->role === 'owner')
        <!-- Akses Langsung untuk Owner -->
        <a href="{{ route('purchase.edit', $purchase->id) }}" class="action-btn" title="Edit">
            <i class="fas fa-edit"></i>
        </a>
        <form id="delete-form-{{ $purchase->id }}" action="{{ route('purchase.destroy', $purchase->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="button" class="action-btn" title="Delete" onclick="confirmDelete('delete-form-{{ $purchase->id }}')">
                <i class="fas fa-trash-alt"></i>
            </button>
        </form>
    @else
        <!-- Akses Terbatas untuk Admin (Staf) -->
        <button type="button" class="action-btn" title="Edit" onclick="requirePasswordForEdit('{{ route('purchase.edit', $purchase->id) }}')">
            <i class="fas fa-edit"></i>
        </button>
        <form id="delete-form-{{ $purchase->id }}" action="{{ route('purchase.destroy', $purchase->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="button" class="action-btn" title="Delete" onclick="requirePasswordForDelete('delete-form-{{ $purchase->id }}')">
                <i class="fas fa-trash-alt"></i>
            </button>
        </form>
    @endif
    ```
    *   *Penjelasan:* Kode ini memilah rendering HTML berdasarkan role pengguna yang sedang login (`auth()->user()->role`).
        *   Jika role bernilai `'owner'`, tag `<a>` edit langsung mengarah ke halaman edit, dan tombol delete langsung memicu fungsi Javascript `confirmDelete()`.
        *   Jika role bernilai `'admin'`, tag edit diganti menjadi elemen `<button>` yang memicu fungsi Javascript `requirePasswordForEdit()`, begitu pula tombol delete memicu `requirePasswordForDelete()`.

*   **Logika Pemrosesan Modal Password (JavaScript & SweetAlert):**
    ```javascript
    // Password default untuk Kepala Perpus / Atasan
    const BOSS_PASSWORD = "123";

    function requirePasswordForEdit(url) {
        swal({
            title: "Password required!",
            text: "Write your boss's password:",
            type: "input",
            inputType: "password",
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonColor: "#cb0c9f",
            confirmButtonText: "OK",
            cancelButtonText: "CANCEL",
            animation: "slide-from-top",
            inputPlaceholder: ".........."
        }, function(inputValue) {
            if (inputValue === false) return false;
            if (inputValue === "") {
                swal.showInputError("Password tidak boleh kosong!");
                return false;
            }
            
            if (inputValue === BOSS_PASSWORD) {
                swal({
                    title: "Nice!",
                    text: "Your password is correct!",
                    type: "success",
                    confirmButtonColor: "#cb0c9f",
                    confirmButtonText: "OK",
                    closeOnConfirm: true
                }, function() {
                    window.location.href = url; // Mengarahkan ke rute edit
                });
            } else {
                swal.showInputError("Password salah!");
                return false;
            }
        });
    }
    ```
    *   *Penjelasan:* Fungsi `requirePasswordForEdit` menerima parameter `url` tujuan edit. Fungsi ini memanggil fungsi `swal` (SweetAlert) dengan opsi `type: "input"` dan `inputType: "password"` untuk memunculkan kotak input password rahasia.
    *   Ketika pengguna menekan tombol OK, fungsi callback mengevaluasi inputan. Jika input kosong, `swal.showInputError()` dipanggil untuk menampilkan pesan validasi. Jika input cocok dengan konstanta `BOSS_PASSWORD` (yaitu `"123"`), SweetAlert menampilkan pesan sukses dan mengarahkan browser ke `url` tujuan melalui `window.location.href = url`. Jika salah, pesan error ditampilkan kembali.

    ```javascript
    function requirePasswordForDelete(formId) {
        swal({
            title: "Password required!",
            text: "Write your boss's password:",
            type: "input",
            inputType: "password",
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonColor: "#cb0c9f",
            confirmButtonText: "OK",
            cancelButtonText: "CANCEL",
            animation: "slide-from-top",
            inputPlaceholder: ".........."
        }, function(inputValue) {
            if (inputValue === false) return false;
            if (inputValue === "") {
                swal.showInputError("Password tidak boleh kosong!");
                return false;
            }
            
            if (inputValue === BOSS_PASSWORD) {
                // Konfirmasi kedua setelah password benar
                swal({
                    title: "Are you sure want to delete?",
                    text: "Your will not be able to recover this data!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#ea0606",
                    confirmButtonText: "YES, DELETE IT!",
                    cancelButtonText: "CANCEL",
                    closeOnConfirm: false
                }, function(isConfirm) {
                    if (isConfirm) {
                        document.getElementById(formId).submit(); // Mengirimkan form delete
                    }
                });
            } else {
                swal.showInputError("Password salah!");
                return false;
            }
        });
    }
    ```
    *   *Penjelasan:* Fungsi `requirePasswordForDelete` menerima parameter `formId` dari form HTML target. Logika pencocokan password persis sama seperti edit, namun alih-alih mengarahkan ke URL baru, jika password benar, ia memunculkan modal SweetAlert kedua tipe `warning` untuk mengonfirmasi ulang niat hapus data. Jika pengguna mengklik "YES, DELETE IT!", barulah JavaScript memicu submit form penghapusan secara programatik menggunakan `document.getElementById(formId).submit()`.

    ```javascript
    function confirmDelete(formId) {
        swal({
            title: "Are you sure want to delete?",
            text: "Your will not be able to recover this data!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ea0606",
            confirmButtonText: "YES, DELETE IT!",
            cancelButtonText: "CANCEL",
            closeOnConfirm: false
        }, function(isConfirm) {
            if (isConfirm) {
                document.getElementById(formId).submit();
            }
        });
    }
    ```
    *   *Penjelasan:* Fungsi `confirmDelete` digunakan khusus untuk Owner. Fungsi ini langsung memunculkan modal konfirmasi peringatan penghapusan tipe `warning` tanpa meminta input password, lalu mengirimkan form penghapusan jika dikonfirmasi.

---

### Akun Demo & Kredensial untuk Video:
Untuk kelancaran presentasi di dalam video, gunakan data akun berikut:
| No | Aktor (Role) | Email | Password | Kegunaan |
| :--- | :--- | :--- | :--- | :--- |
| 1 | **Admin (Staf)** | `admin@gmail.com` | `admin123` | Demonstrasi fitur proteksi & input password atasan. |
| 2 | **Owner (Atasan)** | `atasan@gmail.com` | `owner123` | Demonstrasi bypass validasi / hak akses langsung. |
| 3 | **Password Atasan** | — | `123` | Password modal SweetAlert yang diinput oleh Admin. |
