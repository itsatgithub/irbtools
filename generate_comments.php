<?php

// connect the db
$conf = json_decode(file_get_contents('generate_comments_configuration.json'), TRUE);
$db = mysql_connect($conf["host"], $conf["user"], $conf["password"]) or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db($conf["database"]);

// select the professional data
$query = "SELECT pc.personalcode, pc.text"
. " FROM `personal_comment` AS pc"
. " LEFT JOIN personal AS pe ON pe.personalcode = pc.personalcode"
. " WHERE pe.deleted = ''"
. " AND (pe.state = '5' OR pe.state = '6')"
. "	ORDER BY pc.personalcode"
;

$result = mysql_query($query);
if (!$result) {
	die('Invalid query: ' . mysql_error());
}

while ($row = mysql_fetch_assoc($result))
{
	 var_dump($row);
}
	
echo "Done!";

