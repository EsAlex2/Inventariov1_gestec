<?php
// config/config.php
// Ajusta estas constantes a tu entorno local (XAMPP/MAMP/WAMP).
define('DB_HOST', 'localhost');
define('DB_NAME', 'inventario_v1');
define('DB_USER', 'esalex_admin');
define('DB_PASS', '28011999..');

// URL base (sin la barra final). Ejemplo: http://localhost/public
define('BASE_URL', '/public');

// SMTP (PHPMailer) - configura estos valores para que el sistema envíe correos.
// Si usas Gmail con autenticación moderna, crea una contraseña de aplicación o usa un SMTP relay.
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'usuario@example.com');
define('SMTP_PASS', 'tu_contraseña_smtp');
define('SMTP_FROM', 'no-reply@example.com');
define('SMTP_FROM_NAME', 'Inventario v3');
?>