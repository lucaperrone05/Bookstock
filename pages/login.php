<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: catalogo.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BookStock</title>
    <!-- Tailwind CSS (via CDN per uso immediato, versione 3) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    backgroundImage: {
                        'login-bg': "url('../img/sfondoLogin.jpeg')",
                    }
                }
            }
        }
    </script>
    <style>
        /* Sfondo principale */
        body {
            background-image: url('../assets/img/sfondoLogin.jpeg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        /* Fix per l'autofill di Chrome sui campi trasparenti */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px rgba(0, 0, 0, 0.5) inset !important;
            -webkit-text-fill-color: white !important;
            transition: background-color 5000s ease-in-out 0s;
        }
    </style>
</head>

<body class="h-screen w-full flex items-center justify-center font-sans relative antialiased text-white">

    <!-- Dark overlay -->
    <div class="absolute inset-0 bg-[#0f1117] bg-opacity-40 z-0"></div>

    <!-- Glass Container -->
    <div class="relative z-10 w-full max-w-md px-8 py-10 rounded-3xl bg-white/10 backdrop-blur-md border border-white/20 shadow-[0_8px_32px_0_rgba(0,0,0,0.37)] text-center">

        <!-- Logo/Icona -->
        <div class="text-5xl mb-3 drop-shadow-md">
            <i class="bi bi-book-half"></i>
        </div>

        <!-- Titoli -->
        <h2 class="text-3xl font-bold tracking-tight mb-2">BookStock</h2>
        <p class="text-white/80 text-sm mb-6">Accedi al tuo gestionale</p>
        
        <!-- Error Container -->
        <div id="error-container" class="hidden bg-red-500/20 border border-red-500/50 text-red-200 text-sm rounded-xl p-3 mb-6 flex items-center justify-center gap-2">
            <i class="bi bi-exclamation-circle-fill"></i>
            <span id="error-message"></span>
        </div>

        <!-- Form -->
        <form action="#" method="POST" id="loginForm" class="space-y-6">

            <!-- Username Input -->
            <div class="text-left space-y-1.5">
                <label for="username" class="text-white/90 font-medium text-sm flex items-center gap-2 ml-1">
                    <i class="bi bi-person"></i> Username
                </label>
                <input type="text" id="username" name="username" class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-white placeholder-white/40 focus:outline-none focus:bg-white/20 focus:border-white/50 focus:ring-2 focus:ring-white/10 transition-all duration-300 backdrop-blur-sm" placeholder="Inserisci il tuo username" required>
            </div>

            <!-- Password Input -->
            <div class="text-left space-y-1.5">
                <label for="password" class="text-white/90 font-medium text-sm flex items-center gap-2 ml-1">
                    <i class="bi bi-lock"></i> Password
                </label>
                <input type="password" id="password" name="password" class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-white placeholder-white/40 focus:outline-none focus:bg-white/20 focus:border-white/50 focus:ring-2 focus:ring-white/10 transition-all duration-300 backdrop-blur-sm" placeholder="Inserisci la password" required>
            </div>

            <!-- Pulsante Login -->
            <button type="submit" id="btn-login" class="w-full bg-white/20 border border-white/30 hover:bg-white/30 hover:border-white/50 hover:-translate-y-0.5 hover:shadow-lg active:translate-y-0 text-white font-semibold py-3 rounded-xl transition-all duration-300 backdrop-blur-sm flex items-center justify-center gap-1 text-lg mt-2">
                Accedi <i class="bi bi-arrow-right-short text-2xl leading-none"></i>
            </button>
        </form>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-login');
            const originalHTML = btn.innerHTML;
            const errorContainer = document.getElementById('error-container');
            const errorMessage = document.getElementById('error-message');

            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            errorContainer.classList.add('hidden');

            // Sostituisce il contenuto con uno spinner SVG di Tailwind
            btn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Accesso...
            `;
            btn.disabled = true;

            try {
                const res = await fetch('../api/auth.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username, password })
                });
                
                const data = await res.json();

                if (res.ok && data.success) {
                    window.location.href = 'catalogo.php';
                } else {
                    errorMessage.textContent = data.message || 'Errore durante il login';
                    errorContainer.classList.remove('hidden');
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                }
            } catch (err) {
                errorMessage.textContent = 'Errore di connessione al server';
                errorContainer.classList.remove('hidden');
                btn.innerHTML = originalHTML;
                btn.disabled = false;
            }
        });
    </script>
</body>

</html>