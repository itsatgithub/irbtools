<?php
/**
 * Creación del fichero número 55 - Concesions competitivas
 * Roberto Bartolome 2012-03-27
 * v1.0
 * v1.1 2014-05-13 Roberto Añadida la verificación con password
 * 
 * Para crear el fichero a enviar a ICREA, desde un navegador acceder a la dirección
 * http://tools.irbbarcelona.org/getsirecs55.php
 * 
 * Para ejecutarlo en próximos años debemos cambiar la variable $YEAR con el año en concreto, y el contenido 
 * de los ficheros que se añaden automáticamente BM1055extra.txt y BM1055ICREAextra.txt
 * 
 */

// ====================== Actualizar este bloque ======================
$FILE = "BM1555.txt"; // nombre de archivo
$filename = "BM1555ICREAextra.txt"; // adding extra lines
$YEAR = "2015"; // año de referencia para generar el archivo
$START_DATE = $YEAR . "-01-01";
$END_DATE = $YEAR . "-12-31";
$CODI_INSTITUCIO = "5068"; // codigo de IRB Barcelona. Este dato no cambia nunca
$CODI_FINANCAMENT = "10";
$CODI_TIPUS_ENTITAT = "2";
// ====================== Actualizar este bloque ======================

if (isset($_POST['boton']))
{
	if (sha1($_POST['execution_pass']) === '52f8aebac6244bf9c810cd81ac1f6816f0da9b0f')
	{
		// save the file, do not open it
		header("Content-Disposition: attachment; filename=\"$FILE\"");
				
		// conecto la bd
		$conf = json_decode(file_get_contents('getsirecs55_configuration.json'), TRUE);
		$db = mysql_connect($conf["host"], $conf["user"], $conf["password"]) or die ('I cannot connect to the database because: ' . mysql_error());
		mysql_select_db($conf["database"]);
		
		// 2013-02-27 Condiciones de selección nuevas
		$MY_START_DATE = $YEAR . '-01-01'; // 1 de Enero del año seleccionado
		$MY_END_DATE = $YEAR . '-12-31'; // 31 de Diciembre
		
		$query = "SELECT p.*, at.description AS at_description"
		. ", gt.short_description AS gt_description, fe.short_description AS fe_description, gl.name AS group_leader"
		. ", tcgl.nif AS nif_group_leader"
		// project.grant_type_id - taula complementaria 34
		. ", tc34.sirecs_code AS codi_subvencio_recurs"
		. " FROM jos_sci_projects AS p"
		. " LEFT JOIN `jos_sci_group_leaders` AS gl ON gl.id = p.group_leader_id"
		. " LEFT JOIN `jos_sci_project_action_types` AS at ON at.id = p.action_type_id"
		. " LEFT JOIN `jos_sci_project_grant_types` AS gt ON gt.id = p.grant_type_id"
		. " LEFT JOIN `jos_sci_project_funding_entities` AS fe ON fe.id = p.funding_entity_id"
		// project.grant_type_id - taula complementaria 34
		. " LEFT JOIN sirecs_taula_complementaria_34 AS tc34 ON tc34.irb_code = p.grant_type_id"
		// p.group_leader_id - taula group leaders
		. " LEFT JOIN sirecs_taula_group_leaders AS tcgl ON tcgl.id = p.group_leader_id"
		// 2013-02-27 Cambio en las condiciones de asignación
		. " WHERE (p.awarding_date >= '" . $MY_START_DATE . "' AND p.awarding_date <= '" . $MY_END_DATE . "')"
		//. " WHERE (p.start_date >= '" . $START_DATE . "' AND p.start_date <= '" . $END_DATE . "')"
		// 2012-07-26 Cambio en las condiciones de fechas (las condiciones de abajo no valen)
		//. " WHERE (p.start_date < '" . $START_DATE . "' AND p.end_date >= '" . $START_DATE . "')"
		//. "   OR (p.start_date >= '" . $START_DATE . "' AND p.start_date <= '" . $END_DATE . "')"
		
		//. " AND p.owner_id = 1"
		. " ORDER BY p.id DESC"
		;
		//echo $query . "\n";
		$result = mysql_query($query);
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}
		
		while ($row = mysql_fetch_assoc($result))
		{
			// compruebo que no es un proyecto de la lista a eliminar
			$query2 = "SELECT * FROM sirecs_proyectos_a_eliminar AS s"
			. " WHERE s.id = " . $row['id']
			;
			// echo $query2 . "\n";
			$result2 = mysql_query($query2);
			if (mysql_num_rows($result2)) {
				//echo "eliminado " . $row['id'] . "\n";
				continue;
			}
			
			# Roberto 2014-07-04 Eliminamos proyectos con total_granted_budget, total_budget
			# y overheads_total_budget a 0
			if ($row['total_budget'] == 0 
				&& $row['total_granted_budget'] == 0 
				&& $row['overheads_total_budget'] == 0) {
				continue;
			}
			
			$reg = "";
			// codi entitat
			$reg .= sprintf("%010s", $CODI_INSTITUCIO);
			// codi ens vinculat
			$reg .= sprintf("%010s", "0");
			// any
			$reg .= sprintf("%4s", $YEAR);
			// data inici concesio
			$start_date = new DateTime($row['start_date']); 
			$reg .= sprintf("%8s", $start_date->format('Ymd')); 
			// data fi concesio
			$end_date = new DateTime($row['end_date']); 
			$reg .= sprintf("%8s", $end_date->format('Ymd'));	
			// codi subvencio recurs	
			$reg .= sprintf("%010s", $row['codi_subvencio_recurs']);
			// codi expedient oficial
			// 2012-05-25 Roberto Si no tiene referencia, se incluye el id del proyecto
			// echo "reference = ->" . $row['reference'] . "<-" . "\n";
			if (empty($row['reference'])) {
				$reg .= sprintf("%-50s", $row['id']);
			} else {
				$reg .= sprintf("%-50s", $row['reference']);
			}
			// codi expedient intern
			$reg .= sprintf("%-50s", $row['id']);	
			// codi unitat solicitant
			$reg .= sprintf("%10s", " ");
			// NIF investigador principal
			$reg .= sprintf("%-10s", $row['nif_group_leader']);
			// NIF ampliado investigador principal
			$reg .= sprintf("%-20s", $row['nif_group_leader']);
			// NIF solicitant principal
			$reg .= sprintf("%-10s", "0");
			// NIF ampliado solicitant principal
			$reg .= sprintf("%-20s", "0");
			
			// import total concedit
			// 2013-04-15 Roberto Ponemos total_granted_budget
			$num_aux = intval($row['total_granted_budget'] * 100);
			$reg .= sprintf("%011d", $num_aux);
			
			// import concedit centre
			$num_aux = intval($row['total_budget'] * 100);
			$reg .= sprintf("%011d", $num_aux);
			
			// import concedit ens vinculat
			$reg .= sprintf("%011d", "0");
			
			// import concedit altres entitats
			$import_aux = ($row['total_granted_budget'] - $row['total_budget']) * 100;
			$reg .= sprintf("%011d", $import_aux);	
			
			// import overhead teoric
			$num_aux = intval($row['overheads_total_budget'] * 100);
			$reg .= sprintf("%011d", $num_aux);	
			
			// IP coordina/lidera
			// 2013-02-27 Modificación de las condiciones
			if ($row['action_type_id'] == '1') { // individual
				$reg .= sprintf("%1s", 'I');
			} elseif ($row['role_id'] == '1') { // coordinator
				$reg .= sprintf("%1s", 'C');
			} elseif ($row['role_id'] == '2') { // partner
				$reg .= sprintf("%1s", 'P');
			}
			
			// data signatura
			$reg .= sprintf("%8s", "        ");
			// es tracta  d'una activitat...
			$reg .= sprintf("%1s", "N");	
			// tipus de financament
			$reg .= sprintf("%2s", $CODI_FINANCAMENT);	
			// codi tipus entitat
			$reg .= sprintf("%1s", $CODI_TIPUS_ENTITAT);
			// codi assaig clinic
			$reg .= sprintf("%50s", " ");	
			// nombre de malats
			$reg .= sprintf("%6s", " ");	
			
			$reg .= "\n";
			echo $reg;
		}
		
		// 2013-03-20 Roberto En los años sucesivos no se entregan estos ficheros
		// adding extra lines projects
		//$filename = "BM1055extra.txt";
		//$handle = fopen($filename, "r");
		//$contents = fread($handle, filesize($filename));
		//fclose($handle);
		
		//echo $contents;
		
		// adding ICREA extra line
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
	<h2>Generate SIRECS' file 55</h2>
	<form name="form" method="post" action="getsirecs55.php">
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
