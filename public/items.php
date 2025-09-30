<?php
// public/items.php (AJAX-enhanced)
require_once __DIR__ . '/_layout_top.php';
require_login();
$uid = $_SESSION['user_id'];

// Pagination params
$perPage = 12;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page-1)*$perPage;

$search = trim($_GET['q'] ?? '');
$params = [$uid];
$where = " WHERE i.user_id = ? ";
if ($search) { $where .= " AND (i.sku LIKE ? OR i.name LIKE ?) "; $like="%$search%"; $params[]=$like; $params[]=$like; }

$totalStmt = $pdo->prepare("SELECT COUNT(*) c FROM items i $where");
$totalStmt->execute($params);
$total = (int)$totalStmt->fetch()['c'];
$pages = max(1, ceil($total/$perPage));

$stmt = $pdo->prepare("SELECT i.*, c.name as category, s.name as supplier FROM items i LEFT JOIN categories c ON i.category_id=c.id LEFT JOIN suppliers s ON i.supplier_id=s.id $where ORDER BY i.created_at DESC LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$items = $stmt->fetchAll();

$cats = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$sups = $pdo->query("SELECT * FROM suppliers ORDER BY name")->fetchAll();
?>
<div class="card">
  <div class="header" style="margin-bottom:8px;">
    <h2>Inventario</h2>
    <div style="display:flex; gap:8px; align-items:center;">
      <form method="get" style="display:flex; gap:8px;">
        <input class="input" type="text" name="q" placeholder="Buscar por SKU/Nombre" value="<?=h($search)?>">
        <button class="button ghost" type="submit">Buscar</button>
      </form>
      <button class="button" id="refreshBtn">Refrescar</button>
    </div>
  </div>

  <form id="createItemForm" data-validate class="card">
    <h3>Nuevo ítem</h3>
    <input type="hidden" name="action" value="create_item">
    <div class="form-grid two">
      <input class="input" type="text" name="sku" placeholder="SKU" required>
      <input class="input" type="text" name="name" placeholder="Nombre" required>
      <input class="input" type="number" name="quantity" placeholder="Cantidad" min="0" value="0">
      <input class="input" type="number" step="0.01" name="unit_price" placeholder="Precio unitario" min="0" value="0.00">
    </div>
    <div class="form-grid two" style="margin-top:8px;">
      <select class="input" name="category_id">
        <option value="">-- Categoría --</option>
        <?php foreach($cats as $c): ?><option value="<?=h($c['id'])?>"><?=h($c['name'])?></option><?php endforeach; ?>
      </select>
      <select class="input" name="supplier_id">
        <option value="">-- Proveedor --</option>
        <?php foreach($sups as $s): ?><option value="<?=h($s['id'])?>"><?=h($s['name'])?></option><?php endforeach; ?>
      </select>
    </div>
    <textarea class="input" name="description" rows="3" placeholder="Descripción opcional"></textarea>
    <div style="margin-top:10px; display:flex; gap:8px; justify-content:flex-end;">
      <input type="submit" class="button" value="Agregar ítem">
    </div>
  </form>

  <table class="table">
    <thead>
      <tr>
        <th>ID</th><th>SKU</th><th>Nombre</th><th>Cat.</th><th>Prov.</th><th>Cant.</th><th>Precio</th><th>Valor</th><th>Acciones</th>
      </tr>
    </thead>
    <tbody id="itemsBody">
      <?php foreach ($items as $it): ?>
        <tr class="fade-in" data-id="<?=h($it['id'])?>">
          <td><?=h($it['id'])?></td>
          <td><?=h($it['sku'])?></td>
          <td><?=h($it['name'])?></td>
          <td><?=h($it['category'] ?? '-')?></td>
          <td><?=h($it['supplier'] ?? '-')?></td>
          <td><?=h($it['quantity'])?></td>
          <td>$<?=number_format($it['unit_price'],2)?></td>
          <td>$<?=number_format($it['quantity']*$it['unit_price'],2)?></td>
          <td>
            <details>
              <summary class="button ghost" style="display:inline-block;">Editar</summary>
              <form class="editForm" data-id="<?=h($it['id'])?>" style="margin-top:8px; display:grid; gap:6px;">
                <input type="hidden" name="action" value="update_item">
                <input type="hidden" name="id" value="<?=h($it['id'])?>">
                <input class="input" type="text" name="sku" value="<?=h($it['sku'])?>" required>
                <input class="input" type="text" name="name" value="<?=h($it['name'])?>" required>
                <input class="input" type="number" name="quantity" min="0" value="<?=h($it['quantity'])?>">
                <input class="input" type="number" step="0.01" name="unit_price" min="0" value="<?=h($it['unit_price'])?>">
                <select class="input" name="category_id">
                  <option value="">-- Categoría --</option>
                  <?php foreach($cats as $c): ?><option value="<?=h($c['id'])?>" <?=($it['category_id']==$c['id']?'selected':'')?>><?=h($c['name'])?></option><?php endforeach; ?>
                </select>
                <select class="input" name="supplier_id">
                  <option value="">-- Proveedor --</option>
                  <?php foreach($sups as $s): ?><option value="<?=h($s['id'])?>" <?=($it['supplier_id']==$s['id']?'selected':'')?>><?=h($s['name'])?></option><?php endforeach; ?>
                </select>
                <textarea class="input" name="description" rows="2"><?=h($it['description'])?></textarea>
                <div style="display:flex; gap:6px;">
                  <button class="button saveBtn">Guardar</button>
                  <button class="button ghost deleteBtn" type="button">Eliminar</button>
                </div>
              </form>
            </details>
            <div style="margin-top:6px;">
              <button class="button ghost movBtn" data-id="<?=h($it['id'])?>">Movimiento</button>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($items)): ?>
        <tr><td colspan="9" style="color:var(--muted);">Sin datos aún. Agrega tu primer ítem ↑</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <!-- Paginación -->
  <div style="margin-top:12px; display:flex; gap:8px; justify-content:center; align-items:center;">
    <?php if($page>1): ?><a class="button ghost" href="items.php?page=<?=$page-1?>&q=<?=urlencode($search)?>">« Anterior</a><?php endif; ?>
    <span style="color:var(--muted)">Página <?=$page?> de <?=$pages?></span>
    <?php if($page<$pages): ?><a class="button ghost" href="items.php?page=<?=$page+1?>&q=<?=urlencode($search)?>">Siguiente »</a><?php endif; ?>
  </div>

</div>

<?php require_once __DIR__ . '/_layout_bottom.php'; ?>

<script>
// AJAX helpers
async function postForm(url, form) {
  const fd = new FormData(form);
  const res = await fetch(url, { method: 'POST', body: fd });
  return res.json();
}

document.getElementById('createItemForm')?.addEventListener('submit', async function(e){
  e.preventDefault();
  const res = await postForm('api.php', this);
  if (res.ok) { showToast(res.msg,'success'); setTimeout(()=>location.reload(),700); }
  else showToast(res.msg,'danger');
});

document.querySelectorAll('.editForm').forEach(f => {
  f.querySelector('.saveBtn').addEventListener('click', async (e)=>{
    e.preventDefault();
    const res = await postForm('api.php', f);
    if (res.ok) { showToast(res.msg,'success'); setTimeout(()=>location.reload(),700); }
    else showToast(res.msg,'danger');
  });
  f.querySelector('.deleteBtn').addEventListener('click', async (e)=>{
    if (!confirm('Eliminar ítem?')) return;
    const id = f.querySelector('input[name=id]').value;
    const fd = new FormData(); fd.append('action','delete_item'); fd.append('id', id);
    const res = await fetch('api.php',{method:'POST', body:fd}).then(r=>r.json());
    if (res.ok) { showToast(res.msg,'success'); setTimeout(()=>location.reload(),700); }
    else showToast(res.msg,'danger');
  });
});

document.querySelectorAll('.movBtn').forEach(b => {
  b.addEventListener('click', ()=>{
    const id = b.dataset.id;
    const qty = prompt('Cantidad (use negativo para salida o registre tipo):', '1');
    if (!qty) return;
    const type = parseInt(qty) < 0 ? 'out' : 'in';
    const q = Math.abs(parseInt(qty));
    const fd = new FormData(); fd.append('action','create_movement'); fd.append('item_id', id); fd.append('type', type); fd.append('quantity', q); fd.append('note','Mov manual');
    fetch('api.php',{method:'POST', body:fd}).then(r=>r.json()).then(res=>{
      if (res.ok) { showToast(res.msg,'success'); setTimeout(()=>location.reload(),700); }
      else showToast(res.msg,'danger');
    });
  });
});

document.getElementById('refreshBtn')?.addEventListener('click', ()=>location.reload());
</script>
