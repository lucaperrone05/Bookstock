<?php
$bookMode   = $bookMode   ?? 'view';
$pageTitle  = $pageTitle  ?? 'Libro';
$book       = $book       ?? null;
$authors    = $authors    ?? [];
$categories = $categories ?? [];
$publishers = $publishers ?? [];

$cfg = [
    'view' => ['icon'=>'bi-eye',          'iconBg'=>'bg-blue-100',  'iconColor'=>'text-blue-600',  'badgeBg'=>'bg-blue-50',  'badgeText'=>'text-blue-700',  'label'=>'Sola lettura',  'saveLabel'=>'',               'saveIcon'=>'',          'saveBg'=>''],
    'edit' => ['icon'=>'bi-pencil-square', 'iconBg'=>'bg-amber-100', 'iconColor'=>'text-amber-600', 'badgeBg'=>'bg-amber-50', 'badgeText'=>'text-amber-700', 'label'=>'Modifica',       'saveLabel'=>'Salva modifiche', 'saveIcon'=>'bi-check-lg','saveBg'=>'bg-amber-600 hover:bg-amber-700'],
    'add'  => ['icon'=>'bi-plus-circle',  'iconBg'=>'bg-green-100', 'iconColor'=>'text-green-600', 'badgeBg'=>'bg-green-50', 'badgeText'=>'text-green-700', 'label'=>'Nuovo libro',    'saveLabel'=>'Aggiungi libro',  'saveIcon'=>'bi-plus-lg', 'saveBg'=>'bg-green-600 hover:bg-green-700'],
][$bookMode] ?? [];

// Genera le <option> per un datalist HTML partendo da un array di record DB.
// Ogni option ha il valore visibile (label) e l'ID nascosto come attributo data-id.
function bookDatalistOpts(array $items, string $valKey, string $labelKey): string {
    $out = '';
    foreach ($items as $item) {
        $out .= '<option data-id="'.(int)$item[$valKey].'" value="'.htmlspecialchars($item[$labelKey]).'"></option>';
    }
    return $out;
}

$hideLayoutHeader = true;
require_once __DIR__ . '/layout.php';
?>

<style>
    .book-card{
        background:#fff;
        border-radius:1rem;
        border:1px solid #e5e7eb;
        box-shadow:0 1px 3px rgba(0,0,0,.06)
    }
    .field-label{display:block;
        font-size:.6875rem;font-weight:600;
        text-transform:uppercase;
        letter-spacing:.06em;color:#9ca3af;
        margin-bottom:.25rem
    }
    .field-value{
        font-size:.9375rem;
        color:#1f2937;
        font-weight:500
    }
    .form-input,.form-select{width:100%;
        padding:.55rem .75rem;
        font-size:.875rem;
        border:1px solid #e5e7eb;
        border-radius:.5rem;
        outline:none;
        background:#fff;
        transition:box-shadow .15s,border-color .15s;
        color:#111827
    }
    .form-input:focus,.form-select:focus{
        box-shadow:0 0 0 2px #9ca3af40;
        border-color:#9ca3af
    }
    .form-input.error,.form-select.error{
        border-color:#ef4444;
        box-shadow:0 0 0 2px #ef444420
    }
</style>

<div class="space-y-6">

    <nav class="flex items-center gap-2 text-sm text-gray-500">
        <a href="../pages/catalogo.php" class="hover:text-gray-800 transition-colors flex items-center gap-1">
            <i class="bi bi-journals"></i> <span>Catalogo</span>
        </a>
        <i class="bi bi-chevron-right text-xs text-gray-300"></i>
        <span class="text-gray-800 font-medium"><?= htmlspecialchars($pageTitle) ?></span>
    </nav>

    <!-- Header -->
    <div class="book-card p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl <?= $cfg['iconBg'].' '.$cfg['iconColor'] ?> flex items-center justify-center text-2xl flex-shrink-0">
                <i class="bi <?= $cfg['icon'] ?>"></i>
            </div>

            <div>
                <h1 class="text-xl font-bold text-gray-900"><?= htmlspecialchars($pageTitle) ?></h1>
                <p class="text-sm text-gray-400 mt-0.5">
                    <?= ($book && isset($book['title'])) ? htmlspecialchars($book['title']) : 'Compila i campi per aggiungere un nuovo libro' ?>
                </p>
            </div>
        </div>

        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold <?= $cfg['badgeBg'].' '.$cfg['badgeText'] ?>">
            <i class="bi <?= $cfg['icon'] ?>"></i> <?= $cfg['label'] ?>
        </span>
    </div>

    <!-- Corpo -->
    <div class="book-card overflow-hidden">

    <?php if ($bookMode === 'view'): ?>
        <!-- VIEW -->
        <div class="p-6 sm:p-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="sm:col-span-2 lg:col-span-3">
                <span class="field-label">Titolo</span>
                <p class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($book['title'] ?? '—') ?></p>
            </div>
            <hr class="border-t border-gray-100 sm:col-span-2 lg:col-span-3">
            <?php
            $b = $book ?? [];
            $fields = [
                ['bi-person',   'Autore',            $b['author_name']   ?? '—', ''],
                ['bi-calendar3','Anno pubblicazione', $b['publish_year']  ?? '—', ''],
                ['bi-tag',      'Categoria',          $b['category_name'] ?? '—', 'badge'],
                ['bi-building', 'Casa editrice',      $b['publisher_name']?? '—', ''],
                ['bi-upc',      'ISBN',               $b['isbn']          ?? '—', 'mono'],
                ['bi-tag-fill', 'Prezzo',             (isset($b['price']) ? '€ '.number_format((float)$b['price'],2,',','.') : '—'), 'price'],
            ];

            foreach ($fields as $field): // DE COMMENTARE FORSE? FORSE NO
                $ico   = $field[0];
                $lbl   = $field[1];
                $val   = $field[2];
                $style = $field[3];
            ?>

            <div>
                <span class="field-label"><i class="bi <?= $ico ?> mr-1"></i><?= $lbl ?></span>
                <?php if ($style === 'badge'): ?>
                    <p class="field-value"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700"><?= htmlspecialchars($val) ?></span></p>
                <?php elseif ($style === 'mono'): ?>
                    <p class="field-value font-mono text-sm"><?= htmlspecialchars($val) ?></p>
                <?php elseif ($style === 'price'): ?>
                    <p class="field-value text-lg font-bold"><?= $val ?></p>
                <?php else: ?>
                    <p class="field-value"><?= htmlspecialchars($val) ?></p>
                <?php endif; ?>
            </div>

            <?php endforeach; ?>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-wrap gap-3 justify-between items-center">
            <a href="../pages/catalogo.php" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                <i class="bi bi-arrow-left"></i> Torna al catalogo
            </a>
            <a href="modifica_libro.php?id=<?= (int)($book['id'] ?? 0) ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-sm font-medium transition-colors shadow-sm">
                <i class="bi bi-pencil-square"></i> Modifica
            </a>
        </div>

    <?php else: ?>
        <!-- MODIFICA / AGGIUNGI -->
        <form id="book-form" novalidate class="p-6 sm:p-8 space-y-6">
            <input type="hidden" id="f-id" value="<?= (int)($book['id'] ?? 0) ?>">
            <div>
                <label class="field-label" for="f-title">Titolo <span class="text-red-500">*</span></label>
                <input id="f-title" type="text" class="form-input" placeholder="Es. Il Nome della Rosa" value="<?= htmlspecialchars($book['title'] ?? '') ?>" required>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="field-label" for="f-author">Autore <span class="text-red-500">*</span></label>
                    <input list="list-authors" id="f-author" class="form-input" placeholder="Cerca o inserisci nuovo..." required value="<?= htmlspecialchars($book['author_name'] ?? '') ?>" autocomplete="off">
                    <datalist id="list-authors">
                        <?= bookDatalistOpts($authors, 'id', 'name') ?>
                    </datalist>
                </div>
                <div>
                    <label class="field-label" for="f-year">Anno pubblicazione</label>
                    <input id="f-year" type="number" class="form-input" placeholder="<?= date('Y') ?>" min="1000" max="<?= date('Y')+2 ?>" value="<?= htmlspecialchars($book['publish_year'] ?? '') ?>">
                </div>
                <div>
                    <label class="field-label" for="f-category">Categoria <span class="text-red-500">*</span></label>
                    <input list="list-categories" id="f-category" class="form-input" placeholder="Cerca o inserisci nuovo..." required value="<?= htmlspecialchars($book['category_name'] ?? '') ?>" autocomplete="off">
                    <datalist id="list-categories">
                        <?= bookDatalistOpts($categories, 'id', 'name') ?>
                    </datalist>
                </div>
                <div>
                    <label class="field-label" for="f-publisher">Casa editrice</label>
                    <input list="list-publishers" id="f-publisher" class="form-input" placeholder="Cerca o inserisci nuovo..." value="<?= htmlspecialchars($book['publisher_name'] ?? '') ?>" autocomplete="off">
                    <datalist id="list-publishers">
                        <?= bookDatalistOpts($publishers, 'id', 'name') ?>
                    </datalist>
                </div>
                <div>
                    <label class="field-label" for="f-isbn">ISBN</label>
                    <input id="f-isbn" type="text" class="form-input" placeholder="978…" value="<?= htmlspecialchars($book['isbn'] ?? '') ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '')" pattern="\d*">
                </div>
                <div>
                    <label class="field-label" for="f-price">Prezzo (€)</label>
                    <input id="f-price" type="number" step="0.01" min="0" class="form-input" placeholder="0.00" value="<?= htmlspecialchars($book['price'] ?? '') ?>">
                </div>
            </div>

            <div id="form-error" class="hidden text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg px-4 py-3 flex items-start gap-2">
                <i class="bi bi-exclamation-circle-fill flex-shrink-0 mt-0.5"></i>
                <span id="form-error-msg"></span>
            </div>
        </form>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-wrap gap-3 justify-between items-center">
            <a href="catalogo.php" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                <i class="bi bi-x-lg"></i> Annulla
            </a>
            <button id="btn-save" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg <?= $cfg['saveBg'] ?> text-white text-sm font-medium transition-colors shadow-sm">
                <i class="bi <?= $cfg['saveIcon'] ?>"></i> <?= htmlspecialchars($cfg['saveLabel']) ?>
            </button>
        </div>
    <?php endif; ?>

    </div>
</div>

<?php if ($bookMode !== 'view'): ?>
    
<script>
(function() {

    const isEdit = <?= $bookMode === 'edit' ? 'true' : 'false' ?>;
    const bookId = <?= (int)($book['id'] ?? 0) ?>;

    const btnSave = document.getElementById('btn-save');
    const errBox  = document.getElementById('form-error');
    const errMsg  = document.getElementById('form-error-msg');

    function showErr(msg) {
        errMsg.textContent = msg;
        errBox.classList.remove('hidden');
        errBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function hideErr() {
        errBox.classList.add('hidden');
    }

    function val(id) {
        return document.getElementById(id).value;
    }

    function getHybridVal(inputId, listId) {
        const text = val(inputId).trim();

        if (!text) return { id: null, new_name: null };

        const datalist = document.getElementById(listId);
        const options  = Array.from(datalist.options);
        const option   = options.find(function(opt) {
            return opt.value === text;
        });

        if (option) {
            return { id: parseInt(option.dataset.id, 10), new_name: null };
        } else {
            return { id: null, new_name: text };
        }
    }

    btnSave.addEventListener('click', async function() {
        hideErr();

        const title     = val('f-title').trim();
        const author    = getHybridVal('f-author',    'list-authors');
        const category  = getHybridVal('f-category',  'list-categories');
        const publisher = getHybridVal('f-publisher', 'list-publishers');

        const payload = {
            title:            title,
            author_id:        author.id,
            new_author_name:  author.new_name,
            category_id:      category.id,
            new_category_name: category.new_name,
            publisher_id:     publisher.id,
            new_publisher_name: publisher.new_name,
            isbn:         val('f-isbn').trim() || null,
            price:        val('f-price') || 0,
            publish_year: val('f-year')  || null
        };

        // Validazione campi obbligatori
        if (!title) {
            showErr('Il titolo è obbligatorio.');
            document.getElementById('f-title').classList.add('error');
            return;
        }

        if (!author.id && !author.new_name) {
            showErr('Seleziona o inserisci un autore.');
            document.getElementById('f-author').classList.add('error');
            return;
        }

        if (!category.id && !category.new_name) {
            showErr('Seleziona o inserisci una categoria.');
            document.getElementById('f-category').classList.add('error');
            return;
        }

        // Disabilita il pulsante durante il salvataggio
        btnSave.disabled = true;
        btnSave.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> Salvataggio…';

        try {
            const apiUrl    = isEdit ? `../api/books.php?id=${bookId}` : '../api/books.php';
            const apiMethod = isEdit ? 'PUT' : 'POST';

            const res = await fetch(apiUrl, {
                method:  apiMethod,
                headers: { 'Content-Type': 'application/json' },
                body:    JSON.stringify(payload)
            });

            const data = await res.json();

            if (!res.ok) {
                throw new Error(data.message || 'Errore sconosciuto');
            }

            window.location.href = 'catalogo.php?success=1';

        } catch (e) {
            showErr(e.message);

        } finally {
            btnSave.disabled = false;
            btnSave.innerHTML = '<i class="bi <?= $cfg['saveIcon'] ?>"></i> <?= addslashes($cfg['saveLabel']) ?>';
        }

    }); // chiude btnSave.addEventListener

    // Rimuove il bordo rosso quando l'utente inizia a correggere
    const fieldIds = ['f-title', 'f-author', 'f-category'];

    fieldIds.forEach(function(id) {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input',  function() { el.classList.remove('error'); });
            el.addEventListener('change', function() { el.classList.remove('error'); });
        }
    });

})(); // chiude l'IIFE
</script>
<?php endif; ?>
<?php echo '</main></div></body></html>'; ?>
