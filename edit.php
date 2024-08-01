<?php
session_start(); // Memulai sesi PHP untuk menggunakan variabel session

include "koneksi.php"; // Menyertakan file koneksi.php yang berisi kode untuk menghubungkan ke database

// Mengecek apakah form telah disubmit
if (isset($_POST['submit'])) {
    $id = $_GET['id']; // Mengambil ID barang dari URL

    // Menyaring dan membersihkan input
    $nama = mysqli_real_escape_string($db, $_POST['nama_barang']);
    $harga = mysqli_real_escape_string($db, $_POST['harga_barang']);
    $stok = mysqli_real_escape_string($db, $_POST['stok_barang']);

    // Menyusun query SQL menggunakan prepared statement
    $sql = "UPDATE barang SET nama_barang = ?, harga_barang = ?, stok_barang = ? WHERE id_barang = ?";
    if ($stmt = mysqli_prepare($db, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssii", $nama, $harga, $stok, $id);

        // Menjalankan query SQL pada database
        if (mysqli_stmt_execute($stmt)) {
            // Jika berhasil, set pesan sukses dalam session dan arahkan ke halaman lihat.php
            $_SESSION['pesan'] = "Berhasil mengedit barang";
            header("Location: lihat.php"); // Arahkan pengguna ke halaman lihat.php
            exit(); // Hentikan eksekusi script setelah redirect
        } else {
            // Jika gagal, tampilkan pesan error
            die("Gagal mengedit barang: " . mysqli_error($db));
        }
        mysqli_stmt_close($stmt);
    } else {
        // Jika gagal mempersiapkan statement, tampilkan pesan error
        die("Gagal mempersiapkan query: " . mysqli_error($db));
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Barang</title>
</head>
<body>
    <h2>Form Edit Barang</h2>
    <!-- Form untuk mengedit barang -->
    <form action="edit.php?id=<?php echo htmlspecialchars($_GET['id']) ?>" method="post">
        <?php
        // Mengecek apakah ID barang ada di URL
        if (isset($_GET['id'])) {
            $id = $_GET['id']; // Mengambil ID barang dari URL

            // Menyusun query SQL untuk mengambil data barang berdasarkan ID menggunakan prepared statement
            $sql = "SELECT * FROM barang WHERE id_barang = ?";
            if ($stmt = mysqli_prepare($db, $sql)) {
                mysqli_stmt_bind_param($stmt, "i", $id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                // Mengecek apakah query mengembalikan hasil data
                if ($result->num_rows > 0) {
                    $data = $result->fetch_assoc(); // Mengambil data hasil query
        ?>
                    <!-- Input form dengan nilai awal diisi dengan data barang yang ada -->
                    <label for="nama_barang">Nama Barang:</label><br>
                    <input type="text" id="nama_barang" name="nama_barang" value="<?php echo htmlspecialchars($data['nama_barang']) ?>" required><br><br>
                    <label for="harga_barang">Harga Barang:</label><br>
                    <input type="number" id="harga_barang" name="harga_barang" value="<?php echo htmlspecialchars($data['harga_barang']) ?>" required><br><br>
                    <label for="stok_barang">Stok Barang:</label><br>
                    <input type="number" id="stok_barang" name="stok_barang" value="<?php echo htmlspecialchars($data['stok_barang']) ?>" required><br><br>
        <?php
                } else {
                    echo "Barang tidak ditemukan.";
                }
                mysqli_stmt_close($stmt);
            } else {
                echo "Gagal mempersiapkan query: " . mysqli_error($db);
            }
        } else {
            echo "ID barang tidak ditemukan.";
        }
        ?>
        <!-- Tombol submit untuk mengirim data ke server -->
        <input type="submit" value="Edit Barang" name="submit">
        <!-- Link untuk kembali ke halaman lihat.php -->
        <a href="lihat.php">Kembali</a>
    </form>
</body>
</html>
