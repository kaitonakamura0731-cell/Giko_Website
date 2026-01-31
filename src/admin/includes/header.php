<?php
// Ensure session is started in the main file or here
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Giko Website</title>
    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="/tailwind_config.js"></script>

    <!-- Admin Custom CSS -->
    <link rel="stylesheet" href="/css/admin/style.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-900 text-white">
    <nav class="bg-gray-800 border-b border-gray-700">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="/admin/index.php" class="text-xl font-bold tracking-widest text-primary">GIKO ADMIN</a>
            <div class="flex items-center gap-4">
                <span class="text-gray-400 text-sm">Welcome,
                    <?php echo htmlspecialchars($_SESSION['username'] ?? 'Guest'); ?></span>
                <a href="/admin/logout.php" class="text-gray-400 hover:text-white transition-colors">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>
    <div class="admin-container">