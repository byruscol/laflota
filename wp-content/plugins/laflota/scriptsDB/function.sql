DROP function IF EXISTS laflota.`tieneMuestrasCriticasRecientes`;

DELIMITER $$
CREATE DEFINER=`root`@`localhost` FUNCTION laflota.`tieneMuestrasCriticasRecientes`(id int) RETURNS int
    READS SQL DATA
    DETERMINISTIC
BEGIN
	
	
  RETURN (SELECT sum(t.c) res
	FROM(
			SELECT IF(escritica = 'si',1,0) c
			FROM laflota.wp_lf_muestras
			WHERE vehiculoId = id 
			ORDER BY muestraId DESC LIMIT 6
		)t);

END$$
DELIMITER ;
