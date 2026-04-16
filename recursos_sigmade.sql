-- =========================================================================
-- SCRIPT DE INYECCIÓN LÓGICA (ACADEMICO) - SIGMADE DB
-- Contiene: Triggers, Procedimientos Almacenados y Transacciones ACID.
-- =========================================================================

DELIMITER //

-- ========================================================
-- 1. PROCEDIMIENTO ALMACENADO CON TRANSACCIÓN EXPLÍCITA
-- ========================================================
-- Objetivo: Finalizar un préstamo y regresar los materiales 
-- al inventario de manera Atómica (Todo o Nada).
-- ========================================================
DROP PROCEDURE IF EXISTS SP_FinalizarPrestamoSQL //
CREATE PROCEDURE SP_FinalizarPrestamoSQL(
    IN p_prestamo_id INT, 
    IN p_observaciones TEXT
)
BEGIN
    -- Declarar el manejador de errores: Si cualquier Insert, Delete o Update falla, entra aquí y ejecuta ROLLBACK
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        -- Cancelar toda la operación temporal para evitar registros huérfanos
        ROLLBACK;
        -- Levantar una alerta para la interfaz web
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Error Transaccional: Inconsistencia detectada. Se aplicó ROLLBACK.';
    END;

    -- INICIO FORMAL DE LA TRANSACCIÓN ACID EN MYSQL
    START TRANSACTION;
    
    -- Operación 1: Cambiar el estado principal del modelo y guardar firmas/observaciones
    UPDATE prestamo 
    SET estado_general = 'Finalizado', 
        observaciones = p_observaciones 
    WHERE id = p_prestamo_id;
    
    -- Operación 2: Bucle interno/Joins masivo para recuperar los equipos al catálogo
    UPDATE material m 
    INNER JOIN detalle_prestamo dp ON m.id = dp.material_id
    SET m.disponible = 'Libre'
    WHERE dp.prestamo_id = p_prestamo_id;

    -- SI NINGUNA OPERACIÓN FALLÓ, ATERRIZAR CAMBIOS FISICAMENTE (Durabilidad)
    COMMIT;
END //

-- ========================================================
-- 2. TRIGGER DEFINIDO POR EL USUARIO (BEFORE INSERT)
-- ========================================================
-- Objetivo: Cancelación proactiva y seguridad en cascada.
-- Impide mediante la Base de Datos (no mediante el código PHP)
-- que un usuario que tiene estado "Sancionado" inserte un préstamo.
-- ========================================================
DROP TRIGGER IF EXISTS tr_bloqueo_sancionados_prestamos //
CREATE TRIGGER tr_bloqueo_sancionados_prestamos
BEFORE INSERT ON prestamo
FOR EACH ROW
BEGIN
    DECLARE var_estado_usuario VARCHAR(20);
    
    -- Buscar la integridad y estado actual del usuario que intenta pedir prestado
    SELECT estatus INTO var_estado_usuario 
    FROM usuario 
    WHERE id = NEW.usuario_id;
    
    -- Lógica discriminatoria
    IF var_estado_usuario = 'Sancionado' THEN
        -- Abortar el proceso de inserción desde el motor
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Violación de Negocio: El estudiante o docente se encuentra Sancionado. Rechazando INSERT.';
    END IF;
END //

-- ========================================================
-- 3. TRIGGER DEFINIDO POR EL USUARIO (AFTER UPDATE)
-- ========================================================
-- Objetivo: Generar bitácora automática de auditorías.
-- Audita estructuralmente al sistema si un admin cambia a un usuario.
-- * Nota: Requiere una mini tabla de bitácora primero.
-- ========================================================
CREATE TABLE IF NOT EXISTS auditoria_estatus_usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    estatus_viejo VARCHAR(50),
    estatus_nuevo VARCHAR(50),
    modificado_en DATETIME DEFAULT CURRENT_TIMESTAMP
) //

DROP TRIGGER IF EXISTS tr_auditoria_estatus_usuarios //
CREATE TRIGGER tr_auditoria_estatus_usuarios
AFTER UPDATE ON usuario
FOR EACH ROW
BEGIN
    -- Comparación a nivel de fila (Motor MySQL)
    IF OLD.estatus != NEW.estatus THEN
        -- Rastrear y registrar automáticamente la alteración en el diario de la BD
        INSERT INTO auditoria_estatus_usuarios (usuario_id, estatus_viejo, estatus_nuevo) 
        VALUES (NEW.id, OLD.estatus, NEW.estatus);
    END IF;
END //

DELIMITER ;
