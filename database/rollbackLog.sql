ALTER TABLE `huellitas`.`animales`
DROP FOREIGN KEY `animales_ultusuariomdf_foreign`,
DROP FOREIGN KEY `animales_idcreador_foreign`,
DROP FOREIGN KEY `animales_ibfk_1`;
ALTER TABLE `huellitas`.`animales`
DROP INDEX `idZona` ,
DROP INDEX `animales_ultusuariomdf_foreign` ,
DROP INDEX `animales_idcreador_foreign`;

ALTER TABLE `huellitas`.`animales_encontrados`
DROP FOREIGN KEY `animales_encontrados_ultusuariomdf_foreign`,
DROP FOREIGN KEY `animales_encontrados_idcreador_foreign`,
DROP FOREIGN KEY `animales_encontrados_ibfk_1`;
ALTER TABLE `huellitas`.`animales_encontrados`
DROP INDEX `animales_encontrados_ultusuariomdf_foreign` ,
DROP INDEX `animales_encontrados_idcreador_foreign` ,
DROP INDEX `animales_encontrados_unique`;

ALTER TABLE `huellitas`.`animales_perdidos`
DROP FOREIGN KEY `animales_perdidos_ultusuariomdf_foreign`,
DROP FOREIGN KEY `animales_perdidos_idcreador_foreign`,
DROP FOREIGN KEY `animales_perdidos_ibfk_1`;
ALTER TABLE `huellitas`.`animales_perdidos`
DROP INDEX `animales_perdidos_ultusuariomdf_foreign` ,
DROP INDEX `animales_perdidos_idcreador_foreign` ,
DROP INDEX `animales_perdidos_unique` ;

DROP TABLE `huellitas`.`animales_perdidos`;
DROP TABLE `huellitas`.`animales_encontrados`;
DROP TABLE `huellitas`.`animales`;