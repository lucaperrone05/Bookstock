<?php
$activePage = $activePage ?? '';
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'BookStock'; ?></title>
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Icone Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .nav-active { background-color: rgba(255, 255, 255, 0.1); border-left: 4px solid #fff; }

        /* Sidebar slide-in */
        #sidebar {
            transition: transform 0.3s ease;
        }
        @media (max-width: 767px) {
            #sidebar {
                position: fixed;
                top: 0; left: 0;
                height: 100%;
                z-index: 40;
                transform: translateX(-100%);
            }
            
            #sidebar.open {
                transform: translateX(0);
            }
        }
    </style>
</head>

<body class="bg-gray-100 h-screen flex overflow-hidden">

    <!-- OVERLAY -->
    <div id="sidebar-overlay"
         class="hidden fixed inset-0 bg-black/50 z-30 md:hidden"
         onclick="closeSidebar()">
    </div>

    <!-- NAVBAR LATERALE -->
    <aside id="sidebar" class="w-64 bg-gray-700 text-white flex flex-col flex-shrink-0 shadow-2xl">
        <!-- Logo Area -->
        <div class="p-6 border-b border-gray-600/50 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="bi bi-book-half text-2xl"></i>
                <span class="text-xl font-bold tracking-tight">BookStock</span>
            </div>

            <!-- Pulsante chiudi -->
            <button class="md:hidden text-gray-300 hover:text-white text-xl leading-none"
                    onclick="closeSidebar()" aria-label="Chiudi menu">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- Link di navigazione -->
        <nav class="flex-1 py-6 px-3 space-y-1">
            <a href="catalogo.php" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 hover:bg-gray-600 group <?php echo ($activePage == 'catalogo') ? 'nav-active' : ''; ?>">
                <i class="bi bi-journals text-lg"></i>
                <span class="font-medium">Catalogo</span>
            </a>
            <a href="scorte.php" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 hover:bg-gray-600 group <?php echo ($activePage == 'scorte') ? 'nav-active' : ''; ?>">
                <i class="bi bi-box-seam text-lg"></i>
                <span class="font-medium">Scorte</span>
            </a>
            <a href="alert.php" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 hover:bg-gray-600 group <?php echo ($activePage == 'alert') ? 'nav-active' : ''; ?>">
                <i class="bi bi-exclamation-triangle text-lg"></i>
                <span class="font-medium">Alert</span>
            </a>
        </nav>

        <!-- Footer Sidebar -->
        <div class="p-4 border-t border-gray-600/50 bg-gray-800/30">
            <a href="logout.php" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-600 transition-all text-sm">
                <i class="bi bi-box-arrow-left"></i>
                <span>Esci</span>
            </a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 overflow-y-auto p-4 md:p-8">

    <?php if (empty($hideLayoutHeader)): ?>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle ?? 'Gestionale Libreria') ?></h1>
                <?php if (!empty($pageSubtitle)): ?>
                <p class="text-sm text-gray-500 mt-1"><?= htmlspecialchars($pageSubtitle) ?></p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <script>

        function openSidebar() {
            document.getElementById('sidebar').classList.add('open');
            document.getElementById('sidebar-overlay').classList.remove('hidden');
        }

        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebar-overlay').classList.add('hidden');
        }
    </script>