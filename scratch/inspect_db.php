<?php
include("../CRUD/conexion.php");

echo "TABLES:\n";
$res = mysqli_query($conn, "SHOW TABLES");
while ($row = mysqli_fetch_array($res)) {
    $table = $row[0];
    echo "- $table\n";
    $cols = mysqli_query($conn, "SHOW COLUMNS FROM $table");
    while ($col = mysqli_fetch_assoc($cols)) {
        echo "  - " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
}
?>
