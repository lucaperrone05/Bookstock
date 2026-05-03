<?php
require_once '../includes/auth_check.php';
$activePage = 'catalogo';
$pageTitle  = 'Catalogo';

require_once '../includes/db.php';

// ── Query: tutti i libri con autore e categoria ──────────────────────────────
$stmt = $pdo->query("
    SELECT
        b.id,
        b.title,
        a.name   AS author,
        c.name   AS category,
        b.price,
        b.stock_qty,
        b.stock_alert_qty
    FROM books b
    JOIN authors    a ON a.id = b.author_id
    JOIN categories c ON c.id = b.category_id
    ORDER BY b.title ASC
");
$books = $stmt->fetchAll();

$pageSubtitle = count($books) . ' libri trovati';
include '../includes/layout.php';
?>

<!-- Page Content  -->
<div class="space-y-6">

    <!-- Barra di ricerca e filtri -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input id="search-input"
                   type="text"
                   placeholder="Cerca per titolo, autore…"
                   class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 transition">
        </div>

        <select id="filter-category"
                class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-400 transition bg-white text-gray-600">
            <option value="">Tutte le categorie</option>
            
            <?php
            $cats = $pdo->query("SELECT DISTINCT name FROM categories ORDER BY name")->fetchAll();
            
            foreach ($cats as $cat):
            ?>
            <option value="<?= htmlspecialchars($cat['name']) ?>">
                <?= htmlspecialchars($cat['name']) ?>
            </option>
            <?php endforeach; ?>
        </select>
        
        <a href="aggiungi_libro.php" id="btn-add-book"
                class="inline-flex items-center justify-center gap-2 bg-gray-700 hover:bg-gray-900 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors duration-200 shadow-sm whitespace-nowrap">
            <i class="bi bi-plus-lg"></i>
            Aggiungi Libro
        </a>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table id="books-table" class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-left">
                        <th class="px-5 py-3.5 font-semibold text-gray-600 whitespace-nowrap">#</th>
                        <th class="px-5 py-3.5 font-semibold text-gray-600 whitespace-nowrap">Titolo</th>
                        <th class="px-5 py-3.5 font-semibold text-gray-600 whitespace-nowrap">Autore</th>
                        <th class="px-5 py-3.5 font-semibold text-gray-600 whitespace-nowrap">Categoria</th>
                        <th class="px-5 py-3.5 font-semibold text-gray-600 whitespace-nowrap text-right">Prezzo</th>
                        <th class="px-5 py-3.5 font-semibold text-gray-600 whitespace-nowrap text-center">Stock</th>
                        <th class="px-5 py-3.5 font-semibold text-gray-600 whitespace-nowrap text-center">Azioni</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($books)): ?>
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                            <i class="bi bi-journal-x text-4xl mb-2 block"></i>
                            Nessun libro nel catalogo
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($books as $i => $book): ?>
                    <?php
                        $isLow  = $book['stock_qty'] > 0 && $book['stock_qty'] <= $book['stock_alert_qty'];
                        $isOut  = $book['stock_qty'] === 0;
                        $stockClass = $isOut  ? 'bg-red-100 text-red-700'
                                    : ($isLow ? 'bg-yellow-100 text-yellow-700'
                                               : 'bg-green-100 text-green-700');
                        $stockIcon  = $isOut  ? 'bi-x-circle-fill'
                                    : ($isLow ? 'bi-exclamation-circle-fill'
                                               : 'bi-check-circle-fill');
                    ?>
                    <tr class="book-row hover:bg-gray-50 transition-colors duration-150"
                        data-title="<?= strtolower(htmlspecialchars($book['title'])) ?>"
                        data-author="<?= strtolower(htmlspecialchars($book['author'])) ?>"
                        data-category="<?= htmlspecialchars($book['category']) ?>">

                        <td class="px-5 py-4 text-gray-400 font-mono text-xs"><?= $i + 1 ?></td>

                        <td class="px-5 py-4">
                            <span class="font-semibold text-gray-800"><?= htmlspecialchars($book['title']) ?></span>
                        </td>

                        <td class="px-5 py-4 text-gray-600"><?= htmlspecialchars($book['author']) ?></td>

                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                <?= htmlspecialchars($book['category']) ?>
                            </span>
                        </td>

                        <td class="px-5 py-4 text-right font-medium text-gray-800">
                            € <?= number_format($book['price'], 2, ',', '.') ?>
                        </td>

                        <td class="px-5 py-4 text-center">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold <?= $stockClass ?>">
                                <i class="bi <?= $stockIcon ?>"></i>
                                <?= $book['stock_qty'] ?>
                            </span>
                        </td>

                        <td class="px-5 py-4">
                            <div class="flex items-center justify-center gap-1">
                                <!-- Visualizza dettagli -->
                                <a href="visualizza_libro.php?id=<?= $book['id'] ?>" title="Visualizza dettagli"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 hover:bg-blue-50 transition-colors duration-150">
                                    <i class="bi bi-eye text-base"></i>
                                </a>
                                <!-- Modifica -->
                                <a href="modifica_libro.php?id=<?= $book['id'] ?>" title="Modifica libro"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-amber-600 hover:bg-amber-50 transition-colors duration-150">
                                    <i class="bi bi-pencil text-base"></i>
                                </a>
                                <!-- Elimina -->
                                <button title="Elimina libro"
                                        data-id="<?= $book['id'] ?>"
                                        data-title="<?= htmlspecialchars($book['title']) ?>"
                                        class="btn-delete inline-flex items-center justify-center w-8 h-8 rounded-lg text-red-500 hover:bg-red-50 transition-colors duration-150">
                                    <i class="bi bi-trash text-base"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer: count libri trovati -->
        <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
            <span id="visible-count" class="text-xs text-gray-500">
                <strong><?= count($books) ?></strong> libri trovati
            </span>
        </div>
    </div>
</div>

<!-- Delete Confirm Modal -->
<div id="modal-delete"
     class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
     role="dialog" aria-modal="true" aria-labelledby="modal-delete-title">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="modal-backdrop"></div>
    <!-- Panel -->
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 space-y-4 animate-[fadeInUp_0.2s_ease]">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                <i class="bi bi-trash text-red-500 text-lg"></i>
            </div>
            <div>
                <h3 id="modal-delete-title" class="font-semibold text-gray-800">Elimina libro</h3>
                <p class="text-sm text-gray-500">Questa azione è irreversibile.</p>
            </div>
        </div>
        <p class="text-sm text-gray-700">
            Sei sicuro di voler eliminare <strong id="modal-book-title" class="text-gray-900"></strong>?
        </p>
        <div class="flex gap-3 pt-1">
            <button id="modal-cancel"
                    class="flex-1 px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Annulla
            </button>
            <button id="modal-confirm-delete"
                    class="flex-1 px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-medium transition-colors">
                Elimina
            </button>
        </div>
    </div>
</div>

<script>
// Ricerca live e filtro categorie
const searchInput      = document.getElementById('search-input');
const filterCategory   = document.getElementById('filter-category');
const rows             = document.querySelectorAll('.book-row');
const visibleCount     = document.getElementById('visible-count');

function filterTable() {
    const q    = searchInput.value.toLowerCase().trim();
    const cat  = filterCategory.value;
    let count  = 0;

    rows.forEach(row => {
        const title  = row.dataset.title;
        const author = row.dataset.author;
        const rowCat = row.dataset.category;

        const matchText = !q || title.includes(q) || author.includes(q);
        const matchCat  = !cat || rowCat === cat;

        if (matchText && matchCat) {
            row.classList.remove('hidden');
            count++;
        } else {
            row.classList.add('hidden');
        }
    });

    visibleCount.innerHTML = `Mostrando <strong>${count}</strong> libri`;
}

searchInput.addEventListener('input', filterTable);
filterCategory.addEventListener('change', filterTable);

// eliminare il libro
const modalDelete  = document.getElementById('modal-delete');
const modalTitle   = document.getElementById('modal-book-title');
const modalCancel  = document.getElementById('modal-cancel');
const modalBackdrop = document.getElementById('modal-backdrop');
const modalConfirm = document.getElementById('modal-confirm-delete');
let pendingDeleteId = null;

document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', () => {
        pendingDeleteId = btn.dataset.id;
        modalTitle.textContent = btn.dataset.title;
        modalDelete.classList.remove('hidden');
    });
});

function closeModal() {
    modalDelete.classList.add('hidden');
    pendingDeleteId = null;
}

modalCancel.addEventListener('click', closeModal);
modalBackdrop.addEventListener('click', closeModal);

modalConfirm.addEventListener('click', () => {
    if (!pendingDeleteId) return;
    window.location.href = `elimina_libro.php?id=${pendingDeleteId}`;
});

</script>

<?php
// Chiudi il layout
echo '        </main>
    </div>
</body>
</html>';
?>
