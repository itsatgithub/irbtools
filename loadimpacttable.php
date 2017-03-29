<?php
/* Roberto 20/02/2013
 * 
 * Usage: http://localhost/irbtools/loadimpacttable.php
 * 
 * Como usar el archivo:
 * 
 * El user y pass de conexión a la BD (de test y operacional) se encuentran en un archivo de texto que no se gestiona en Google Code
 * Los datos del fichero están en formato user1,pass1,user2,pass2,user3,pass3...
 * Conectar el script a la BD de test o de operación descomentando las lineas adecuadas.
 * Los datos a cargar en la bd se añaden como filas en la tabla 'jos_sci_upload_impact_factor' en dos columnas de la
 * forma 'publicación', 'impact_factor'.
 * El script dispone de bloques de código: 'Cargando journals', 'Actualizando el order', 
 * 'Listando datos', 'Cargando impact factors'. Estos bloques puede ejecutarse uno a continuación de otro o de forma separada.
 * El último bloque debe ejecutarse para todos los años que se quieran insertar.
 * Al finalizar la ejecución del último bloque los datos estarán cargados
 * 
 * Nota 21/02/2013: se cargan los años 2011, 2012, 2013 con los mismos valores
 *
 */ 

// user y pass
$conn_params = file_get_contents('./db_connection_parameters.txt');
$comm_params_array = explode(",", $conn_params);

// debug db
//$db = mysql_connect("irbsvr3.irb.pcb.ub.es", $comm_params_array[0], $comm_params_array[1]) or die ('I cannot connect to the database because: ' . mysql_error());
//mysql_select_db("dev_joomlaatirb");
	
// operational db
/*
$db = mysqli_connect("irbsvr83.irb.pcb.ub.es", $comm_params_array[2], $comm_params_array[3], "sciprod");
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}
*/

define("DB_SERVER", "complete");
define("DB_USERNAME", "complete");
define("DB_PASSWORD", "complete");
define("DB_DATABASE", "complete");
$db = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}


?>
<html>
<head>
<title>file</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<?php
/*
 * Cargando journals
 */
/*
$query = "SELECT *"
. " FROM jos_sci_upload_impact_factor"
;
$result = mysqli_query($db, $query);
while ($row = mysqli_fetch_assoc($result))
{
	$journal = ucwords(strtolower(trim($row['journal'])));
	
	// miro si existe en journals
	$query2 = "SELECT * from jos_sci_journals WHERE `description` = '" . $journal . "'";
	$result2 = mysqli_query($db, $query2);
	
	if (!mysqli_num_rows($result2))
	{		
		// insert new journal
		$query3 = "INSERT INTO jos_sci_journals(`description`, `short_description`)"
		. " VALUES ('" . $journal . "', '" . $journal . "')"
		;
		//echo $query3 . '<br>';
				
		$result3 = mysqli_query($db, $query3);
		if (!$result3) {
			die('Invalid query: ' . mysqli_error($db));
		}
		
		echo 'He insertado el journal ' . $journal . ' en la tabla maestra.<br>';
		
	}
}
echo "End";
*/

/*
 * Actualizando el order
 */
/*
$query = "SELECT *"
. " FROM jos_sci_journals"
. " ORDER BY short_description"
;
$result = mysqli_query($db, $query);
$i = 1; // order
while ($row = mysqli_fetch_assoc($result))
{
	$query2 = "UPDATE `jos_sci_journals`"
	. " SET `order` = " . $i
	. " WHERE `short_description` = '" . $row['short_description'] . "'"
	;
	//echo $query2 . '<br>';
	$result2 = mysqli_query($db, $query2);
	if (!$result2) {
	    die('Invalid query: ' . mysqli_error($db));
	}
	
	$i++;
}
echo "End";
*/



/*
 * Listando datos
 */
/*
$query = "SELECT *"
. " FROM jos_sci_journals"
;
$result = mysqli_query($db, $query);
while ($row = mysqli_fetch_assoc($result))
{
	echo "description ->" . $row['description'] . "<-, short_description ->" . $row['short_description'] . "<-<br>";
}
echo "End";
*/



/*
 * Cargando impact factors
 */
/*
$my_year = '2018';

$query = "SELECT *"
. " FROM jos_sci_upload_impact_factor"
;
$result = mysqli_query($db, $query);
while ($row = mysqli_fetch_assoc($result))
{	
	$journal = ucwords(strtolower(trim($row['journal'])));
	$journal = str_replace(".", "", $journal);
	
	// miro si existe en journals
	$query2 = "SELECT * from jos_sci_journals WHERE `description` = '" . $journal . "'";
	//echo $query2 . '<br>';
	$result2 = mysqli_query($db, $query2);
	$row2 = mysqli_fetch_object($result2);

	if (mysqli_num_rows($result2))
	{
	

		$query9 = "UPDATE `jos_sci_journals` SET `tobedeleted` = '0'"
		. " WHERE `id` = '". $row2->id . "'"
		;
		//echo $query9 . '<br>';
		//break;
		$result9 = mysqli_query($db, $query9);
		if (!$result9) {
			echo "A revisar: " . $query9 . "<br>";
		}


		$query4 = "SELECT * FROM jos_sci_journal_impact_factors WHERE `journal_id` = ". $row2->id . " AND `year` = '".$my_year."'";
		$result4 = mysqli_query($db, $query4);
		if (!mysqli_num_rows($result4))
		{
			// inserto nuevo impact factor
			$query3 = "INSERT INTO jos_sci_journal_impact_factors(`journal_id`, `year`, `impact_factor`, `quartile`, `jcrd`, `sjrif`, `sjrq`, `sjrd`, `tobedeleted`)"
			. " VALUES (" . $row2->id . ", '".$my_year."', " 
			. ($row['jcrif'] == ''? 0:$row['jcrif']) .", '". $row['jcrq'] ."', '". $row['jcrd'] ."', "
			. ($row['sjrif'] == ''? 0:$row['sjrif']) .", '". $row['sjrq'] ."', '". $row['sjrd'] ."', '0')"
			;
			//echo $query3 . '<br>';
			//break;
			$result3 = mysqli_query($db, $query3);
			if (!$result3) {
				echo "A revisar: " . $query3 . "<br>";
			}
		} else {
			
			// update nuevo impact factor
			$query3 = "UPDATE `jos_sci_journal_impact_factors` SET"
			." `impact_factor` = '" . ($row['jcrif'] == ''? 0:$row['jcrif']) . "'"
			.", `quartile` = '" . $row['jcrq'] . "'"
			.", `jcrd` = '" . $row['jcrd'] . "'"
			.", `sjrif` = '" . ($row['sjrif'] == ''? 0:$row['sjrif']) . "'"
			.", `sjrq` = '" . $row['sjrq'] . "'"
			.", `sjrd` = '" . $row['sjrd'] . "'"
			.", `tobedeleted` = '0'"
			. " WHERE `journal_id` = '". $row2->id . "' AND `year` = '".$my_year."'"
			;
			//echo $query3 . '<br>';
			//break;
			$result3 = mysqli_query($db, $query3);
			if (!$result3) {
				echo "A revisar: " . $query3 . "<br>";
			}
		}
	} else {
		echo "A revisar: " . $journal . " no existe.<br>";
	}
}
echo "End";
*/



?>
</body>
</html>