<?php
/**
 * Creación del fichero número 56 - Concesions no competitivas
 * Roberto Bartolome 2012-06-11
 * v1.0
 * v1.1 2014-05-13 Roberto Añadida la verificación con password
 * 
 * 2013-03-25 Roberto. Actualizado para generar el archivo.
 * 
 * Para crear el fichero a enviar a ICREA, desde un navegador acceder a la dirección
 * http://tools.irbbarcelona.org/getsirecs56.php
 * 
 * Para ejecutarlo en próximos años debemos cambiar la variable $YEAR con el año en concreto, y el contenido 
 * de la tabla sirecs_taula_56 desde donde se extrae la información
 * 
 */

// ====================== Actualizar este bloque ======================
$FILE = "BM1556.txt"; // nombre de archivo
$YEAR = "2015"; // año de referencia para generar el archivo
$START_DATE = $YEAR . "-01-01";
$END_DATE = $YEAR . "-12-31";
$CODI_INSTITUCIO = "5068"; // codigo de IRB Barcelona. Este dato no cambia nunca
// ====================== Actualizar este bloque ======================

if (isset($_POST['boton']))
{
	if (sha1($_POST['execution_pass']) === '52f8aebac6244bf9c810cd81ac1f6816f0da9b0f')
	{
		// save the file, do not open it
		header("Content-Disposition: attachment; filename=\"$FILE\"");
		
		// conecto la bd
		$conf = json_decode(file_get_contents('getsirecs56_configuration.json'), TRUE);
		$db = mysql_connect($conf["host"], $conf["user"], $conf["password"]) or die ('I cannot connect to the database because: ' . mysql_error());
		mysql_select_db($conf["database"]);
				
		// 2013-03-25 Años
		$MY_START_DATE = $YEAR . '-01-01'; // 1 de Enero del año seleccionado
		$MY_END_DATE = $YEAR . '-12-31'; // 31 de Diciembre
		
		$query = "SELECT p.*"
		. ", gl.name AS group_leader"
		. ", tcgl.nif AS nif_group_leader"
		// project.funding_sector_id - taula complementaria 40
		. ", tc40.sirecs_code AS codi_sector_organisme_financador"
		. " FROM jos_sci_research_contracts AS p"
		. " LEFT JOIN `jos_sci_group_leaders` AS gl ON gl.id = p.group_leader_id"
		// p.group_leader_id - taula group leaders
		. " LEFT JOIN sirecs_taula_group_leaders AS tcgl ON tcgl.id = p.group_leader_id"
		// project.funding_sector_id - taula complementaria 40
		. " LEFT JOIN sirecs_taula_complementaria_40 AS tc40 ON tc40.irb_code = p.funding_sector_id"
		. " WHERE (p.start_date >= '" . $MY_START_DATE . "' AND p.start_date <= '" . $MY_END_DATE . "')"
		. " ORDER BY p.id DESC"
		;
		//echo $query . "\n";
		$result = mysql_query($query);
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}
		
		while ($row = mysql_fetch_assoc($result))
		{
			$reg = "";
			// codi entitat
			$reg .= sprintf("%010s", $CODI_INSTITUCIO);
			// codi ens vinculat
			$reg .= sprintf("%010s", "0");
			// any
			$reg .= sprintf("%4s", $YEAR);
			// data inici
			$start_date = new DateTime($row['start_date']);
			$reg .= sprintf("%8s", $start_date->format('Ymd'));
			// data fi concesio
			$end_date = new DateTime($row['end_date']);
			$reg .= sprintf("%8s", $end_date->format('Ymd'));
			// codi tipus d'acord
			// 2013-04-18 Roberto Ver email de Clara Caminal 2013-04-17 4:38 PM
			$pos = strpos($row['acronym'], "SUBVENCIOGTAT");
			if ($pos === false) { // Acronym no contiene la palabra buscada
				$reg .= sprintf("%1s", "2");
			} else {
				$reg .= sprintf("%1s", "4");
			}
			
			// codi expedient oficial
			if (empty($row['acronym'])) {
				$reg .= sprintf("%-50s", $row['id']);
			} else {
				$reg .= sprintf("%-50s", $row['acronym']);
			}
			
			// codi expedient intern
			$reg .= sprintf("%-50s", $row['id']);
			// codi organisme financador
			$reg .= sprintf("%-20s", $row['company']);
			// codi sector organisme financador
			// 2013-04-18 Roberto Ver email de Clara Caminal 2013-04-17 4:38 PM
			if (empty($row['company'])) {
				$reg .= sprintf("%4s", $row['codi_sector_organisme_financador']);
			} else {
				$reg .= sprintf("%4s", " ");
			}
			
			// codi destinacio
			$reg .= sprintf("%4s", "C012");
			// codi unitat
			$reg .= sprintf("%10s", " ");
			// NIF investigador principal
			$reg .= sprintf("%-10s", $row['nif_group_leader']);
			// NIF ampliado investigador principal
			$reg .= sprintf("%-20s", $row['nif_group_leader']);
			// NIF solicitant principal
			$reg .= sprintf("%-10s", $row['nif_group_leader']);
			// NIF ampliado solicitant principal
			$reg .= sprintf("%-20s", $row['nif_group_leader']);
			// import total
			$num_aux = intval($row['budget'] * 100);
			$reg .= sprintf("%011d", $num_aux);
			// import contracte
			$num_aux = intval($row['budget'] * 100);
			$reg .= sprintf("%011d", $num_aux);
			// import ens vinculat
			$reg .= sprintf("%011d", "0");
			// import altres
			$reg .= sprintf("%011d", "0");
			// import overhead teoric
			$num_aux = intval($row['overhead'] * 100);
			$reg .= sprintf("%011d", $num_aux);
			// coordina / lidera
			$reg .= sprintf("%1s", "C");
			// data signatura
			$reg .= sprintf("%8s", " ");
			// coordinada?
			$reg .= sprintf("%1s", "N");
			// tipus de financament
			if ($row['funding_sector_id'] == '8') {
				$reg .= sprintf("%2s", "12");
			} else {
				$reg .= sprintf("%2s", "10");
			}
			// tipus entitat
			$reg .= sprintf("%1s", "2");
			
			// codi assaig clinic
			$reg .= sprintf("%50s", " ");
			// nombre de malalts
			$reg .= sprintf("%6s", " ");
			
			$reg .= "\n";
			echo $reg;
		}
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
	<h2>Generate SIRECS' file 56</h2>
	<form name="form" method="post" action="getsirecs56.php">
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
