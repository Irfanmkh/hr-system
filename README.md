# HR Management System (Full Stack Technical Test)

Aplikasi Web HR System yang dibangun dengan arsitektur terpisah antara Frontend (Nuxt 3) dan Backend (Laravel REST API), menggunakan PostgreSQL sebagai database utama serta menerapkan Role-Based Access Control (RBAC).

---

## Tech Stack & Arsitektur

- **Frontend:** Nuxt 3 (Vue 3, Tailwind CSS, Chart.js)
- **Backend:** Laravel (REST API, Sanctum, Form Request Validation)
- **Database:** PostgreSQL (Supabase)
- **Deployment:** Shared Hosting cPanel (Node.js App & PHP Laravel)

---

## Bagian 1: Database Design & ERD

Sistem menggunakan database PostgreSQL dengan struktur relasi sebagai berikut:

- `roles` (1) ke (N) `users`
- `users` (1) ke (1) `candidates`
- `job_lists` (1) ke (N) `applications`
- `candidates` (1) ke (N) `applications`

### Skema Tabel Utama:
* **`roles`**: `id`, `name` (HR Admin, HR Staff, Interviewer, Candidate)
* **`users`**: `id`, `name`, `email`, `password`, `role_id`, `created_at`, `updated_at`
* **`candidates`**: `id`, `full_name`, `user_id`, `email`, `phone`, `address`, `birth_date`, `status`
* **`job_lists`**: `id`, `title`, `department`, `description`, `is_active`
* **`applications`**: `id`, `candidate_id`, `job_list_id`, `apply_date`, `status`

---

## Bagian 2: API Endpoints

Semua respon API mengembalikan format JSON standar beserta HTTP Status Code yang sesuai (200, 201, 401, 403, 422).

### Authentication
- `POST /api/login` - Otentikasi pengguna
- `POST /api/logout` - Menghapus sesi / token

### Jobs Management
- `GET /api/jobs` - Mengambil daftar lowongan
- `POST /api/jobs` - Menambah lowongan baru
- `PUT /api/jobs/{id}` - Memperbarui data lowongan
- `DELETE /api/jobs/{id}` - Menghapus lowongan

### Candidates Management
- `GET /api/candidates` - Daftar kandidat (pencarian, paginasi, filter status, & pengurutan)
- `POST /api/candidates` - Menambah kandidat
- `GET /api/candidates/{id}` - Detail kandidat & riwayat lamaran
- `PUT /api/candidates/{id}` - Memperbarui data kandidat

### Applications Management
- `POST /api/applications` - Mengirim lamaran
- `GET /api/applications` - Mengambil daftar lamaran

---

## Bagian 4 & 5: Keamanan & Hak Akses (RBAC)

### Hak Akses

- **HR Admin:** Akses penuh ke seluruh fitur dan endpoint.
- **HR Staff:** Pengelolaan CRUD kandidat dan lowongan pekerjaan.
- **Interviewer:** Hanya dapat melihat (Read-only) data kandidat.
- **Candidate:** Hanya memiliki hak akses ke data miliknya sendiri.

### Penerapan Keamanan
* **Password Hashing:** Menggunakan algoritma Bcrypt bawaan Laravel.
* **Proteksi SQL Injection:** Seluruh kueri database menggunakan Eloquent ORM dan Query Builder dengan PDO Parameter Binding.
* **Validasi Data:** Semua request masuk divalidasi menggunakan Laravel FormRequest sebelum diproses controller.
* **Otorisasi Middleware:** Pemeriksaan token dan role dilakukan menggunakan Middleware kustom serta Laravel Gates/Policies.
* **CORS:** Akses API dibatasi hanya untuk origin frontend resmi (`https://hris.imaka.my.id`).

---

## Bagian 3: Fitur Frontend

- **Desain Responsif:** Tampilan menyesuaikan perangkat desktop dan mobile.
- **Halaman Login:** Terhubung langsung dengan endpoint otentikasi API.
- **Dashboard:** Menampilkan ringkasan total kandidat, lowongan aktif, serta grafik sebaran kandidat berdasarkan status.
- **Daftar Kandidat:** Dilengkapi fitur pencarian, filter status, paginasi, dan pengurutan data.
- **Detail Kandidat:** Menampilkan biodata lengkap beserta riwayat pekerjaan yang dilamar.
- **Kelola Lowongan:** Fitur CRUD untuk manajemen data pekerjaan.
- **Dark Mode:** Memiliki fitur dark/light mode
- menyesuaikan tampilan dengan key value yang diberikan

---

## Panduan Setup

### Backend (Laravel)
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed

## Data Seeder
Akun Pengujian (Seeder Data)

+---------------+--------------------+-------------+------------------------------------+
| Role          | Email              | Password    | Hak Akses / Scope                  |
+---------------+--------------------+-------------+------------------------------------+
| hr admin      | admin@gmail.com    | password123 | Full Access (Semua Fitur)          |
| hr staff      | staff@gmail.com    | password123 | CRUD Candidate & Job Management    |
| interviewer   | interviewer@gmail.com | password123 | Read-only Candidate & Job       |
| candidate     | candidate@gmail.com| password123 | Mengakses data milik sendiri saja  |
+---------------+--------------------+-------------+------------------------------------+
