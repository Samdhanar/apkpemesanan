### Catatan pembangunan aplikasi

# Aplikasi Pemesanan Cafe Atau Restoran 
sebuah aplikasi yang dapat digunakan untuk pemesanan minuman dan makanan di sebuah restoran  dan sebagainya. menggunakan bahasa pemrogaman PHP dan UI/UX dengan BOOTSTRAP

# USER DAN FITUR-FITURNYA:
## Pemilik atau Admin (1)
    - Melihat laporan 
    - Manambahkan/mengubah/menghapus menu makanan/minuman  dan harga
    - Menambahkan/menghapus user

## Kasir (2)
    - Melihat Harga dan total Harga pesanan
    - Konfirmasi pembayaran 
    - Struk/bukti pembayaran 

## Pelayan (4)
    - Menerima Pesanan
    - Konfirmasi terima pesanan Tidak bisa mengubah pesanan
    - Mengubah status pesanan "siap saji"

# Vidio 6

# Jalan Progam ( Algoritma )
    1. Inisialisasi Sistem
        - Load halaman login.
        - User memilih role (Admin, Kasir, Pelayan, Dapur) atau otomatis diarahkan sesuai data login.
        - Autentikasi username & password dari database.
        - Jika login gagal → tampilkan pesan error & kembali ke login.
        - Jika login sukses → arahkan ke dashboard sesuai role

    2. Fungsi untuk Admin
        A. Melihat Laporan
            - Admin klik menu Laporan.
            - Sistem menampilkan filter (tanggal, bulan, tahun, kategori menu).
            - Query database untuk menampilkan data penjualan & total pendapatan.
            - Admin dapat mengekspor laporan ke PDF/Excel ( Opsional ).

        B. CRUD Menu
            - Admin Bisa Kelola Menu.
                  Pilih aksi:
                    Tambah Menu: Masukkan nama, kategori, harga, gambar → Simpan.
                    Edit Menu: Pilih menu → ubah data → Simpan.
                    Hapus Menu: Pilih menu → konfirmasi hapus → Hapus dari database.

        C. Kelola User
            - Admin klik Kelola User.
            - Sistem menampilkan daftar user.
                Pilih aksi:
                    Tambah User: Isi nama, username, password, role → Simpan.
                    Hapus User: Pilih user → konfirmasi hapus.

    3. Fungsi untuk Kasir
        A. Melihat Harga & Total Harga Pesanan
            - Kasir klik Menu Makanan & Minuman.
            - Sistem menampilkan daftar menu + harga dari database.
            - Kasir memilih menu + jumlah pesanan → klik Tambah ke Pesanan.
            - Sistem menghitung total harga otomatis.
            - Simpan pesanan → status awal Menunggu Dapur.Kasir pilih Daftar Pesanan.
            - Sistem menampilkan semua pesanan yang belum dibayar, lengkap dengan item & total harga.

        B. Konfirmasi Pembayaran
            - Input jumlah uang bayar (jika cash) → hitung kembalian otomatis.
            - Update status pesanan jadi Lunas.

        C. Cetak Struk
            - Setelah pembayaran dikonfirmasi → cetak struk berisi:
                Nama café
                Tanggal & jam
                Daftar menu & harga
                Total harga, uang bayar, dan kembalian

    5. Fungsi untuk Dapur
        A. Menerima Pesanan
            - Dapur melihat daftar pesanan dengan status Menunggu Dapur.
            - Klik Terima Pesanan → sistem mengunci pesanan agar tidak bisa diubah.
