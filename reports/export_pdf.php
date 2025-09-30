<?php
// reports/export_pdf.php - usa Dompdf si está disponible (instala con Composer), si no fallback.
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/auth.php';
require_login();

$uid = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT sku, name, quantity, unit_price FROM items WHERE user_id=? ORDER BY name");
$stmt->execute([$uid]);
$rows = $stmt->fetchAll();

// Si autoload de Composer existe y Dompdf está instalado, generamos PDF con Dompdf
$vendor = __DIR__ . '/../vendor/autoload.php';
if (file_exists($vendor)) {
    require $vendor;
    use Dompdf\Dompdf;
    $html = '<h2>Inventario</h2><table border="1" cellspacing="0" cellpadding="6"><tr><th>SKU</th><th>Nombre</th><th>Cant.</th><th>Precio</th><th>Total</th></tr>';
    $sum = 0;
    foreach ($rows as $r) {
        $total = $r['quantity'] * $r['unit_price'];
        $sum += $total;
        $html .= '<tr><td>'.htmlspecialchars($r['sku']).'</td><td>'.htmlspecialchars($r['name']).'</td><td>'.(int)$r['quantity'].'</td><td>'.number_format($r['unit_price'],2).'</td><td>'.number_format($total,2).'</td></tr>';
    }
    $html .= '<tr><td colspan="4"><strong>TOTAL</strong></td><td>'.number_format($sum,2).'</td></tr>';
    $html .= '</table>';
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4','portrait');
    $dompdf->render();
    $dompdf->stream('inventario.pdf', ['Attachment'=>1]);
    exit;
}

// Fallback: PDF muy básico (texto)
function pdf_escape($s){ return str_replace(['\\','(',')'], ['\\\\','\(','\)'], $s); }
$lines = [];
$lines[] = "INVENTARIO - REPORTE";
$lines[] = "";
$lines[] = sprintf("%-12s %-28s %8s %10s %10s", "SKU", "Nombre", "Cant.", "Precio", "Total");
$lines[] = str_repeat('-', 72);
$sum = 0;
foreach ($rows as $r) {
    $total = (float)$r['quantity'] * (float)$r['unit_price'];
    $sum += $total;
    $lines[] = sprintf("%-12s %-28s %8d %10.2f %10.2f",
        mb_strimwidth($r['sku'],0,12,'', 'UTF-8'),
        mb_strimwidth($r['name'],0,28,'', 'UTF-8'),
        (int)$r['quantity'],
        (float)$r['unit_price'],
        $total
    );
}
$lines[] = "";
$lines[] = sprintf("%-12s %-28s %8s %10s %10.2f", "", "TOTAL", "", "", $sum);
$text = implode("\n", $lines);
$w = 595; $h = 842; $left = 40; $top = 800; $font_size = 12;
$content = "BT\n/F1 {$font_size} Tf\n{$left} {$top} Td\n(" . pdf_escape($text) . ") Tj\nET";
$pdf = "%PDF-1.4\n";
$pdf .= "1 0 obj <</Type /Catalog /Pages 2 0 R>> endobj\n";
$pdf .= "2 0 obj <</Type /Pages /Count 1 /Kids [3 0 R]>> endobj\n";
$pdf .= "3 0 obj <</Type /Page /Parent 2 0 R /MediaBox [0 0 {$w} {$h}] /Resources <</Font <</F1 4 0 R>>>> /Contents 5 0 R>> endobj\n";
$pdf .= "4 0 obj <</Type /Font /Subtype /Type1 /BaseFont /Courier>> endobj\n";
$len = strlen($content);
$pdf .= "5 0 obj <</Length {$len}>> stream\n{$content}\nendstream endobj\n";
$pdf .= "xref\n0 6\n0000000000 65535 f \n";
$pdf .= "trailer <</Size 6/Root 1 0 R>>\nstartxref\n0\n%%EOF";
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="inventario.pdf"');
echo $pdf;
