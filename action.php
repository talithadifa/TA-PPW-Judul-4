<?php
session_start();

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: index.php");
    exit();
}

function generateNewId($kontak) {
    if (empty($kontak)) return 1;
    return max(array_keys($kontak)) + 1;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $action = $_POST['action'] ?? '';
    $kontak = $_SESSION['kontak'] ?? [];
    $errors = [];
    $id = $_POST['id'] ?? null;

    if ($action === 'add' || $action === 'update') {

        $nama = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telepon = trim($_POST['telepon'] ?? '');
        $kategori = trim($_POST['kategori'] ?? '');

        if (empty($nama)) {
            $errors[] = "Nama lengkap harus diisi.";
        } elseif (!preg_match("/^[a-zA-Z\s.'-]+$/", $nama)) {
            $errors[] = "Nama hanya boleh mengandung huruf dan karakter (spasi, ., ', -).";
        }

        if (empty($telepon)) {
            $errors[] = "Nomor telepon harus diisi.";
        } elseif (!preg_match("/^[0-9]{10,13}$/", $telepon)) {
            $errors[] = "Nomor telepon harus 10–13 digit angka.";
        }

        if (empty($email)) {
             $errors[] = "Email harus diisi.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Format email tidak valid.";
        }

        if (empty($kategori)) {
            $errors[] = "Kategori harus dipilih.";
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $redirect_url = "dashboard.php";
            if ($action === 'update') $redirect_url .= "?action=edit&id=$id";
            header("Location: $redirect_url");
            exit();
        }

        $new_data = [
            'nama' => htmlspecialchars($nama),
            'email' => htmlspecialchars($email),
            'telepon' => htmlspecialchars($telepon),
            'kategori' => htmlspecialchars($kategori)
        ];

        if ($action === 'add') {
            $new_id = generateNewId($kontak);
            $kontak[$new_id] = $new_data;
            $_SESSION['message'] = "Kontak berhasil ditambahkan!";
        } elseif ($action === 'update' && isset($kontak[$id])) {
            $kontak[$id] = $new_data;
            $_SESSION['message'] = "Kontak berhasil diperbarui!";
        }
    }


    elseif ($action === 'delete') {
        if (isset($kontak[$id])) {
            unset($kontak[$id]);
            $_SESSION['message'] = "Kontak berhasil dihapus!";
        } else {
             $_SESSION['errors'] = ["Gagal menghapus: Data tidak ditemukan."];
        }
    }

    $_SESSION['kontak'] = $kontak;
}

header("Location: dashboard.php");
exit();
?>