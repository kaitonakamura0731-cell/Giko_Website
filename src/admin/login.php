<?php
session_start();
require_once 'includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password_hash FROM admin_users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                // Login Success
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: index.php"); // Dashboard
                exit;
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    } else {
        $error = 'Please enter both username and password.';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Giko Admin</title>
    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../tailwind_config.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin/style.css">
</head>

<body class="bg-gray-900 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-sm">
        <div class="bg-gray-800 rounded-lg shadow-lg border border-gray-700 overflow-hidden">
            <div class="px-6 py-8 border-b border-gray-700 bg-black/20">
                <div class="text-center">
                    <div class="text-3xl font-bold font-en tracking-widest text-primary mb-2">GIKO</div>
                    <div class="text-gray-400 text-sm tracking-wider">ADMINISTRATION</div>
                </div>
            </div>

            <form method="POST" action="" class="p-8">
                <?php if ($error): ?>
                    <div class="bg-red-500/10 border border-red-500/50 text-red-500 px-4 py-3 rounded mb-6 text-sm">
                        <i class="fas fa-exclamation-circle mr-2"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <div class="mb-6">
                    <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider mb-2" for="username">
                        Username
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            <i class="fas fa-user"></i>
                        </span>
                        <input
                            class="w-full bg-gray-700 border border-gray-600 text-white rounded py-3 pl-10 pr-3 focus:outline-none focus:border-primary transition-colors"
                            id="username" name="username" type="text" placeholder="Enter username" required autofocus>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-gray-400 text-xs font-bold uppercase tracking-wider mb-2" for="password">
                        Password
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input
                            class="w-full bg-gray-700 border border-gray-600 text-white rounded py-3 pl-10 pr-3 focus:outline-none focus:border-primary transition-colors"
                            id="password" name="password" type="password" placeholder="Enter password" required>
                    </div>
                </div>

                <button
                    class="w-full bg-primary hover:bg-yellow-500 text-black font-bold py-3 px-4 rounded transition-colors tracking-widest font-en"
                    type="submit">
                    LOGIN
                </button>
            </form>
        </div>

        <div class="text-center mt-6 text-gray-500 text-xs">
            &copy; <?php echo date('Y'); ?> Giko Website. All rights reserved.
        </div>
    </div>

</body>

</html>