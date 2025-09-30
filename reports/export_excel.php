<?php
// reports/export_excel.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/auth.php';
require_login();

$uid = $_SESSION['user_id'];

// Obtenemos datos
$stmt = $pdo->prepare("SELECT sku, name, description, quantity, unit_price, (quantity*unit_price) total FROM items WHERE user_id=? ORDER BY name");
$stmt->execute([$uid]);
$rows = $stmt->fetchAll();

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=inventario.xls");
echo "<table border='1'>";
echo "<tr><th>SKU</th><th>Nombre</th><th>Descripción</th><th>Cantidad</th><th>Precio</th><th>Total</th></tr>";
foreach ($rows as $r) {
  echo "<tr>";
  echo "<td>".htmlspecialchars($r['sku'])."</td>";
  echo "<td>".htmlspecialchars($r['name'])."</td>";
  echo "<td>".htmlspecialchars($r['description'])."</td>";
  echo "<td>".(int)$r['quantity']."</td>";
  echo "<td>".number_format((float)$r['unit_price'],2)."</td>";
  echo "<td>".number_format((float)$r['total'],2)."</td>";
  echo "</tr>";
}
echo "</table>";
