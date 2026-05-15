<?php
/**
 * Script de diagnóstico de conexión a base de datos
 * Acceso: https://stokmaster.com.co/test_conexion.php
 */

// Intentar obtener variables de entorno
$host = getenv('DB_HOST') ?: getenv('DATABASE_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: getenv('DATABASE_PORT') ?: '3306';
$user = getenv('DB_USER') ?: getenv('DATABASE_USER') ?: 'no_user';
$pass = getenv('DB_PASSWORD') ?: getenv('DATABASE_PASSWORD') ?: 'no_password';
$db   = getenv('DB_NAME') ?: getenv('DATABASE_NAME') ?: getenv('DB_DATABASE') ?: 'no_db';

echo "<h2>Diagnóstico de Conexión</h2>";
echo "<b>Host:</b> $host<br>";
echo "<b>Puerto:</b> $port<br>";
echo "<b>Usuario:</b> $user<br>";
echo "<b>Base de Datos:</b> $db<br>";
echo "<hr>";

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5, // 5 segundos de timeout
    ];
    
    echo "Intentando conectar...<br>";
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    echo "<h3 style='color:green'>✅ ¡CONEXIÓN EXITOSA!</h3>";
    echo "Versión del servidor: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
    
} catch (PDOException $e) {
    echo "<h3 style='color:red'>❌ ERROR DE CONEXIÓN</h3>";
    echo "<b>Mensaje:</b> " . $e->getMessage() . "<br>";
    echo "<b>Código:</b> " . $e->getCode() . "<br>";
    
    if (strpos($e->getMessage(), 'timed out') !== false) {
        echo "<br><b>Sugerencia:</b> Esto parece un bloqueo de FIREWALL. Asegúrate de permitir la IP del servidor en el panel de tu base de datos.";
    }
}

echo "<hr>";
echo "<b>Variables de entorno detectadas:</b><pre>";
print_r($_ENV);
echo "</pre>";
