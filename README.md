# 🛏️ MalasBaca - Digital Library Management System

![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat&logo=bootstrap&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green.svg)

**MalasBaca** adalah sistem manajemen perpustakaan digital berbasis web yang memungkinkan pengelolaan koleksi buku, user management, dan sistem favorit dengan interface yang modern dan responsif.

---

## 📸 Screenshots

### 🏠 Halaman Beranda

![Beranda](screenshots/home.png)

### 📚 Dashboard User

![Dashboard User](screenshots/user-dashboard.png)

### 👨‍💼 Dashboard Admin

![Dashboard Admin](screenshots/admin-dashboard.png)

### 📊 Laporan Aktivitas User

![User Activity Report](screenshots/user-activity-report.png)

### 🔐 Login & Registrasi

![Login](screenshots/login.png)
![Register dengan CAPTCHA](screenshots/register.png)

---

## ✨ Fitur Utama

### 🔐 Autentikasi & Keamanan

- ✅ **Login/Register** dengan validasi keamanan
- ✅ **CAPTCHA** dengan coretan pada registrasi
- ✅ **Role-based Access Control** (Admin & Member)
- ✅ **Prepared Statements** untuk mencegah SQL Injection
- ✅ **Input Validation & Sanitization** di semua endpoint
- ✅ **File Upload Validation** (tipe file & ukuran)
- ✅ **Session Management** yang aman

### 📚 Manajemen Buku (Admin)

- ✅ **CRUD Buku** lengkap dengan upload cover & PDF
- ✅ **Manajemen Kategori** buku
- ✅ **Validasi file** (max 5MB gambar, 50MB PDF)
- ✅ **Sanitasi filename** untuk keamanan
- ✅ **Preview cover & PDF** sebelum upload

### 👥 Manajemen User (Admin)

- ✅ **CRUD User** dengan validasi lengkap
- ✅ **Role Management** (Admin/Member)
- ✅ **Password Reset** dengan enkripsi MD5
- ✅ **User Activity Tracking**
- ✅ **Bulk User Operations**

### ⭐ Sistem Favorit (Member)

- ✅ **Tambah/Hapus Favorit** dengan validasi duplikasi
- ✅ **Halaman Favorit** khusus per user
- ✅ **Tracking waktu** favorit ditambahkan
- ✅ **View PDF** langsung dari browser

### 📊 Reporting & Analytics (Admin)

- ✅ **Dashboard Analytics** dengan statistik real-time
- ✅ **Laporan Aktivitas User** (Top 10 user paling aktif)
- ✅ **User Terbaru & Tidak Aktif**
- ✅ **Distribusi Role** (Admin vs Member)
- ✅ **Export ke Excel** (CSV format)
- ✅ **Rata-rata favorit per user**

### 🎨 User Experience

- ✅ **Responsive Design** (Mobile, Tablet, Desktop)
- ✅ **Search & Filter** buku berdasarkan kategori
- ✅ **Buku Populer** berdasarkan jumlah favorit
- ✅ **Detail buku** di sidebar dengan smooth scroll
- ✅ **Flash Messages** untuk feedback user
- ✅ **Loading states & validation feedback**

---

## 🛠️ Tech Stack

| Component         | Technology                    |
| ----------------- | ----------------------------- |
| **Backend**       | PHP 7.4+ (Native)             |
| **Database**      | MySQL 5.7+                    |
| **Frontend**      | HTML5, CSS3, JavaScript (ES6) |
| **Framework CSS** | Bootstrap 5.3                 |
| **Icons**         | Font Awesome 6.4              |
| **Fonts**         | Google Fonts (Poppins)        |
| **Server**        | Apache (XAMPP)                |

---

## 📁 Struktur Folder

```
responsi/
├── admin/                      # Area Admin
│   ├── layout/                 # Layout admin (header, footer)
│   ├── buku.php               # CRUD Buku
│   ├── kategori.php           # CRUD Kategori
│   ├── users.php              # Manajemen User
│   ├── index.php              # Dashboard & Reporting
│   └── export_user_activity.php # Export Excel
├── config/
│   └── database.php           # Konfigurasi database
├── layout/                     # Layout public/user
│   ├── header.php
│   ├── footer.php
│   └── sidebar.php
├── uploads/
│   ├── covers/                # Cover buku
│   └── pdfs/                  # File PDF buku
├── user/                       # Area Member
│   ├── index.php              # Dashboard user
│   └── favorites.php          # Halaman favorit
├── auth_check.php             # Middleware auth
├── captcha.php                # Generator CAPTCHA
├── functions.php              # Helper functions
├── index.php                  # Homepage public
├── login.php                  # Halaman login
├── register.php               # Halaman registrasi
├── logout.php                 # Logout handler
└── style.css                  # Global styles
```

---

## 🚀 Instalasi

### Prasyarat

- **XAMPP** atau web server dengan PHP 7.4+ dan MySQL
- **Web Browser** modern (Chrome, Firefox, Edge)

### Langkah Instalasi

1. **Clone Repository**

   ```bash
   git clone https://github.com/username/malasbaca.git
   cd malasbaca
   ```

2. **Setup Database**

   - Buka **phpMyAdmin** (`http://localhost/phpmyadmin`)
   - Buat database baru: `db_perpus`
   - Import file SQL (jika tersedia) atau buat tabel manual:

   ```sql
   CREATE TABLE users (
       id INT PRIMARY KEY AUTO_INCREMENT,
       username VARCHAR(50) UNIQUE NOT NULL,
       email VARCHAR(100) UNIQUE NOT NULL,
       password VARCHAR(255) NOT NULL,
       role ENUM('admin', 'member') DEFAULT 'member'
   );

   CREATE TABLE categories (
       id INT PRIMARY KEY AUTO_INCREMENT,
       name VARCHAR(100) NOT NULL
   );

   CREATE TABLE books (
       id INT PRIMARY KEY AUTO_INCREMENT,
       title VARCHAR(255) NOT NULL,
       author VARCHAR(100) NOT NULL,
       category_id INT,
       description TEXT,
       cover_image VARCHAR(255),
       pdf_file VARCHAR(255),
       FOREIGN KEY (category_id) REFERENCES categories(id)
   );

   CREATE TABLE favorites (
       id INT PRIMARY KEY AUTO_INCREMENT,
       user_id INT NOT NULL,
       book_id INT NOT NULL,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       FOREIGN KEY (user_id) REFERENCES users(id),
       FOREIGN KEY (book_id) REFERENCES books(id),
       UNIQUE KEY unique_favorite (user_id, book_id)
   );
   ```

3. **Konfigurasi Database**

   - Edit `config/database.php` sesuai konfigurasi lokal Anda:

   ```php
   $host = "localhost";
   $user = "root";
   $pass = "";
   $db   = "db_perpus";
   ```

4. **Pindahkan ke htdocs**

   ```bash
   # Copy folder ke htdocs XAMPP
   cp -r malasbaca C:/xampp/htdocs/
   ```

5. **Set Permissions**

   - Pastikan folder `uploads/` memiliki write permission:

   ```bash
   chmod -R 755 uploads/
   ```

6. **Akses Aplikasi**
   - Buka browser: `http://localhost/malasbaca`

---

## 👤 Default Credentials

### Admin Account

```
Username: admin
Password: admin123
```

### Member Account (Testing)

```
Username: member
Password: member123
```

> ⚠️ **Penting:** Ubah password default setelah instalasi pertama!

---

## 📖 Cara Penggunaan

### Untuk Admin

1. **Login** dengan akun admin
2. **Dashboard** menampilkan:
   - Statistik total buku, kategori, user, favorit
   - Laporan aktivitas user
   - User paling aktif (Top 10)
   - Export laporan ke Excel
3. **Kelola Buku:**
   - Tambah buku baru (cover + PDF)
   - Edit/hapus buku
   - Atur kategori
4. **Kelola User:**
   - Tambah user manual
   - Edit role & data user
   - Hapus user (dengan proteksi self-delete)

### Untuk Member

1. **Register** akun baru dengan CAPTCHA
2. **Login** dengan akun member
3. **Browse Buku:**
   - Lihat koleksi buku terbaru
   - Filter berdasarkan kategori
   - Search buku by judul/penulis
4. **Favorit:**
   - Klik buku → Tambah ke favorit
   - Akses halaman "My Favorites"
   - Baca PDF langsung di browser

---

## 🔒 Fitur Keamanan

| Fitur                 | Implementasi                            |
| --------------------- | --------------------------------------- |
| **SQL Injection**     | Prepared Statements di semua query      |
| **XSS Attack**        | `htmlspecialchars()` & `strip_tags()`   |
| **CSRF**              | Session-based validation                |
| **File Upload**       | Validasi MIME type & ukuran file        |
| **Path Traversal**    | `basename()` & sanitasi filename        |
| **Password**          | MD5 hashing (dapat diupgrade ke bcrypt) |
| **Session Hijacking** | `session_regenerate_id()` on login      |
| **Input Validation**  | Whitelist & length constraints          |

---

## 📊 Fitur Export

Export data aktivitas user ke **Excel (CSV)**:

- Format UTF-8 dengan BOM (Excel Indonesia compatible)
- Include ringkasan statistik
- Daftar user paling aktif
- User terbaru & belum aktif
- Timestamp export & nama admin

---

## 🎯 Future Enhancements

- [ ] Upgrade password hashing ke **bcrypt/argon2**
- [ ] Add **email verification** saat registrasi
- [ ] Implement **book rating system**
- [ ] Add **comment/review** pada buku
- [ ] **Multi-language support** (ID/EN)
- [ ] **Dark mode** toggle
- [ ] **Advanced search** dengan filter kompleks
- [ ] **PDF reader** terintegrasi di website
- [ ] **Bookmark** halaman PDF terakhir dibaca
- [ ] **Notification system** untuk buku baru
- [ ] **REST API** untuk mobile app
- [ ] **QR Code** untuk sharing buku

---

## 🤝 Contributing

Contributions are welcome! Silakan:

1. Fork repository ini
2. Buat branch fitur baru (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

---

## 📝 License

Distributed under the **MIT License**. See `LICENSE` for more information.

---

## 👨‍💻 Developer

Developed with ☕ by **[Your Name]**

- GitHub: [@yourusername](https://github.com/yourusername)
- Email: your.email@example.com

---

## 🙏 Acknowledgments

- [Bootstrap](https://getbootstrap.com/) - Frontend framework
- [Font Awesome](https://fontawesome.com/) - Icon library
- [Google Fonts](https://fonts.google.com/) - Typography
- [PHP Manual](https://www.php.net/manual/en/) - Documentation
- [MySQL Documentation](https://dev.mysql.com/doc/) - Database reference

---

## 📞 Support

Jika ada pertanyaan atau menemukan bug, silakan buat [issue](https://github.com/username/malasbaca/issues) di repository ini.

---

<div align="center">
  
**⭐ Star this repository if you find it helpful!**

Made with ❤️ for book lovers who are too lazy to go to the library 🛏️

</div>
