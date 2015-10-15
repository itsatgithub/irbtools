<?php
/**
 * Creación del fichero número 46 - Personal R+D Acumulat para Sirecs
 * Roberto Bartolome 2012-03-27
 * v 1.0
 * v 1.1 Roberto 2014-05-13 Añadida la verificación con password
 * 
 * Uso:
 * Abrir un navegador con el url http://tools.irbbarcelona.pcb.ub.es/getsirecs46.php
 * 
 * Los campos se justifican de acuerdo a la documentación de Php.
 * printf("[%10s]\n",    $s); // right-justification with spaces
 * printf("[%-10s]\n",   $s); // left-justification with spaces
 * 
 */

// ====================== Actualizar este bloque ======================
$FILE = "BM1446.txt"; // nombre de archivo. IMPORTANTE: el nombre del archivo contiene el año: 11 = 2011, 12 = 2012, etc.
$filename = "BM1446extra.txt"; // adding extra lines
$YEAR = "2014"; // año de referencia para generar el archivo
$START_DATE = $YEAR . "-01-01";
$END_DATE = $YEAR . "-12-31";
$CODI_INSTITUCIO = "5068"; // codigo de IRB Barcelona. Este dato no cambia nunca
$HORAS_DOCENCIA = "0000"; // horas de dedicacio setmanal a la docencia
$HORAS_DEDICACIO = "4000"; // horas de dedicacio total
$HORES_ASSISTENCIAL = "0000";
$TIPUS_ENTITAT = "2";
$CODI_ASSISTENCIAL = "A";
$VISITANT = "N";
// ====================== Actualizar este bloque ======================

if (isset($_POST['boton']))
{
	if (sha1($_POST['execution_pass']) === '52f8aebac6244bf9c810cd81ac1f6816f0da9b0f')
	{
		// save the file, do not open it
		header("Content-Disposition: attachment; filename=\"$FILE\"");
		
		// conecto la bd
		$conf = json_decode(file_get_contents('getsirecs46_configuration.json'), TRUE);
		$db = mysql_connect($conf["host"], $conf["user"], $conf["password"]) or die ('I cannot connect to the database because: ' . mysql_error());
		mysql_select_db($conf["database"]);
		
		// seleccionamos todo el personal
		$queryper = "SELECT p.personalcode"
		. " FROM personal AS p"
		. " WHERE p.deleted = ''"
		// . " AND p.state = '5'" // 2012-05-30 Roberto No miramos activos
		. " AND p.personalcode != '00434'" // 2012-05-29 Roberto Sacamos a Joan Massague de la lista
		;
		//echo $queryper . "\n";
		$resultper = mysql_query($queryper);
		if (!$resultper) {
			die('Invalid query: ' . mysql_error());
		}
		
		while ($rowper = mysql_fetch_assoc($resultper))
		{
			// calculo el año de incorporación al centro. Sera el año más pequeño
			$qu = "SELECT pro.start_date AS start_date"
			. " FROM personal AS p"
			. " LEFT JOIN professional AS pro ON pro.professional_personal = p.personalcode"
			. " WHERE p.personalcode = '" . $rowper['personalcode'] . "'"
			. " AND pro.deleted = ''"
			// 2012-06-12 Roberto Es para todos sus contratos, no solo el current
			// . " AND pro.current != ''"
			. " ORDER BY pro.start_date"
			;
			//echo $qu . "\n";
			$re = mysql_query($qu);
			if (!$re) {
				die('Invalid query: ' . mysql_error());
			}
			$ro = mysql_fetch_assoc($re);
			$date = new DateTime($ro['start_date']);
			$any_dincorporacio = $date->format('Y');
			
			// seleccionamos los datos profesionales. Puede haber varios registros si ha tenido varios contratos
			$query = "SELECT p.personalcode AS personalcode"
			. ", p.dni AS dni"
			. ", p.name AS name"
			. ", p.surname1 AS surname1"
			. ", p.surname2 AS surname2"
			. ", p.birth_date AS birth_date"
			. ", ge.description AS gender"
			. ", u.unitcode AS unitcode"
			. ", u.description AS unit"
			. ", rg.description AS research_group"
			. ", pro.email AS email"
			. ", pro.phone AS phone"
			. ", pro.location AS location"
			. ", pro.start_date AS start_date"
			. ", pro.end_date AS end_date"
			. ", pro.type_of_contract AS type_of_contract"
			. ", pro.research_group AS research_group"
			. ", pro.position AS position"
			// personal.nationality - taula complementaria 10
			. ", tc10.sirecs_code AS nationality"
			// professional.type_of_contract - taula complementaria 14
			. ", tc14.sirecs_code AS codi_tipus_vinculacio_laboral"
			// professional.position - taula complementaria 15
			. ", tc15.sirecs_code AS codi_categoria"
			// professional.position - taula complementaria 30
			. ", tc30.sirecs_code AS codi_carrec"
			// professional.payroll_institution - taula complementaria 39_1_27
			. ", tc39_1_27.sirecs_code AS codi_institucio"
			. " FROM personal AS p"
			. " LEFT JOIN professional AS pro ON pro.professional_personal = p.personalcode"
			. " LEFT JOIN position AS po ON po.positioncode = pro.position"
			. " LEFT JOIN unit AS u ON u.unitcode = pro.professional_unit"
			. " LEFT JOIN research_group AS rg ON rg.research_groupcode = pro.research_group"
			. " LEFT JOIN organization_unit AS ou ON ou.organization_unitcode = u.organization_unit"
			. " LEFT JOIN gender AS ge ON ge.gendercode = p.gender"
			// personal.nationality - taula complementaria 10
			. " LEFT JOIN sirecs_taula_complementaria_10 AS tc10 ON tc10.irb_code = p.nationality"
			// professional.type_of_contract - taula complementaria 14
			. " LEFT JOIN sirecs_taula_complementaria_14 AS tc14 ON tc14.irb_code = pro.type_of_contract"
			// professional.position - taula complementaria 15
			. " LEFT JOIN sirecs_taula_complementaria_15 AS tc15 ON tc15.irb_code = pro.position"
			// professional.position - taula complementaria 30 IMP: usa pro.position, como tc15
			. " LEFT JOIN sirecs_taula_complementaria_30 AS tc30 ON tc30.irb_code = pro.position"
			// professional.payroll_institution - taula complementaria 39_1_27
			. " LEFT JOIN sirecs_taula_complementaria_39_1_27 AS tc39_1_27 ON tc39_1_27.irb_code = pro.payroll_institution"
			. " WHERE p.personalcode = '" . $rowper['personalcode'] . "'"
			// 2012-09-26 Roberto Sacamos de la lista Visiting Scientist, Visiting Student, Visiting professor, Practices
			. " AND pro.position != '00013' AND pro.position != '00014' AND pro.position != '00029' AND pro.position != '00032'"
			. " AND ("
			// .- inicia el contrato en el pasado y, o se acaba depues del 1 de enero o es indefinido.
			. "   (pro.start_date <= '" . $START_DATE . "' AND (pro.end_date >= '" . $START_DATE . "' OR pro.end_date IS NULL))"
			// .- la fecha de inicio de contrato esta en el año de referencia
			. "   OR (pro.start_date >= '" . $START_DATE . "' AND pro.start_date <=  '" . $END_DATE . "')"
			// .- la fecha de fin de contrato esta en el año de referencia
			. "   OR (pro.end_date >= '" . $START_DATE . "' AND pro.end_date <=  '" . $END_DATE . "')"
			. " )"
			. " AND pro.deleted = ''"
			// 2012-05-30 Roberto Comento la linea de current porque puede ser un pasado
			//. " AND pro.current != ''"
			;
			//echo $query . "\n";
			$result = mysql_query($query);
			if (!$result) {
				die('Invalid query: ' . mysql_error());
			}
			// tratamos todos los posibles registros de profesional
			while ($row = mysql_fetch_assoc($result))
			{
				$reg = "";
				// any
				$reg .= sprintf("%s", $YEAR);
				// codi entitat
				$reg .= sprintf("%010s", $CODI_INSTITUCIO);
				// codi unitat descripcio
				$reg .= sprintf("%010s", $CODI_INSTITUCIO);
				// codi area de coneixement - taula complementaria 11
				$query2 = "SELECT `tc11`.`sirecs_code` AS codi_area_de_coneixement"
				. " FROM sirecs_taula_complementaria_11 AS tc11"
				. " WHERE `tc11`.`irb_code` = '" . $row['unitcode'] . "'"
				;
				//echo $query2 . "<br>";
				$result2 = mysql_query($query2);
				if (!$result2) {
					die('Invalid query: ' . mysql_error());
				}
				$row2 = mysql_fetch_assoc($result2);	
				if ($row2['codi_area_de_coneixement']) {
					$reg .= sprintf("%03s", $row2['codi_area_de_coneixement']);
				} else {
					$reg .= sprintf("%03s", "999");
				}
						
				// nif
				$reg .= sprintf("%-10s", substr(trim($row['dni']), 0, 10));
				// nif actualizado
				$reg .= sprintf("%-20s", substr(trim($row['dni']), 0, 20));	
				// nom
				// Roberto 2013-09-27 Ampliado a 20 caracteres por petición de Alba en ticket HGM-643647
				$reg .= sprintf("%-20s", substr(trim($row['name']), 0, 15));
				// 1 cognom
				$reg .= sprintf("%-20s", substr(trim($row['surname1']), 0, 20));
				// 2 cognom
				if ($row['surname2']) {
					$reg .= sprintf("%-20s", substr(trim($row['surname2']), 0, 20));	
				} else {
					$reg .= sprintf("%-20s", "                    ");	
				}
				// any de naixement
				$date = new DateTime($row['birth_date']);
				$reg .= sprintf("%4s", $date->format('Y'));
				// codi municipi naixement
				$reg .= sprintf("%5s", "     ");
				// codi provincia naixement
				$reg .= sprintf("%2s", "  ");		
				// codi pais naixement
				$reg .= sprintf("%3s", "   ");		
				// codi nacionalitat
				// hay casos como Palestina, que no tienen nacionalidad con código
				if ($row['nationality']) {
					$reg .= sprintf("%03s", $row['nationality']);
				} else {
					$reg .= sprintf("%3s", "000");
				}
				// sexe, M, F
				$char = (substr($row['gender'], 0, 1) == 'M' ? 'H':'D');
				$reg .= sprintf("%1s", $char);
				
				// 2013-03-19 Roberto. Miramos la máxima graduación hasta la fecha de fin de contrato.
				// codi grau de titulacio - taula complementaria 13
				// primero seleccionamos el grado de educación más alto
				
				// 2013-03-22 Roberto.
				// Si la fecha de fin de contrato es 'indefinido' o es mayor que el último dia del año,
				// ponemos el último día del año.
				if ($row['end_date'] == "" || $row['end_date'] > $END_DATE) {
					$aux_end_date = $END_DATE;
				} else {
					$aux_end_date = $row['end_date'];
				}
				$query2 = "SELECT `e`.`type` AS `education_code`, `toe`.`order` AS `toe_order`"
				. " FROM education AS e"
				. " LEFT JOIN type_of_education AS toe ON toe.type_of_educationcode = e.type"
				. " WHERE e.education_personal = '" . $row['personalcode'] . "'"
				// 2012-06-19 Roberto Añadido para sacar la máxima formación hasta la fecha de fin de contrato
				. " AND e.graduation_date < '" . $aux_end_date . "'"
				. " AND e.deleted = ''"
				. " AND toe.deleted = ''"
				. " ORDER BY `toe`.`order` DESC"
				;
				//echo $query2 . "\n";
				$result2 = mysql_query($query2);
				if (!$result2) {
					die('Invalid query: ' . mysql_error());
				}
				$row2 = mysql_fetch_assoc($result2);
				$education_code = (int)$row2['education_code'];
				// ahora $education_code tiene el código de educación que hay que transformar al código SIRECS
				$query3 = "SELECT `tc13`.`sirecs_code` AS codi_grau_titulacio"
				. " FROM sirecs_taula_complementaria_13 AS tc13"
				. " WHERE `tc13`.`irb_code` = '" . $education_code . "'"
				;
				//echo $query3 . "\n";
				$result3 = mysql_query($query3);
				if (!$result3) {
					die('Invalid query: ' . mysql_error());
				}
				$row3 = mysql_fetch_assoc($result3);
				if ($row3['codi_grau_titulacio']) {
					$reg .= sprintf("%1s", $row3['codi_grau_titulacio']);
				} else {
					$reg .= sprintf("%1s", "0");
				}
				
				// any d'incorporacio al centre
				$reg .= sprintf("%4s", $any_dincorporacio);	
				// codi tipus vinculacio laboral - taula complementaria 14
				if ($row['codi_tipus_vinculacio_laboral']) {
					$reg .= sprintf("%1s", $row['codi_tipus_vinculacio_laboral']);
				} else {
					$reg .= sprintf("%1s", "0");
				}
				// codi categoria - taula complementaria 15
				if ($row['codi_categoria']) {
					$reg .= sprintf("%2s", $row['codi_categoria']);	
				} else {
					$reg .= sprintf("%2s", "  ");	
				}
				// codi carrec - taula complementaria 30
				if ($row['codi_carrec']) {
					$reg .= sprintf("%2s", $row['codi_carrec']);	
				} else {
					$reg .= sprintf("%2s", "00");	
				}
				// hores de dedicacio docencia
				$reg .= sprintf("%4s", $HORAS_DOCENCIA);
				// hores de dedicacio total
				$reg .= sprintf("%4s", $HORAS_DEDICACIO);
				
				// codi subvencio
				// 22/02/2013 Actualizado para recoger los datos de la tabla 
				$query3 = "SELECT *"
				. " FROM grant_concession AS gc"
				. " WHERE gc.grant_concession_personal = '" . $row['personalcode'] . "'"
				. " AND ("
				// .- inicia el grant en el pasado y, o se acaba depues del 1 de enero o es indefinido.
				. "   (gc.start_date <= '" . $START_DATE . "' AND (gc.end_date >= '" . $START_DATE . "' OR gc.end_date IS NULL))"
				// .- la fecha de inicio de grant esta en el año de referencia
				. "   OR (gc.start_date >= '" . $START_DATE . "' AND gc.start_date <=  '" . $END_DATE . "')"
				// .- la fecha de fin de grant esta en el año de referencia
				. "   OR (gc.end_date >= '" . $START_DATE . "' AND gc.end_date <=  '" . $END_DATE . "')"
				. " )"
				. " AND gc.deleted = ''"
				;
				//echo $query3 . "\n";
				$result3 = mysql_query($query3);
				if (!$result3) {
					die('Invalid query: ' . mysql_error());
				}
				if (mysql_num_rows($result3))
				{		
					$row3 = mysql_fetch_assoc($result3);			
					// buscamos en la taula_complementaria_34 para enviar el código
					$query4 = "SELECT `tc34`.`sirecs_code` AS codi_subvencio"
					. " FROM sirecs_taula_complementaria_34 AS tc34"
					. " WHERE `tc34`.`irb_code` = '" . $row3['table_grant'] . "'"
					;
					// echo $query4 . "\n";
					$result4 = mysql_query($query4);
					if (!$result4) {
						die('Invalid query: ' . mysql_error());
					}
					$row4 = mysql_fetch_assoc($result4);
					if ($row4['codi_subvencio']) {
						$reg .= sprintf("%10s", $row4['codi_subvencio']);
					} else {
						$reg .= sprintf("%10s", "          ");
					}				
				} else {
					$reg .= sprintf("%10s", "          ");
				}
				
				// codi situacio administrativa
				// 2012-05-23 Roberto Todos son Servicio Activo
				$reg .= sprintf("%2s", "SA");
				
				// Apartat Mobilitat
				
				// codi universitat on es va...
				$reg .= sprintf("%2s", "  ");
				// codi pais de la...
				$reg .= sprintf("%3s", "   ");
				// any en que...
				$reg .= sprintf("%4s", "    ");
				// codi universitat on es va doctorar
				$reg .= sprintf("%2s", "  ");
				// codi pais de la...
				$reg .= sprintf("%3s", "   ");
				// any en que es...
				$reg .= sprintf("%4s", "    ");		
				// codi tipus entitat
				$reg .= sprintf("%s", $TIPUS_ENTITAT);
				// hores de dedicacio assistencial
				$reg .= sprintf("%s", $HORES_ASSISTENCIAL);		
				// hores de dedicacio a la recerca
				// 2014-07-22 Alba solicita el cambio en la condición
				if ($row['codi_categoria'] == '21'
					|| $row['codi_categoria'] == '26'
					|| $row['codi_categoria'] == '22'
					|| $row['codi_categoria'] == '23' 
					|| $row['codi_categoria'] == '24'
					|| $row['codi_categoria'] == '25')
				{
					// 40 a recerca, 0 a gestio
					$reg .= sprintf("%s", "4000");			
					$reg .= sprintf("%s", "0000");		

				} else {
					// 0 a recerca, 40 a gestio
					$reg .= sprintf("%s", "0000");
					$reg .= sprintf("%s", "4000");
				}
				// visitant
				$reg .= sprintf("%s", $VISITANT);
				// codi cos o escala
				$reg .= sprintf("%s", " ");
				// codi del grup
				// la variable $education_code se ha calculado más arriba para otra columna. Ahora se usa también aqui.
				// ahora $education_code tiene el código de educación que hay que transformar al código SIRECS
				$query3 = "SELECT `tc18`.`sirecs_code` AS codi_grup"
				. " FROM sirecs_taula_complementaria_18 AS tc18"
				. " WHERE `tc18`.`irb_code` = '" . $education_code . "'"
				;
				//echo $query3 . "\n";
				$result3 = mysql_query($query3);
				if (!$result3) {
					die('Invalid query: ' . mysql_error());
				}
				$row3 = mysql_fetch_assoc($result3);
				if ($row3['codi_grup']) {
					$reg .= sprintf("%1s", $row3['codi_grup']);
				} else {
					$reg .= sprintf("%1s", " ");
				}
				
				// data inici contracte
				$date = new DateTime($row['start_date']);
				$reg .= sprintf("%8s", $date->format('Ymd'));
				// data fi contracte
				if ($row['end_date']) {
					$date = new DateTime($row['end_date']);	
					$reg .= sprintf("%8s", $date->format('Ymd'));
				} else {
					$reg .= sprintf("%8s", "99999999");
				}
				// codi institucio contractant
				// Roberto 2013-06-26 Incremento el campo a 20 caracteres	
				$reg .= sprintf("%-20s", substr(trim($row['codi_institucio']), 0, 20));
				
				// validation
				/*
				if (strlen($reg) != 220)
				{	
					echo "ERROR. Length = " . strlen($reg) . " reg = " . $reg . "<br>";
				}
				*/
				
				// 2014-10-15 Roberto A partir de 2014 - ORCID
				$reg .= sprintf("%16s", "0000000000000000");
				
				// 2014-10-15 Roberto A partir de 2014 - Identificador d'empleat propi del centre
				$reg .= sprintf("%20s", "00000000000000000000");
				
				$reg .= "\n";
				echo $reg;
			}
		}
		
		$handle = fopen($filename, "r");
		$contents = fread($handle, filesize($filename));
		fclose($handle);
		
		echo $contents;
	} else {
		?>
		<html>
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		</head>
		<body>
		<p>You have not been authorized.</p>
		</body>
		</html>
		<?php
	}
} else {
	?>
	<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	</head>
	<body>
	<h2>Generate SIRECS' file 46</h2>
	<form name="form" method="post" action="getsirecs46.php">
	<table border="0">
	<tr>
	<td align="right">Execution Password:</td>
	<td><input name="execution_pass" type="password" size="50" maxlength="50"></td>
	</tr>
	<tr>
	<td colspan="2"><input type="submit" name="boton" value="Submit"></td>
	</tr>
	</table>
	</form>
	</body>
	</html>
	<?php 
}
?>
