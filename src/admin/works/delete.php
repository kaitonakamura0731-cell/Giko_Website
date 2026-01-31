<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
checkAuth();

$id = $_GET['id'] ?? null;

if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM works WHERE id = ?");
        $stmt->execute([$id]);
    } catch (PDOException $e) {
        // Handle error if needed
    }
}

header("Location: index.php");
exit;
?>