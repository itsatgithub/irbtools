<?php

// connect the db
$conf = json_decode(file_get_contents('generate_configuration.json'), TRUE);
$db = mysql_connect($conf["host"], $conf["user"], $conf["password"]) or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db($conf["database"]);

$query = "SELECT pe.personalcode AS 'Code'"
. ", '' AS 'LineId'"
. ", 'SEISALARIO' AS 'Object'"
. ", 'NULL' AS 'LogInst'"
. ", com.start_date AS 'StartDate'"
. ", com.end_date AS 'EndDate'"
. ", '00001' AS 'Type of compensation'"
. ", com.amount AS 'Amount'"
. ", CASE WHEN com.current = '1' THEN 'Y' WHEN com.current = '0' THEN 'N' END AS 'Current'"
. " FROM `compensation` AS com"
. " LEFT JOIN personal AS pe ON pe.personalcode = com.compensation_personal"
. " WHERE pe.deleted = ''"
. " AND com.deleted = ''"
. " AND (pe.state = '5' OR pe.state = '6')"
. "	ORDER BY pe.personalcode"
;

echo $query;
break;

$result = mysql_query($query);
if (!$result) {
	die('Invalid query: ' . mysql_error());
}

while ($row = mysql_fetch_assoc($result))
{
	 var_dump($row);
}
	
echo "Done!";

