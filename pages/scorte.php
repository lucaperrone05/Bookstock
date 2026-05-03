<?php
require_once '../includes/auth_check.php';
$activePage = 'scorte';
$pageTitle  = 'Gestione Scorte';
$pageSubtitle = 'Aggiungi o rimuovi quantità dal magazzino';

require_once '../includes/db.php';

// Fetch all books per la tendina
$books = $pdo->query("SELECT id, title, stock_qty FROM books ORDER BY title ASC")->fetchAll();
$selectedBookId = isset($_GET['book_id']) ? (int)$_GET['book_id'] : null;

include '../includes/layout.php';
?>

<!-- TomSelect CSS per la ricerca dinamica -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<style>
/* Nascondi frecce native dal number input */
input[type="number"]::-webkit-inner-spin-button, 
input[type="number"]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
input[type="number"] { -moz-appearance: textfield; }

/* Adattamento stile TomSelect per integrarlo con Tailwind */
.ts-control { border-radius: 0.5rem; padding: 0.65rem 1rem; border-color: #e5e7eb; font-size: 1rem; }
.ts-control.focus { border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5); }
.ts-dropdown { border-radius: 0.5rem; font-size: 0.875rem; }
</style>

<div class="max-w-3xl mx-auto space-y-6 animate-[fadeInUp_0.3s_ease]">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        
        <div class="p-5 border-b border-gray-100 bg-gray-50 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 flex-shrink-0">
                <i class="bi bi-box-seam text-lg"></i>
            </div>
            <div>
                <h2 class="font-semibold text-gray-800">Aggiorna Giacenza</h2>
            </div>
        </div>
        
        <form id="stock-form" class="p-6 space-y-8">
            <div id="alert-container" class="hidden rounded-lg p-4 text-sm font-medium"></div>

            <!-- Selezione Libro con TomSelect -->
            <div>
                <label for="book_id" class="block text-sm font-medium text-gray-700 mb-2">Seleziona Libro *</label>
                <select id="book_id" name="book_id" placeholder="Cerca libro per titolo..." required>
                    <option value="">Cerca libro per titolo...</option>
                    <?php foreach ($books as $b): ?>
                        <option value="<?= $b['id'] ?>" data-stock="<?= $b['stock_qty'] ?>" <?= $b['id'] == $selectedBookId ? 'selected' : '' ?>>
                            <?= htmlspecialchars($b['title']) ?> (Attuali: <?= $b['stock_qty'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <p id="current-stock-helper" class="mt-2 text-sm text-gray-600 <?= $selectedBookId ? '' : 'hidden' ?> bg-blue-50 p-2.5 rounded-lg border border-blue-100 flex items-center gap-2">
                    <i class="bi bi-info-circle text-blue-500"></i>
                    <span>Scorte attuali: <strong id="current-stock-val" class="text-blue-700 text-lg"></strong></span>
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Tipo Operazione e Quantità *</label>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-6 bg-gray-50 p-5 rounded-xl border border-gray-200">
                    
                    <!-- Carico -->
                    <label class="cursor-pointer flex-1 w-full max-w-[200px]">
                        <input type="radio" name="type" value="carico" class="peer sr-only" checked>
                        <div class="rounded-xl border-2 border-gray-200 p-3 hover:bg-white peer-checked:border-green-500 peer-checked:bg-green-50 transition-all text-center h-full">
                            <i class="bi bi-box-arrow-in-down text-2xl text-green-600 block mb-1"></i>
                            <div class="font-bold text-gray-800">Carico</div>
                        </div>
                    </label>

                    <!-- Quantità -->
                    <div class="flex items-center justify-center gap-3">
                        <button type="button" onclick="changeQty(-1)" class="w-10 h-10 rounded-full border border-gray-300 bg-white hover:bg-red-50 text-gray-600 hover:text-red-600 focus:ring-2 focus:ring-red-500 transition-colors shadow-sm"><i class="bi bi-dash font-bold text-xl"></i></button>
                        <div class="relative">
                            <input type="number" id="quantity" name="quantity" min="1" value="1" required class="w-20 text-center py-2.5 border-2 border-gray-300 rounded-xl font-bold text-xl focus:border-blue-500 focus:ring-0 outline-none shadow-inner">
                        </div>
                        <button type="button" onclick="changeQty(1)" class="w-10 h-10 rounded-full border border-gray-300 bg-white hover:bg-green-50 text-gray-600 hover:text-green-600 focus:ring-2 focus:ring-green-500 transition-colors shadow-sm"><i class="bi bi-plus font-bold text-xl"></i></button>
                    </div>

                    <!-- Scarico -->
                    <label class="cursor-pointer flex-1 w-full max-w-[200px]">
                        <input type="radio" name="type" value="scarico" class="peer sr-only">
                        <div class="rounded-xl border-2 border-gray-200 p-3 hover:bg-white peer-checked:border-red-500 peer-checked:bg-red-50 transition-all text-center h-full">
                            <i class="bi bi-box-arrow-up text-2xl text-red-600 block mb-1"></i>
                            <div class="font-bold text-gray-800">Scarico</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-4 border-t border-gray-100 flex justify-end">
                <button type="submit" id="btn-submit" class="bg-gray-800 hover:bg-gray-900 text-white font-medium px-6 py-3 rounded-xl shadow-md transition-all w-full sm:w-auto">
                    <i class="bi bi-save mr-2"></i> Conferma
                </button>
            </div>
        </form>
    </div>
</div>

<!-- TomSelect JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>

const selectControl = new TomSelect('#book_id', {
    create: false,
    sortField: { field: "text", direction: "asc" },
    onChange: function(value) {
        const helper = document.getElementById('current-stock-helper');
        if (!value) return helper.classList.add('hidden');

        // legge sempre dal <option> reale nel DOM, non dall'oggetto interno di TomSelect
        const originalOption = document.querySelector(`#book_id option[value="${value}"]`);
        if (!originalOption) return;
        document.getElementById('current-stock-val').textContent = originalOption.dataset.stock;
        helper.classList.remove('hidden');
    }
});

// Mostra giacenza se c'è un libro passato tramite URL (da alert.php)
<?php if ($selectedBookId): ?>
    selectControl.trigger('change', <?= $selectedBookId ?>);
<?php endif; ?>

// logica Pulsanti Quantità
const qtyInput = document.getElementById('quantity');
function changeQty(delta) {
    let current = parseInt(qtyInput.value) || 1;
    let next = current + delta;
    qtyInput.value = next < 1 ? 1 : next;
}
qtyInput.addEventListener('change', () => {
    if (parseInt(qtyInput.value) < 1 || isNaN(qtyInput.value)) qtyInput.value = 1;
});

// Logica Submit
const form = document.getElementById('stock-form');
const alertContainer = document.getElementById('alert-container');
const btnSubmit = document.getElementById('btn-submit');

function showAlert(msg, isSuccess) {
    alertContainer.innerHTML = `<i class="bi ${isSuccess ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'} mr-2"></i> ${msg}`;
    alertContainer.className = `p-4 rounded-lg text-sm font-medium mb-4 flex items-center ${isSuccess ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200'}`;
    alertContainer.classList.remove('hidden');
    alertContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!selectControl.getValue()) return showAlert('Per favore, seleziona un libro.', false);
    
    alertContainer.classList.add('hidden');
    const data = {
        book_id: selectControl.getValue(),
        type: document.querySelector('input[name="type"]:checked').value,
        quantity: qtyInput.value
    };

    try {
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = 'Salvataggio in corso...';
        
        const res = await fetch('../api/stock.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await res.json();

        if (res.ok && result.success) {
            showAlert('Giacenza aggiornata! Nuova giacenza: <strong class="ml-1 text-lg">' + result.data.new_stock + '</strong>', true);
            
            // aggiorna il data-stock nel <option> reale del DOM
            const originalOption = document.querySelector(`#book_id option[value="${data.book_id}"]`);
            if (originalOption) originalOption.dataset.stock = result.data.new_stock;

            // Aggiorna solo il testo nell'oggetto TomSelect (senza passare .element)
            const optionObj = selectControl.options[data.book_id];
            const newText = optionObj.text.replace(/\(Attuali: \d+\)/, `(Attuali: ${result.data.new_stock})`);
            selectControl.updateOption(data.book_id, { value: data.book_id, text: newText });

            // Reset quantità e selezione
            qtyInput.value = 1;
            selectControl.clear();
        } else {
            showAlert(result.message || 'Si è verificato un errore.', false);
        }
    } catch (err) {
        showAlert('Errore di rete. Impossibile connettersi al server.', false);
    } finally {
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = '<i class="bi bi-save mr-2"></i> Conferma';
    }
});
</script>

<?php
// Chiudi il layout
echo '        </main>
    </div>
</body>
</html>';
?>