<?php
// config/config.php
// Ajusta estas constantes a tu entorno local (XAMPP/MAMP/WAMP).
define('DB_HOST', 'localhost');
define('DB_NAME', 'inventario_v1');
define('DB_USER', 'root');
define('DB_PASS', '');

// URL base (sin la barra final). Ejemplo: http://localhost/inventario_v1/public
define('BASE_URL', '/inventario_v1/public');

// SMTP (PHPMailer) - configura estos valores para que el sistema envíe correos.
// Si usas Gmail con autenticación moderna, crea una contraseña de aplicación o usa un SMTP relay.
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'usuario@example.com');
define('SMTP_PASS', 'tu_contraseña_smtp');
define('SMTP_FROM', 'no-reply@example.com');
define('SMTP_FROM_NAME', 'Inventario v3');
?>