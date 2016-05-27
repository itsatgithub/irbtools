<?php

// connect the db
$conf = json_decode(file_get_contents('generate_configuration.json'), TRUE);
$db = mysql_connect($conf["host"], $conf["user"], $conf["password"]) or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db($conf["database"]);
$path = "/media/psf/its/admin/Projects/2015 - Replacement ERP/pictures";

$query = "SELECT pp.photo AS 'image'"
. ", pe.personalcode AS 'personalcode'"
. " FROM `personalphoto` AS pp"
. " LEFT JOIN personal AS pe ON pe.photo = pp.personalPhotocode"
. " WHERE pe.deleted = ''"
. " AND (pe.state = '5' OR pe.state = '6')"
. "	ORDER BY pe.personalcode"
;

//echo $query;
//break;

$result = mysql_query($query);
if (!$result) {
	die('Invalid query: ' . mysql_error());
}

while ($row = mysql_fetch_assoc($result))
{
	//var_dump($row);
	//break;
	file_put_contents($path."/".$row['personalcode'], $row['image']);
	//break;
}
	
echo "Done!";

