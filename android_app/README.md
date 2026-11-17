# 📱 CBT Kiosk Browser — SMKN 1 Kemlagi (Android Kiosk App)

Aplikasi Android Kiosk khusus ujian CBT di **SMK Negeri 1 Kemlagi** untuk mencegah siswa membuka aplikasi lain, membagi layar (*split screen*), menggunakan *floating apps* (kalkulator AI / Google Lens), dan mengambil tangkapan layar (*screenshot*).

---

## 🔒 Fitur Keamanan Aplikasi

1. **Anti-Screenshot & Anti-Screen Recording (`FLAG_SECURE`)**
   - Layar tidak bisa di-screenshot atau direkam dengan aplikasi screen recorder.
   - Layar menjadi hitam (*blank*) jika dilihat dari menu *Recent Apps*.

2. **Screen Pinning / Lock Task Mode (`startLockTask()`)**
   - Aplikasi otomatis terkunci di layar depan (*kiosk mode*).
   - Tombol *Home*, *Recent Apps*, dan *Back* dinonaktifkan sehingga siswa tidak bisa keluar dari aplikasi tanpa izin pengawas.

3. **Blokir Split Screen & Multi-Window**
   - `android:resizeableActivity="false"` mencegah fitur *split screen* dan *pop-up view* bawaan Android.

4. **Identifikasi Kiosk Resmi (Custom User-Agent)**
   - Mengirimkan *Header User-Agent*: `CBT-Kiosk-Kemlagi/1.0` ke server Moodle & Exam-Admin.
   - Moodle dapat memvalidasi bahwa hanya siswa yang memakai aplikasi ini yang diizinkan mengakses kuis.

5. **Menu Keluar Khusus Pengawas (Protected Exit Dialog)**
   - Siswa tidak bisa keluar sembarangan.
   - Untuk keluar aplikasi, pengawas mengetuk ikon gembok di pojok kanan atas atau menekan tombol kembali 5 kali, lalu memasukkan PIN Pengawas (default: `PROKTOR123`).

---

## 🛠️ Cara Build APK Menggunakan Android Studio (5 Menit)

1. Buka **Android Studio** di komputer Anda.
2. Pilih **File &rsaquo; Open**, lalu arahkan ke folder `android_app/`.
3. Tunggu proses *Gradle Sync* selesai.
4. (Opsional) Sesuaikan URL server ujian di file `MainActivity.java`:
   ```java
   private static final String DEFAULT_EXAM_URL = "https://exam.smkn1kemlagi.sch.id";
   ```
5. Klik menu **Build &rsaquo; Build Bundle(s) / APK(s) &rsaquo; Build APK(s)**.
6. File APK siap digunakan di folder `app/build/outputs/apk/release/` atau `debug/`.
7. Bagikan file `.apk` ke siswa melalui Google Drive / Flashdisk / Portal Sekolah.

---

## 💡 Opsi Alternatif Instan (Tanpa Build APK)

Jika Anda ingin langsung menggunakan aplikasi yang sudah ada di Google Play Store tanpa perlu melakukan compile sendiri di Android Studio:

1. Minta siswa mengunduh aplikasi **Exambro** atau **Safe Exam Browser (SEB)** dari Play Store:
   - Pencarian: `"Exambro"` di Google Play Store (oleh pembuat terpercaya seperti *CV. Jasa Koding Mandiri* atau *Exambro Kemdikbud*).
2. Di dalam Exambro, masukkan URL Ujian:
   `https://exam.smkn1kemlagi.sch.id`
3. Exambro secara otomatis mengunci layar siswa, mematikan notifikasi, dan mencegah *split screen*.
