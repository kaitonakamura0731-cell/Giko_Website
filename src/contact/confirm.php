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

    <!-- Header -->
    <header class="fixed w-full z-50 transition-all duration-300 bg-black/80 backdrop-blur-md border-b border-white/5" id="header">
        <div class="container mx-auto px-6 h-20 flex justify-between items-center">
            <a href="../index.php" class="flex items-center group">
                <img src="../assets/images/logo_new.png" alt="GIKO" class="h-10 group-hover:opacity-80 transition-opacity">
            </a>
            <a href="index.php"
                class="text-xs font-bold font-en tracking-widest hover:text-primary transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> CONTACT
            </a>
        </div>
    </header>

    <!-- Confirm Section -->
    <section class="min-h-screen flex items-center justify-center py-24" style="padding-top: 8rem;">
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

    <!-- Footer -->
    <footer class="bg-secondary pt-24 pb-12 border-t border-white/5 text-white">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-20">
                <div>
                    <img src="../assets/images/logo_new.png" alt="GIKO" class="h-8 mb-6">
                    <p class="text-xs text-gray-500 leading-loose mb-6">最高級の素材と技術で、カーライフに彩りを。</p>
                    <div class="flex space-x-4">
                        <a href="https://www.instagram.com/giko_artisan?igsh=MWRuenVqMzBkNzA3bw==" target="_blank"
                            class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-primary transition-colors"><i
                                class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div>
                    <h3 class="text-sm font-bold font-en tracking-widest mb-6 border-b border-primary/30 inline-block pb-2">MENU</h3>
                    <ul class="space-y-4 text-xs tracking-wider text-gray-400">
                        <li><a href="../index.php#concept" class="hover:text-white transition-colors">CONCEPT</a></li>
                        <li><a href="../pages/works.php" class="hover:text-white transition-colors">WORKS</a></li>
                        <li><a href="../pages/before_after.html" class="hover:text-white transition-colors">BEFORE & AFTER</a></li>
                        <li><a href="../index.php#flow" class="hover:text-white transition-colors">FLOW</a></li>
                        <li><a href="../index.php#company" class="hover:text-white transition-colors">COMPANY</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-bold font-en tracking-widest mb-6 border-b border-primary/30 inline-block pb-2">CONTACT</h3>
                    <ul class="space-y-4 text-xs tracking-wider text-gray-400">
                        <li><a href="index.php" class="hover:text-white transition-colors">お問い合わせフォーム</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-bold font-en tracking-widest mb-6 border-b border-primary/30 inline-block pb-2">LEGAL</h3>
                    <ul class="space-y-4 text-xs tracking-wider text-gray-400">
                        <li><a href="../legal/privacy.html" class="hover:text-white transition-colors">プライバシーポリシー</a></li>
                        <li><a href="../legal/tokusho.html" class="hover:text-white transition-colors">特定商取引法に基づく表記</a></li>
                        <li><a href="../legal/terms.html" class="hover:text-white transition-colors">利用規約</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/5 pt-8 flex justify-between items-center text-[10px] text-gray-600 font-en tracking-widest">
                <p>&copy; 2025 GIKO. ALL RIGHTS RESERVED.</p>
                <div>DESIGNED BY ATLASSHIFT</div>
            </div>
        </div>
    </footer>
    <script src="../assets/js/main.js"></script>

</body>

</html>