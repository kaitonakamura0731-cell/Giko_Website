<?php
session_start();

// 1. CSRF Token Validation
if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('Location: contact.php?error=csrf');
    exit;
}

// 2. Input Validation & Sanitization
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.php');
    exit;
}

// Helper for cleaning inputs
function clean($str)
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

$name = isset($_POST['name']) ? clean($_POST['name']) : '';
$email = isset($_POST['email']) ? clean($_POST['email']) : '';
$tel = isset($_POST['tel']) ? clean($_POST['tel']) : '';
$subject = isset($_POST['subject']) ? clean($_POST['subject']) : '';
$message = isset($_POST['message']) ? clean($_POST['message']) : '';

// Validation (Ensure required fields)
if ($name === '' || $email === '' || $subject === '' || $message === '') {
    header('Location: contact.php?error=required');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CONFIRM | 技巧 -Giko-</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../tailwind_config.js"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;500;700&family=Montserrat:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>


<body class="bg-black text-white antialiased">

    <!-- Confirm Section -->
    <section class="min-h-screen flex items-center justify-center py-24">
        <div class="container mx-auto px-6 max-w-3xl">
            <div class="text-center mb-12">
                <h1 class="text-3xl font-bold font-en tracking-widest mb-4">CONFIRMATION</h1>
                <p class="text-gray-400 text-sm">以下の内容で送信してもよろしいですか？</p>
            </div>

            <div class="bg-white/5 border border-white/10 rounded-sm p-8 space-y-8">
                <div>
                    <p class="text-xs font-bold font-en tracking-widest text-primary mb-2">NAME</p>
                    <p class="text-lg"><?php echo $name; ?></p>
                </div>
                <div>
                    <p class="text-xs font-bold font-en tracking-widest text-primary mb-2">EMAIL</p>
                    <p class="text-lg"><?php echo $email; ?></p>
                </div>
                <div>
                    <p class="text-xs font-bold font-en tracking-widest text-primary mb-2">PHONE</p>
                    <p class="text-lg"><?php echo $tel ?: '-'; ?></p>
                </div>
                <div>
                    <p class="text-xs font-bold font-en tracking-widest text-primary mb-2">SUBJECT</p>
                    <p class="text-lg"><?php echo $subject; ?></p>
                </div>
                <div>
                    <p class="text-xs font-bold font-en tracking-widest text-primary mb-2">MESSAGE</p>
                    <p class="text-lg whitespace-pre-wrap leading-relaxed"><?php echo $message; ?></p>
                </div>
            </div>

            <form action="complete.php" method="POST" class="mt-8 flex justify-center gap-4">
                <!-- Hidden inputs to pass data -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="name" value="<?php echo $name; ?>">
                <input type="hidden" name="email" value="<?php echo $email; ?>">
                <input type="hidden" name="tel" value="<?php echo $tel; ?>">
                <input type="hidden" name="subject" value="<?php echo $subject; ?>">
                <input type="hidden" name="message" value="<?php echo $message; ?>">

                <button type="button" onclick="history.back()"
                    class="px-8 py-3 bg-transparent border border-gray-600 text-gray-400 hover:text-white hover:border-white transition-colors rounded-sm text-sm tracking-widest font-en">
                    BACK
                </button>
                <button type="submit"
                    class="px-12 py-3 bg-primary text-black font-bold hover:bg-white transition-all rounded-sm text-sm tracking-widest font-en shadow-lg shadow-primary/20">
                    SEND
                </button>
            </form>
        </div>
    </section>

</body>

</html>