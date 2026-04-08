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
    <title>Login - GIKO Admin</title>
    <!-- Tailwind CSS (CDN) -->
    <link rel="stylesheet" href="../css/tailwind.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../css/admin/style.css">
</head>

<body style="background-color:#111827;color:#fff;margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;">

    <div style="width:100%;max-width:340px;padding:0 1rem;">
        <div style="background-color:#1f2937;border-radius:0.5rem;border:1px solid #374151;overflow:hidden;box-shadow:0 10px 25px rgba(0,0,0,0.3);">
            <div style="padding:2rem 1.5rem;border-bottom:1px solid #374151;background:rgba(0,0,0,0.2);text-align:center;">
                <div style="font-size:1.75rem;font-weight:700;letter-spacing:0.15em;color:#0055FF;margin-bottom:0.25rem;font-family:'Montserrat',sans-serif;">GIKO</div>
                <div style="color:#9ca3af;font-size:0.75rem;letter-spacing:0.1em;">ADMINISTRATION</div>
            </div>

            <form method="POST" action="" style="padding:1.5rem;">
                <?php if ($error): ?>
                    <div style="background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.5);color:#ef4444;padding:0.5rem 0.75rem;border-radius:0.25rem;margin-bottom:1rem;font-size:0.8rem;">
                        <i class="fas fa-exclamation-circle" style="margin-right:0.5rem;"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <div style="margin-bottom:1rem;">
                    <label style="display:block;color:#9ca3af;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:0.4rem;" for="username">
                        Username
                    </label>
                    <div style="position:relative;">
                        <span style="position:absolute;top:50%;left:0.75rem;transform:translateY(-50%);color:#6b7280;">
                            <i class="fas fa-user"></i>
                        </span>
                        <input
                            style="width:100%;background-color:#374151;border:1px solid #4b5563;color:#fff;border-radius:0.25rem;padding:0.6rem 0.75rem 0.6rem 2.25rem;font-size:0.875rem;outline:none;box-sizing:border-box;"
                            id="username" name="username" type="text" placeholder="Enter username" required autofocus
                            onfocus="this.style.borderColor='#0055FF'" onblur="this.style.borderColor='#4b5563'">
                    </div>
                </div>

                <div style="margin-bottom:1.25rem;">
                    <label style="display:block;color:#9ca3af;font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:0.4rem;" for="password">
                        Password
                    </label>
                    <div style="position:relative;">
                        <span style="position:absolute;top:50%;left:0.75rem;transform:translateY(-50%);color:#6b7280;">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input
                            style="width:100%;background-color:#374151;border:1px solid #4b5563;color:#fff;border-radius:0.25rem;padding:0.6rem 0.75rem 0.6rem 2.25rem;font-size:0.875rem;outline:none;box-sizing:border-box;"
                            id="password" name="password" type="password" placeholder="Enter password" required
                            onfocus="this.style.borderColor='#0055FF'" onblur="this.style.borderColor='#4b5563'">
                    </div>
                </div>

                <button
                    style="width:100%;background-color:#0055FF;color:#fff;font-weight:700;padding:0.6rem 1rem;border-radius:0.25rem;border:none;cursor:pointer;letter-spacing:0.1em;font-family:'Montserrat',sans-serif;font-size:0.875rem;transition:background-color 0.2s;"
                    type="submit"
                    onmouseover="this.style.backgroundColor='#0044cc'" onmouseout="this.style.backgroundColor='#0055FF'">
                    LOGIN
                </button>
            </form>
        </div>

        <div style="text-align:center;margin-top:1.5rem;color:#6b7280;font-size:0.7rem;">
            &copy; <?php echo date('Y'); ?> GIKO Website. All rights reserved.
        </div>
    </div>

</body>

</html>