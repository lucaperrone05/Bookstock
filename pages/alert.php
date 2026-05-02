<?php
require_once '../includes/auth_check.php';
$activePage = 'alert';
$pageTitle  = 'Alert Scorte';
$pageSubtitle = 'Libri esauriti o sotto la soglia di allerta';

require_once '../includes/db.php';

// Fetch alert books
$stmt = $pdo->query("
    SELECT b.id, b.title, b.stock_qty, b.stock_alert_qty, a.name AS author_name 
    FROM books b 
    JOIN authors a ON b.author_id = a.id 
    WHERE b.stock_qty <= b.stock_alert_qty 
    ORDER BY b.stock_qty ASC
");
$alerts = $stmt->fetchAll();

include '../includes/layout.php';
?>

<!-- ── Page Content ─────────────────────────────────────────────────────── -->
<div class="max-w-6xl mx-auto space-y-6 animate-[fadeInUp_0.3s_ease]">

    <!-- Info Banner -->
    <?php if (!empty($alerts)): ?>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0">
                <i class="bi bi-info-circle text-blue-500 text-lg"></i>
            </div>
            <p class="text-sm text-gray-600">
                Clicca su una card per essere reindirizzato alla pagina scorte e ripristinare il magazzino.
            </p>
        </div>
        <div class="hidden sm:block">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                Totale Alert: <?= count($alerts) ?>
            </span>
        </div>
    </div>
    <?php endif; ?>

    <!-- Cards container -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (empty($alerts)): ?>
            <div class="col-span-full bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center animate-[fadeIn_0.5s_ease]">
                <i class="bi bi-check-circle-fill text-6xl text-green-400 mb-4 inline-block drop-shadow-sm"></i>
                <h3 class="text-2xl font-bold text-gray-800 tracking-tight">Tutto sotto controllo!</h3>
                <p class="text-gray-500 mt-2 max-w-md mx-auto">Il tuo magazzino è in ottima salute. Non ci sono libri esauriti o sotto la soglia di allerta al momento.</p>
            </div>
        <?php else: ?>
            <?php foreach ($alerts as $book): 
                $isOut = $book['stock_qty'] <= 0;
                // Colori dinamici basati sullo stato
                $cardBg = $isOut ? 'bg-red-50 border-red-200 hover:border-red-300' : 'bg-amber-50 border-amber-200 hover:border-amber-300';
                $iconColor = $isOut ? 'text-red-600' : 'text-amber-600';
                $badgeClass = $isOut ? 'bg-red-100 text-red-700 border-red-200' : 'bg-amber-100 text-amber-800 border-amber-200';
                $iconClass = $isOut ? 'bi-x-octagon-fill' : 'bi-exclamation-triangle-fill';
            ?>
                <a href="scorte.php?book_id=<?= $book['id'] ?>" class="block rounded-xl border shadow-sm transition-all duration-200 hover:-translate-y-1.5 hover:shadow-lg <?= $cardBg ?> group">
                    <div class="p-5 h-full flex flex-col relative overflow-hidden">
                        
                        <!-- Elemento decorativo in background -->
                        <div class="absolute -right-4 -top-4 opacity-5 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-6">
                            <i class="bi <?= $iconClass ?> text-9xl <?= $iconColor ?>"></i>
                        </div>

                        <!-- Header Card -->
                        <div class="flex items-start justify-between mb-5 relative z-10">
                            <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center flex-shrink-0 shadow-sm group-hover:shadow transition-shadow">
                                <i class="bi <?= $iconClass ?> text-2xl <?= $iconColor ?>"></i>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold border uppercase tracking-wider <?= $badgeClass ?>">
                                <?= $isOut ? 'Esaurito' : 'In Esaurimento' ?>
                            </span>
                        </div>
                        
                        <!-- Info Libro -->
                        <div class="relative z-10 mb-6">
                            <h3 class="font-bold text-gray-800 text-lg leading-tight mb-1 line-clamp-2 group-hover:text-blue-700 transition-colors">
                                <?= htmlspecialchars($book['title']) ?>
                            </h3>
                            <p class="text-sm text-gray-600 font-medium">
                                <i class="bi bi-person text-gray-400 mr-1"></i>
                                <?= htmlspecialchars($book['author_name']) ?>
                            </p>
                        </div>
                        
                        <!-- Footer Card (Metriche) -->
                        <div class="flex items-center justify-between border-t border-black/10 pt-4 mt-auto relative z-10">
                            <div class="flex flex-col">
                                <span class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Giacenza Attuale</span>
                                <span class="text-3xl font-black <?= $iconColor ?> leading-none drop-shadow-sm">
                                    <?= $book['stock_qty'] ?>
                                </span>
                            </div>
                            <div class="w-px h-8 bg-black/10 mx-2"></div>
                            <div class="flex flex-col text-right">
                                <span class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Soglia Allerta</span>
                                <span class="text-2xl font-bold text-gray-700 leading-none">
                                    <?= $book['stock_alert_qty'] ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
// Chiudi il layout
echo '        </main>
    </div>
</body>
</html>';
?>
