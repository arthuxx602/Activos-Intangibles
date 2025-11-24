
-- CAMBIOS PARA LARAVEL 12
-- Definición de roles base
CREATE TABLE IF NOT EXISTS `rol` (
  `ID_Rol` int unsigned NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) NOT NULL,
  `Descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID_Rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `rol` (`ID_Rol`, `Nombre`, `Descripcion`) VALUES
  (1, 'Administrador', 'Acceso completo al sistema'),
  (2, 'Moderador',     'Gestión de usuarios e inversiones'),
  (3, 'Inversionista', 'Consulta de sus inversiones y reportes')
ON DUPLICATE KEY UPDATE `Nombre` = VALUES(`Nombre`), `Descripcion` = VALUES(`Descripcion`);

-- Ajustes de columnas para usuario2
ALTER TABLE `usuario2`
  ADD COLUMN IF NOT EXISTS `Fecha` date NULL AFTER `Contraseña`,
  ADD COLUMN IF NOT EXISTS `FK_ID_Rol` int unsigned NOT NULL DEFAULT 3 AFTER `Fecha`;

ALTER TABLE `usuario2`
  ADD CONSTRAINT `usuario2_fk_id_rol_foreign` FOREIGN KEY (`FK_ID_Rol`) REFERENCES `rol` (`ID_Rol`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- Ajustar la tabla pivote proyecto_usuario para que apunte a usuario2
ALTER TABLE `proyecto_usuario`
  DROP FOREIGN KEY IF EXISTS `proyecto_usuario_fk_id_usuario_foreign`,
  ADD CONSTRAINT `proyecto_usuario_fk_usuario2_foreign` FOREIGN KEY (`FK_ID_Usuario`) REFERENCES `usuario2` (`ID_Usuario`) ON DELETE CASCADE ON UPDATE CASCADE;
