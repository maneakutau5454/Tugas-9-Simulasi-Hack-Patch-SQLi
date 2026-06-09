<?php
// Connection configuration
$host = "localhost";
$user_db = "root";
$pass_db = "";
$db_name = "keamanan_db";

$conn_error = null;
$pesan = "";
$pesan_type = ""; // 'success' or 'error' or 'info'
$executed_sql = "";

// Establish connection
try {
    $conn = new mysqli($host, $user_db, $pass_db, $db_name);
    if ($conn->connect_error) {
        $conn_error = "Koneksi Database Gagal: (" . $conn->connect_errno . ") " . $conn->connect_error;
    }
} catch (Exception $e) {
    $conn_error = "Koneksi Database Gagal: " . $e->getMessage();
}

// Handle Form Submission (Secure Code - Langkah 4)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['password'])) {
    if ($conn_error) {
        $pesan = "Tidak dapat memproses login karena masalah database.";
        $pesan_type = "error";
    } else {
        $user_input = $_POST['username'];
        $pass_input = $_POST['password'];

        // SECURE SQL QUERY (PREPARED STATEMENTS)
        $sql_template = "SELECT * FROM users WHERE username = ? AND password = ?";
        $executed_sql = "PREPARE: " . $sql_template . "\nBIND PARAMETERS: username = '" . $user_input . "', password = '" . $pass_input . "'";

        try {
            $stmt = $conn->prepare($sql_template);
            if ($stmt) {
                $stmt->bind_param("ss", $user_input, $pass_input);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $pesan = "🔑 Berhasil Login! Selamat datang, Role: <strong>" . htmlspecialchars($row['role']) . "</strong>";
                    $pesan_type = "success";
                } else {
                    $pesan = "❌ Gagal Login! Sistem aman dari injeksi.";
                    $pesan_type = "error";
                }
                $stmt->close();
            } else {
                $pesan = "⚠️ Gagal mempersiapkan query: " . $conn->error;
                $pesan_type = "error";
            }
        } catch (Exception $e) {
            $pesan = "⚠️ Kesalahan Eksekusi Query: " . $e->getMessage();
            $pesan_type = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Injection Lab - Vulnerable Login</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            background-color: #090d16;
            background-image: 
                radial-gradient(at 0% 0%, rgba(234, 88, 12, 0.12) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(147, 51, 234, 0.1) 0px, transparent 50%),
                radial-gradient(at 50% 100%, rgba(234, 88, 12, 0.05) 0px, transparent 50%),
                radial-gradient(#1f293d 1px, transparent 1px);
            background-size: 40px 40px, 40px 40px, 40px 40px, 24px 24px;
        }
        
        .glass-panel {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        }
        
        .btn-glow:hover {
            box-shadow: 0 0 20px rgba(249, 115, 22, 0.4);
        }

        .glow-orange {
            box-shadow: 0 0 40px -10px rgba(234, 88, 12, 0.3);
        }

        .glow-emerald {
            box-shadow: 0 0 40px -10px rgba(16, 185, 129, 0.3);
        }
    </style>
</head>
<body class="min-h-screen text-slate-100 flex flex-col items-center justify-between p-4 md:p-8">

    <!-- Header Section -->
    <header class="w-full max-w-4xl text-center mt-4 mb-2">
        <div class="inline-flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider mb-3">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            Tahap 2: Secure Mode (Langkah 4 - Prepared Statements)
        </div>
        <h1 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight">
            Sistem Otentikasi Lab Kriptografi
        </h1>
        <p class="text-slate-400 mt-2 text-sm md:text-base max-w-xl mx-auto">
            Sistem login telah diamankan menggunakan binding parameter (Prepared Statements) untuk menolak serangan SQL Injection.
        </p>
    </header>

    <!-- Main Container -->
    <main class="w-full max-w-md my-auto">
        <div class="glass-panel glow-emerald rounded-3xl p-6 md:p-8 relative overflow-hidden transition-all duration-300">
            <!-- Top decorative bar -->
            <div class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-emerald-500 to-teal-600"></div>

            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-emerald-500/10 text-emerald-500 rounded-2xl border border-emerald-500/20">
                        <i data-lucide="shield-check" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-lg text-white">Login Perusahaan</h2>
                        <p class="text-xs text-slate-400">Database: keamanan_db</p>
                    </div>
                </div>
                <!-- Status DB indicator -->
                <div class="flex items-center gap-1.5 bg-slate-800/80 px-3 py-1 rounded-full border border-slate-700 text-xs">
                    <?php if ($conn_error): ?>
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <span class="text-rose-400 font-medium">Offline</span>
                    <?php else: ?>
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span class="text-emerald-400 font-medium">Online</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Database offline warning -->
            <?php if ($conn_error): ?>
                <div class="bg-rose-500/10 border border-rose-500/20 rounded-2xl p-4 mb-6 flex items-start gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-500 shrink-0 mt-0.5"></i>
                    <div class="text-xs text-rose-300 font-medium leading-relaxed">
                        <strong>Koneksi Error:</strong> <?=$conn_error?>. <br>
                        <span class="text-slate-400">Pastikan MySQL di Laragon/XAMPP sudah menyala dan database <code class="text-rose-400">keamanan_db</code> telah dibuat.</span>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Feedback Message -->
            <?php if ($pesan): ?>
                <div class="mb-6 rounded-2xl p-4 transition-all duration-300 animate-fadeIn <?php
                    if ($pesan_type == 'success') echo 'bg-emerald-500/10 border border-emerald-500/20 text-emerald-300';
                    else if ($pesan_type == 'error') echo 'bg-rose-500/10 border border-rose-500/20 text-rose-300';
                    else echo 'bg-blue-500/10 border border-blue-500/20 text-blue-300';
                ?>">
                    <div class="flex items-start gap-3">
                        <?php if ($pesan_type == 'success'): ?>
                            <i data-lucide="unlock" class="w-5 h-5 text-emerald-400 shrink-0"></i>
                        <?php elseif ($pesan_type == 'error'): ?>
                            <i data-lucide="lock" class="w-5 h-5 text-rose-400 shrink-0"></i>
                        <?php else: ?>
                            <i data-lucide="info" class="w-5 h-5 text-blue-400 shrink-0"></i>
                        <?php endif; ?>
                        <p class="text-sm font-medium leading-relaxed"><?= $pesan; ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="" class="space-y-5">
                <div>
                    <label for="username" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 pointer-events-none">
                            <i data-lucide="user" class="w-5 h-5"></i>
                        </span>
                        <input 
                            type="text" 
                            name="username" 
                            id="username" 
                            required
                            placeholder="admin_rahasia atau payload" 
                            class="w-full bg-slate-950/60 border border-slate-800 rounded-2xl py-3.5 pl-12 pr-4 text-white placeholder-slate-500 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500/30 transition-all font-sans text-sm"
                        >
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider">Password</label>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 pointer-events-none">
                            <i data-lucide="key-round" class="w-5 h-5"></i>
                        </span>
                        <input 
                            type="text" 
                            name="password" 
                            id="password" 
                            required
                            placeholder="password_super_sulit_123" 
                            class="w-full bg-slate-950/60 border border-slate-800 rounded-2xl py-3.5 pl-12 pr-4 text-white placeholder-slate-500 focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500/30 transition-all font-sans text-sm"
                        >
                    </div>
                </div>

                <button 
                    type="submit" 
                    class="w-full bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold py-3.5 px-4 rounded-2xl transition-all duration-300 btn-glow mt-2 flex items-center justify-center gap-2 text-sm uppercase tracking-wider"
                >
                    <i data-lucide="shield" class="w-4 h-4"></i> Masuk (Aman)
                </button>
            </form>
        </div>

        <!-- Educational Output (Shows executed SQL query) -->
        <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && $executed_sql): ?>
            <div class="mt-6 glass-panel rounded-2xl p-5 border border-emerald-500/20 animate-fadeIn">
                <h3 class="text-xs font-bold text-emerald-400 uppercase tracking-wider mb-2.5 flex items-center gap-2">
                    <i data-lucide="terminal" class="w-4 h-4"></i> Proses Parameter Binding:
                </h3>
                <div class="bg-slate-950/80 border border-slate-800/80 rounded-xl p-4 overflow-x-auto">
                    <code class="font-mono text-xs text-emerald-300 whitespace-pre-wrap break-all"><?= htmlspecialchars($executed_sql); ?></code>
                </div>
                <div class="mt-3 text-[11px] text-slate-400 leading-relaxed">
                    Kerangka query dikirim ke MySQL terlebih dahulu menggunakan tanda tanya (<code class="text-slate-300 font-mono">?</code>). Input pengguna diproses secara terpisah dan diperlakukan murni sebagai string/data biasa, bukan instruksi perintah SQL!
                </div>
            </div>
        <?php endif; ?>
    </main>

    <!-- Info & Payload Helpers -->
    <footer class="w-full max-w-xl text-center mt-6 mb-2">
        <div class="bg-slate-900/40 border border-slate-800/50 rounded-2xl p-4 text-xs text-slate-400 max-w-md mx-auto">
            <div class="flex items-center justify-center gap-2 mb-2 text-slate-300 font-bold">
                <i data-lucide="shield-check" class="w-4 h-4 text-emerald-500"></i> Proteksi Aktif
            </div>
            <p class="mb-2">Silakan uji ulang payload sebelumnya:</p>
            <div class="flex items-center justify-center gap-2 bg-slate-950/50 border border-slate-800 px-3 py-1.5 rounded-xl w-fit mx-auto mb-2">
                <code class="font-mono font-bold text-slate-400 select-all cursor-pointer">' OR '1'='1</code>
            </div>
            <p class="text-[10px] text-slate-500">Sistem akan menolak input ini karena parameter telah dipisahkan dari logika SQL.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize Lucide Icons
            lucide.createIcons();
        });
    </script>
</body>
</html>
