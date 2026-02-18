<?php
// Debug settings
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/db.php';

echo "<h1>Admin Password Reset Tool</h1>";
echo "<p>Connected to database: <strong>" . htmlspecialchars($db) . "</strong> on host: <strong>" . htmlspecialchars($host) . "</strong></p>";

$username = 'admin';
$password = 'password';
$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
        // Update
        $stmt = $pdo->prepare("UPDATE admin_users SET password_hash = ? WHERE username = ?");
        $stmt->execute([$hash, $username]);
        echo "<div style='color: green; font-weight: bold; padding: 20px; border: 2px solid green; background: #e8f5e9; border-radius: 5px;'>Success: Password for '{$username}' has been updated to '{$password}'.</div>";
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO admin_users (username, password_hash) VALUES (?, ?)");
        $stmt->execute([$username, $hash]);
        echo "<div style='color: green; font-weight: bold; padding: 20px; border: 2px solid green; background: #e8f5e9; border-radius: 5px;'>Success: User '{$username}' created with password '{$password}'.</div>";
    }

    echo "<p style='margin-top: 20px;'><a href='login.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login Page</a></p>";
    echo "<p style='color: red; margin-top: 20px;'><strong>⚠️ Security Warning:</strong> Please delete this file (<code>reset_admin.php</code>) after successful login.</p>";

} catch (PDOException $e) {
    echo "<div style='color: red; padding: 20px; border: 2px solid red; background: #ffebee; border-radius: 5px;'>Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>