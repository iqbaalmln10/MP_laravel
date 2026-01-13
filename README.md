# Proman
# Cara Menjalankan

1. php artisan serve : perintah untuk menjalankan backend dan html utama 
2. npm run dev : perintah untuk menjalankan tailwind css ,js (frontend)
3. ketika menjalankan web harus melakukan kedua perintah diatas 
4. url yang benar [http://localhost:8000](http://localhost:8000/)

# Catatn TeachStack

Berdasarkan percakapan kita dan kode yang telah kita susun bersama, Anda sedang membangun sebuah aplikasi modern dengan *tech stack* yang sangat efisien dan mutakhir di ekosistem Laravel.

Berikut adalah daftar teknologi dan komponen yang Anda gunakan di dalam proyek **mp_laravel** ini:

### 1. Framework Utama: **Laravel 12+**

Anda menggunakan versi terbaru Laravel yang memberikan struktur folder lebih ramping. Anda memanfaatkannya untuk:

- **Eloquent ORM**: Mengelola relasi database antara `User`, `Project`, dan `Task`.
- **Routing**: Mengatur alur halaman dari Dashboard hingga rencana menu Kalender besok.
- **Authentication**: Mengamankan data sehingga setiap user hanya bisa melihat proyek miliknya sendiri.

### 2. Frontend & Interaktivitas: **TALL Stack (Variasi)**

Anda menggunakan kombinasi teknologi yang memungkinkan aplikasi terasa sangat cepat (tanpa *reload* halaman):

- **Livewire 3**: Otak di balik komponen Anda. Semua logika seperti `addTask` dan `deleteTask` berjalan di sisi server tapi terasa instan di mata user.
- **Livewire Volt**: Ini adalah fitur canggih yang kita gunakan. Anda menulis kode PHP (logika) dan HTML (tampilan) dalam satu file `.blade.php`. Ini membuat pengembangan jauh lebih cepat.
- **Alpine.js**: Digunakan untuk interaksi sisi klien, seperti membuka/menutup modal, mengelola state `openDetail`, dan menghubungkan ID proyek ke Livewire menggunakan `$wire.entangle`.
- **Tailwind CSS**: Semua desain tombol, modal yang transparan (backdrop-blur), grid, dan animasi *hover* menggunakan kelas-kelas Tailwind.

### 3. Database: **MySQL / MariaDB**

Tempat penyimpanan data permanen Anda dengan struktur:

- **Table `projects`**: Menyimpan `title`, `description`, dan `status` (`pending`, `on_progress`, `completed`).
- **Table `tasks`**: Menyimpan detail pekerjaan yang terhubung ke setiap proyek.

### 4. Konsep Arsitektur: **Event-Driven UI**

Ini adalah bagian yang paling menarik dari proyek Anda. Anda menggunakan sistem **Events & Listeners**:

- **`$dispatch`**: Mengirim sinyal (misalnya: `project-updated`).
- **`on([...])`**: Menangkap sinyal tersebut di komponen lain (misalnya komponen `index` menangkap sinyal dari `task-manager`) untuk memperbarui tampilan secara otomatis.

---

### Ringkasan Fitur yang Sudah Ada:

- **CRUD Proyek**: Membuat, membaca, mengedit, dan menghapus proyek.
- **Manajemen Tugas (Task Manager)**: Menambah dan mencentang tugas di dalam modal.
- **Otomatisasi Status**: Logika cerdas yang mengubah status proyek berdasarkan jumlah tugas yang selesai.
- **Filter Reaktif**: Menyaring daftar proyek berdasarkan status secara instan.

Dengan kombinasi di atas, proyek **mp_laravel** Anda bukan lagi sekadar website biasa, melainkan sebuah **Single Page Application (SPA)** yang sangat responsif.

Apakah ada bagian dari teknologi ini yang ingin Anda pelajari lebih dalam sebelum kita lanjut ke menu Kalender besok?