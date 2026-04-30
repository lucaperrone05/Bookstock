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

        /* Gestione placeholder/floating label con la logica peer di Tailwind */
        input:focus~label,
        input:not(:placeholder-shown)~label {
            transform: scale(0.85) translateY(-1.5rem) translateX(0.25rem);
            color: white;
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
        <p class="text-white/80 text-sm mb-8">Accedi al tuo gestionale</p>

        <!-- Form -->
        <form action="#" method="POST" id="loginForm" class="space-y-6">

            <!-- Email Input -->
            <div class="relative text-left">
                <input type="email" id="email" name="email" class="peer w-full bg-white/10 border border-white/20 rounded-xl px-4 pt-5 pb-2 text-white placeholder-transparent focus:outline-none focus:bg-white/20 focus:border-white/50 focus:ring-2 focus:ring-white/10 transition-all duration-300 backdrop-blur-sm" placeholder="nome@esempio.com" required>
                <label for="email" class="absolute left-4 top-3.5 text-white/70 transition-all duration-300 pointer-events-none origin-top-left flex items-center gap-2">
                    <i class="bi bi-envelope"></i> Email
                </label>
            </div>

            <!-- Password Input -->
            <div class="relative text-left">
                <input type="password" id="password" name="password" class="peer w-full bg-white/10 border border-white/20 rounded-xl px-4 pt-5 pb-2 text-white placeholder-transparent focus:outline-none focus:bg-white/20 focus:border-white/50 focus:ring-2 focus:ring-white/10 transition-all duration-300 backdrop-blur-sm" placeholder="Password" required>
                <label for="password" class="absolute left-4 top-3.5 text-white/70 transition-all duration-300 pointer-events-none origin-top-left flex items-center gap-2">
                    <i class="bi bi-lock"></i> Password
                </label>
            </div>

            <!-- Pulsante Login -->
            <button type="submit" id="btn-login" class="w-full bg-white/20 border border-white/30 hover:bg-white/30 hover:border-white/50 hover:-translate-y-0.5 hover:shadow-lg active:translate-y-0 text-white font-semibold py-3 rounded-xl transition-all duration-300 backdrop-blur-sm flex items-center justify-center gap-1 text-lg mt-2">
                Accedi <i class="bi bi-arrow-right-short text-2xl leading-none"></i>
            </button>
        </form>

        <!-- Link dimenticato -->
        <a href="#" class="inline-block mt-8 text-sm text-white/70 hover:text-white hover:underline transition-colors duration-200">
            Hai dimenticato la password?
        </a>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-login');
            const originalHTML = btn.innerHTML;

            // Sostituisce il contenuto con uno spinner SVG di Tailwind
            btn.innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Accesso...
            `;
            btn.disabled = true;

            setTimeout(() => {
                btn.innerHTML = originalHTML;
                btn.disabled = false;
                alert('Login effettuato con successo! (Solo frontend)');
            }, 1500);
        });
    </script>
</body>

</html>