<?php
require_once("./config/databaseConfig.php");

if ($conn) {
    echo "✅ Conexión exitosa.";
} else {
    echo "❌ No se pudo conectar.";
}
?>