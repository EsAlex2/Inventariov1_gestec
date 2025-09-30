<?php
// public/_layout_top.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/utils.php';
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>GesTec Campus</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<div id="toast" class="toast"></div>
<div class="container">
  <div class="header">
    <div class="brand">
      <div class="logo"></div>
      <div>
        <div style="font-weight:800; letter-spacing:.3px;">GesTec Campus v1</div>
        <div style="font-size:12px; color:var(--muted)">Futurista · Rápido · Seguro</div>
      </div>
    </div>
    <div class="toolbar">
      <button class="button ghost" data-theme-toggle>🌗 Tema</button>
      <?php if (is_logged_in()): ?>
        <span class="badge success">Hola, <?=h($_SESSION['user_name'])?></span>
        <a class="button ghost" href="dashboard.php">Dashboard</a>
        <a class="button ghost" href="items.php">Inventario</a>
        <a class="button ghost" href="movements.php">Movimientos</a>
        <a class="button ghost" href="clients.php">Clientes</a>
        <a class="button" href="logout.php">Cerrar sesión</a>
      <?php else: ?>
        <a class="button ghost" href="index.php">Login</a>
        <a class="button secondary" href="signup.php">Crear cuenta</a>
      <?php endif; ?>
    </div>
  </div>
