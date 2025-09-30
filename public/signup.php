<?php
// public/signup.php - registro con username, nombres, apellidos y teléfono
require_once __DIR__ . '/_layout_top.php';
if (is_logged_in()) { header("Location: dashboard.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $first = trim($_POST['first_name'] ?? '');
  $last = trim($_POST['last_name'] ?? '');
  $username = trim($_POST['username'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  $email = strtolower(trim($_POST['email'] ?? ''));
  $password = $_POST['password'] ?? '';

  if (!$first || !$last || !$username || !$email || !$password) {
    redirect_with("/signup.php", "Completa todos los campos obligatorios", "warning");
  }
  // verificar username/email
  $exists = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
  $exists->execute([$email, $username]);
  if ($exists->fetch()) {
    redirect_with("/signup.php", "Email o username ya registrado", "danger");
  }
  $hash = password_hash($password, PASSWORD_DEFAULT);
  $ins = $pdo->prepare("INSERT INTO users (username, first_name, last_name, phone, email, password_hash, role) VALUES (?,?,?,?,?,?,?)");
  $ins->execute([$username, $first, $last, $phone, $email, $hash, 'user']);
  redirect_with("/index.php", "Cuenta creada. Ya puedes iniciar sesión.", "success");
}
?>
<div class="auth-wrap">
  <div class="card auth-card scale-in">
    <h2>Crear cuenta</h2>
    <p style="color:var(--muted); margin-bottom:14px;">Configura tu inventario privado.</p>
    <form method="post" data-validate>
      <div class="form-grid two">
        <input class="input" type="text" name="first_name" placeholder="Nombres" required>
        <input class="input" type="text" name="last_name" placeholder="Apellidos" required>
      </div>
      <div class="form-grid two">
        <input class="input" type="text" name="username" placeholder="Username" required>
        <input class="input" type="tel" name="phone" placeholder="Teléfono móvil">
      </div>
      <div class="form-grid two">
        <input class="input" type="email" name="email" placeholder="Correo" required>
        <input class="input" type="password" name="password" placeholder="Contraseña" required>
      </div>
      <div class="auth-actions" style="margin-top:12px;">
        <a href="index.php" class="button ghost">Volver</a>
        <input type="submit" class="button" value="Crear cuenta →">
      </div>
    </form>
  </div>
</div>
<?php require_once __DIR__ . '/_layout_bottom.php'; ?>
