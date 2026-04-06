<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Sarpras — Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-md p-10">

        <!-- LOGO -->
        <div class="flex items-center gap-3 mb-8">
            <div class="w-10 h-10 bg-blue-700 rounded-xl flex items-center justify-center text-white font-bold text-sm">
                ES
            </div>
            <div>
                <p class="font-bold text-gray-800 leading-tight">E-Sarpras</p>
                <p class="text-xs text-gray-400">Sistem Peminjaman Alat</p>
            </div>
        </div>

        <!-- HEADING -->
        <h2 class="text-2xl font-bold text-gray-800 mb-1">Masuk</h2>
        <p class="text-sm text-gray-400 mb-8">Masukkan email dan password Anda</p>

        <!-- ERROR -->
        <?php if (!empty($_GET['error'])): ?>
        <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-lg px-4 py-3 mb-6">
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
        <?php endif; ?>

        <!-- FORM -->
        <form action="login_proses.php" method="post" class="space-y-5">

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">Email</label>
                <input
                    type="email"
                    name="email"
                    required
                    placeholder="contoh@email.com"
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1.5">Password</label>
                <div class="relative">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        placeholder="••••••••"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition pr-11"
                    >
                    <button type="button" onclick="togglePassword()"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 text-sm">
                        👁
                    </button>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2.5 rounded-xl transition text-sm">
                Masuk
            </button>

        </form>

        <p class="text-center text-xs text-gray-300 mt-8">&copy; <?= date('Y') ?> E-Sarpras</p>

    </div>

    <script>
    function togglePassword() {
        const input = document.getElementById('password');
        input.type = input.type === 'password' ? 'text' : 'password';
    }
    </script>

</body>
</html>