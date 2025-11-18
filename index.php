<?php
session_start();

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}

$login_error = '';
$remembered_user = $_COOKIE['simako_remember'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = htmlspecialchars($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === "admin" && $password === "123456") { 
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['login_time'] = date('Y-m-d H:i:s'); 

        if (isset($_POST['remember'])) {
            setcookie('simako_remember', $username, time() + (86400 * 30), "/");
        } else {
            setcookie('simako_remember', '', time() - 3600, "/");
        }

        header("Location: dashboard.php");
        exit();
    } else {
        $login_error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SIMAKO - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .animate-pop-in { animation: popIn 0.4s ease-out forwards; }
        @keyframes popIn { 
            0% { opacity: 0; transform: translateY(20px) scale(0.98); } 
            100% { opacity: 1; transform: translateY(0) scale(1); } 
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 p-4">

    <div class="w-full max-w-sm p-6 space-y-4 bg-white shadow-2xl rounded-2xl animate-pop-in border-t-4 border-cyan-500">

        <div class="text-center pt-2"> 
            <h2 class="text-2xl font-extrabold text-gray-900">
                SIMAKO Login
            </h2>
            <p class="mt-1 text-xs text-gray-500">Sistem Manajemen Kontak</p>
        </div>

        <?php if ($login_error): ?>
            <div class="p-3 text-sm font-medium text-red-600 bg-red-100 rounded-lg" role="alert">
                <?php echo $login_error; ?>
            </div>
        <?php endif; ?>

        <form class="mt-4 space-y-4" method="POST">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input id="username" name="username" type="text" required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                           focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition"
                    value="<?php echo htmlspecialchars($remembered_user); ?>">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input id="password" name="password" type="password" required
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                           focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition"
                    value="123456">
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox"
                        <?php echo $remembered_user ? 'checked' : ''; ?>
                        class="h-4 w-4 text-cyan-600 focus:ring-cyan-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-900">Remember me</label>
                </div>
            </div>
            
            <div>
                <button type="submit" name="login"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-lg text-sm font-bold text-white
                               bg-gradient-to-r from-cyan-600 to-blue-600 
                               hover:from-cyan-700 hover:to-blue-700 
                               focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500
                               transition-all duration-300 ease-in-out transform hover:-translate-y-px active:scale-[0.98]">
                    Masuk
                </button>
            </div>
        </form>

    </div>

</body>
</html>
