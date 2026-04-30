<?php
$activePage = $activePage ?? '';
?>
<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'BookStock'; ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .nav-active { background-color: rgba(255, 255, 255, 0.1); border-left: 4px solid #fff; }
    </style>
</head>

<body class="bg-gray-100 h-screen flex overflow-hidden">

    <!-- NAVBAR LATERALE -->
    <aside class="w-64 bg-gray-700 text-white flex flex-col flex-shrink-0 shadow-2xl">
        <!-- Logo Area -->
        <div class="p-6 border-b border-gray-600/50">
            <div class="flex items-center gap-3">
                <i class="bi bi-book-half text-2xl"></i>
                <span class="text-xl font-bold tracking-tight">BookStock</span>
            </div>
        </div>

        <!-- Navigation Links -->
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
            <a href="login.php" class="flex items-center gap-3 px-4 py-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-600 transition-all text-sm">
                <i class="bi bi-box-arrow-left"></i>
                <span>Esci</span>
            </a>
        </div>
    </aside>

    <!-- CONTENT WRAPPER -->
    <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden">
        <!-- TOPBAR -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 flex-shrink-0">
            <h2 class="text-xl font-semibold text-gray-800"><?php echo $pageTitle ?? 'Gestionale Biblioteca'; ?></h2>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500 font-medium italic">Gestionale Libreria</span>
                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600">
                    <i class="bi bi-person-fill"></i>
                </div>
            </div>
        </header>

        <!-- MAIN CONTENT AREA -->
        <main class="flex-1 overflow-y-auto p-8">