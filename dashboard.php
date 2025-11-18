<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: index.php");
    exit();
}

$edit_data = null;
$edit_id = null;

if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $edit_id = $_GET['id'];
    $kontak = $_SESSION['kontak'] ?? [];

    if (isset($kontak[$edit_id])) {
        $edit_data = $kontak[$edit_id];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>SIMAKO - Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>

<style>
    .animate-fadeInUp { animation: fadeInUp 0.5s ease-out forwards; }
    @keyframes fadeInUp {
        0% { opacity: 0; transform: translateY(20px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #1f2937; }
    ::-webkit-scrollbar-thumb { background: #06b6d4; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #0891b2; }
</style>
</head>

<body class="bg-gray-900 text-gray-300">

<nav class="bg-gray-800 shadow-lg border-b border-gray-700 sticky top-0 z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <div class="flex-shrink-0 flex items-center">
                <h1 class="text-2xl font-extrabold text-white">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-500">SIMAKO</span>
                </h1>
            </div>

            <div class="flex items-center">
                <form method="POST" action="dashboard.php">
                    <button type="submit" name="logout"
                            class="bg-red-600 hover:bg-red-700 text-white font-medium py-1 px-3 rounded-md text-sm">
                        Logout
                    </button>
                </form>
            </div>

        </div>
    </div>
</nav>

<main class="max-w-7xl mx-auto p-8 lg:p-10">
    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-4xl font-extrabold text-white">Dashboard Kontak</h1>

        <p class="text-sm font-medium text-gray-500">
            Hari ini: <?php echo date("d-m-Y H:i:s"); ?>
        </p>
    </div>

    <?php
    $message = $_SESSION['message'] ?? null;
    unset($_SESSION['message']);
    if ($message):
    ?>
        <div id="notif" class="p-4 mb-6 text-sm bg-cyan-900 border-l-4 border-cyan-500 text-cyan-200 rounded-lg shadow-md animate-fadeInUp transition duration-700">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-1 bg-gray-800 p-6 rounded-xl shadow-xl border border-gray-700 animate-fadeInUp">
            <h2 class="text-2xl font-semibold mb-5 text-white border-b border-gray-700 pb-3">
                <?php echo $edit_data ? 'Edit Kontak' : 'Tambah Kontak Baru'; ?>
            </h2>
            <?php if (isset($_SESSION['errors'])): ?>
                <div class="p-3 mb-4 text-sm font-medium text-red-300 bg-red-900 rounded-lg border border-red-700">
                    <ul class="list-disc list-inside">
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>

           <form action="action.php" method="POST" class="space-y-4">
    <input type="hidden" name="action" value="<?php echo $edit_data ? 'update' : 'add'; ?>">

    <?php if ($edit_data): ?>
        <input type="hidden" name="id" value="<?php echo $edit_id; ?>">
    <?php endif; ?>

    <div>
        <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
        <input type="text" name="nama" required
               placeholder="Masukkan Nama Lengkap"
               value="<?php echo htmlspecialchars($edit_data['nama'] ?? ''); ?>"
               class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Nomor Telepon</label>
        <input type="text" name="telepon" required pattern="[0-9]{10,13}"
               placeholder="Masukkan Nomor Telepon"
               title="Nomor telepon harus 10–13 digit angka"
               value="<?php echo htmlspecialchars($edit_data['telepon'] ?? ''); ?>"
               class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Email</label>
        <input type="email" name="email" required
               placeholder="Masukkan Email"
               value="<?php echo htmlspecialchars($edit_data['email'] ?? ''); ?>"
               class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Kategori</label>
        <select name="kategori" required
                class="w-full bg-gray-700 border border-gray-600 text-white px-3 py-2 rounded-md">
            <option disabled selected value="">Pilih Kategori</option>
            <?php
            $opsi = ['Keluarga', 'Teman', 'Rekan Kerja', 'Bisnis', 'Lainnya'];
            foreach ($opsi as $o):
            ?>
                <option value="<?php echo $o; ?>"
                    <?php if (($edit_data['kategori'] ?? '') == $o) echo 'selected'; ?>>
                    <?php echo $o; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="flex space-x-3 pt-3">
        <button class="flex-grow py-2.5 rounded-md bg-gradient-to-r from-cyan-600 to-blue-600 text-white font-bold">
            <?php echo $edit_data ? 'Update Kontak' : 'Simpan Kontak'; ?>
        </button>

        <?php if ($edit_data): ?>
            <a href="dashboard.php" class="py-2.5 px-4 bg-gray-700 rounded-md text-gray-300">
                Batal
            </a>
        <?php endif; ?>
    </div>

</form>

        </div>
        <div class="lg:col-span-2 bg-gray-800 rounded-xl shadow-xl border border-gray-700 animate-fadeInUp">

            <div class="p-6 border-b border-gray-700 flex justify-between">
                <h2 class="text-2xl font-semibold text-white">Daftar Kontak</h2>

                <p class="text-white font-bold text-lg">
                    Total: <?php echo count($_SESSION['kontak'] ?? []); ?>
                </p>
            </div>

            <?php
            $kontak = $_SESSION['kontak'] ?? [];
            ?>

            <?php if (empty($kontak)): ?>
                <div class="text-center p-16 text-gray-500">
                    <p>Belum ada kontak</p>
                </div>

            <?php else: ?>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">

                        <thead class="bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-bold">Telepon</th>
                                <th class="px-6 py-3 text-left text-xs font-bold">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-bold">Kategori</th>
                                <th class="px-6 py-3 text-center text-xs font-bold">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-700">
                            <?php foreach ($kontak as $id => $data): ?>
                                <tr>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($data['nama']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($data['telepon']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($data['email']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($data['kategori']); ?></td>

                                    <td class="px-6 py-4 text-center">
                                        <a href="dashboard.php?action=edit&id=<?php echo $id; ?>"
                                           class="text-cyan-400 hover:text-cyan-300">Edit</a>

                                        <form action="action.php" method="POST" class="inline-block"
                                              onsubmit="return confirm('Hapus kontak ini?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                                            <button class="text-red-500 hover:text-red-400 ml-2">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>

            <?php endif; ?>

        </div>
    </div>

    <footer class="text-center mt-10 pt-6 border-t border-gray-700">
        <p class="text-sm text-gray-500">
            SIMAKO © <?php echo date("Y"); ?>. All rights reserved.
        </p>
    </footer>

</main>

<script>
    setTimeout(() => {
        const notif = document.getElementById("notif");
        if (notif) {
            notif.style.opacity = "0";
            notif.style.transition = "opacity 0.7s ease";
            setTimeout(() => notif.remove(), 700);
        }
    }, 3000);
</script>

</body>
</html>
