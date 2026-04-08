<?php
include('c:/xampp/htdocs/sigmade_php/CRUD/conexion.php');

$triggers = [
    "DROP TRIGGER IF EXISTS actualizar_material_eliminacion",
    "CREATE TRIGGER actualizar_material_eliminacion
    AFTER DELETE ON detalle_prestamo
    FOR EACH ROW
    BEGIN
        UPDATE material SET disponible = 'Libre' WHERE id = OLD.material_id;
    END",

    "DROP TRIGGER IF EXISTS actualizar_material_estado",
    "CREATE TRIGGER actualizar_material_estado
    AFTER UPDATE ON prestamo
    FOR EACH ROW
    BEGIN
        IF NEW.estado_general IN ('Finalizado', 'Denegado', 'Devuelto', 'Cancelado', 'Entregado') THEN
            UPDATE material 
            SET disponible = 'Libre' 
            WHERE id IN (SELECT material_id FROM detalle_prestamo WHERE prestamo_id = NEW.id);
        ELSEIF NEW.estado_general IN ('Activo', 'Prestado') THEN
            UPDATE material 
            SET disponible = 'Prestado' 
            WHERE id IN (SELECT material_id FROM detalle_prestamo WHERE prestamo_id = NEW.id);
        END IF;
    END"
];

foreach ($triggers as $t) {
    if (mysqli_query($conn, $t)) {
        echo "Exito ejecutando: \n" . substr($t, 0, 50) . "...\n";
    } else {
        echo "Error: " . mysqli_error($conn) . "\nEn query: $t\n";
    }
}
?>
