<?php
// public/movements.php
require_once __DIR__ . '/_layout_top.php';
require_login();
$uid = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT m.*, i.name as item_name FROM movements m JOIN items i ON m.item_id=i.id WHERE m.user_id=? ORDER BY m.created_at DESC LIMIT 500");
$stmt->execute([$uid]);
$rows = $stmt->fetchAll();
?>
<div class="card">
  <h2>Historial de Movimientos</h2>
  <p style="color:var(--muted)">Últimos movimientos de entrada/salida.</p>
  <table class="table">
    <thead><tr><th>#</th><th>Item</th><th>Tipo</th><th>Cantidad</th><th>Proveedor</th><th>Cliente</th><th>Nota</th><th>Fecha</th></tr></thead>
    <tbody>
      <?php foreach($rows as $r): ?>
        <tr>
          <td><?=h($r['id'])?></td>
          <td><?=h($r['item_name'])?></td>
          <td><?=h($r['type'])?></td>
          <td><?=h($r['quantity'])?></td>
          <td><?=h($r['supplier_name'] ?? '-')?></td>
          <td><?=h($r['client_name'] ?? '-')?></td>
          <td><?=h($r['note'])?></td>
          <td><?=h($r['created_at'])?></td>
        </tr>
      <?php endforeach; ?>
      <?php if(empty($rows)): ?><tr><td colspan="6" style="color:var(--muted)">Sin movimientos aún</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
<?php require_once __DIR__ . '/_layout_bottom.php'; ?>