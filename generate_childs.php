<?php

// connect the db
$conf = json_decode(file_get_contents('generate_configuration.json'), TRUE);
$db = mysql_connect($conf["host"], $conf["user"], $conf["password"]) or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db($conf["database"]);

$query = "SELECT ch.child_personal AS 'personalcode'"
. ", ch.birth_date AS 'birth_date'"
. ", ch.observations AS 'observations'"
. " FROM `child` AS ch"
. " LEFT JOIN personal AS pe ON pe.personalcode = ch.child_personal"
. " WHERE pe.deleted = '' AND ch.deleted = ''"
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
	//break;
}
	
echo "Done!";

