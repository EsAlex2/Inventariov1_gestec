<?php
// public/dashboard.php
require_once __DIR__ . '/_layout_top.php';
require_login();

// Stats
$uid = $_SESSION['user_id'];
$totalItems = $pdo->prepare("SELECT COUNT(*) c FROM items WHERE user_id = ?");
$totalItems->execute([$uid]); $count = (int)$totalItems->fetch()['c'];

$totalQty = $pdo->prepare("SELECT COALESCE(SUM(quantity),0) s FROM items WHERE user_id = ?");
$totalQty->execute([$uid]); $sumQty = (int)$totalQty->fetch()['s'];

$totalValue = $pdo->prepare("SELECT COALESCE(SUM(quantity*unit_price),0) v FROM items WHERE user_id = ?");
$totalValue->execute([$uid]); $sumVal = (float)$totalValue->fetch()['v'];

// Data para gráfico: valor por categoría
$catStmt = $pdo->prepare("SELECT COALESCE(c.name,'Sin categoría') as cat, COALESCE(SUM(i.quantity*i.unit_price),0) val FROM items i LEFT JOIN categories c ON i.category_id=c.id WHERE i.user_id=? GROUP BY IFNULL(i.category_id,0) ORDER BY val DESC LIMIT 12");
$catStmt->execute([$uid]); $catData = $catStmt->fetchAll();
$catLabels = array_map(function($r){ return $r['cat']; }, $catData);
$catValues = array_map(function($r){ return (float)$r['val']; }, $catData);
?>
<div class="card">
  <h2>Dashboard</h2>
  <p style="color:var(--muted);">Visión general de tu inventario.</p>
  <div class="form-grid three" style="margin-top:12px;">
    <div class="card">
      <div style="font-size:12px; color:var(--muted)">Ítems</div>
      <div style="font-size:28px; font-weight:800;"><?=h($count)?></div>
    </div>
    <div class="card">
      <div style="font-size:12px; color:var(--muted)">Existencias</div>
      <div style="font-size:28px; font-weight:800;"><?=h($sumQty)?></div>
    </div>
    <div class="card">
      <div style="font-size:12px; color:var(--muted)">Valor total</div>
      <div style="font-size:28px; font-weight:800;">$<?=number_format($sumVal,2)?></div>
    </div>
  </div>

  <div style="margin-top:18px;" class="card">
    <h3>Valor por categoría</h3>
    <canvas id="catChart" width="400" height="140"></canvas>
  </div>

  <div style="margin-top:12px;">
    <a class="button" href="items.php">Gestionar inventario →</a>
    <a class="button ghost" href="../reports/export_excel.php">Descargar Excel</a>
    <a class="button ghost" href="../reports/export_pdf.php">Descargar PDF</a>
  </div>
</div>

<?php require_once __DIR__ . '/_layout_bottom.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('catChart');
if (ctx) {
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: <?=json_encode($catLabels)?>,
      datasets: [{ label: 'Valor ($)', data: <?=json_encode($catValues)?>, tension: .3 }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
  });
}
</script>
