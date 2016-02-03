<?php

// connect the db
$conf = json_decode(file_get_contents('generate_configuration.json'), TRUE);
$db = mysql_connect($conf["host"], $conf["user"], $conf["password"]) or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db($conf["database"]);

$query = "SELECT edu.education_personal AS 'Employee N'"
. ", '' AS 'LineNum'"
. ", edu.education_personal AS 'Employee N'"
. ", edu.start_date AS 'Start Date'"
. ", edu.graduation_date AS 'Graduation Date'"
. ", edu.type AS 'Type'"
. ", edu.center AS 'Center'"
. ", edu.title AS 'Title'"
. ", edu.speciality AS 'Speciality'"
. ", edu.education_country AS 'Education Country'"
. " FROM `education` AS edu"
. " LEFT JOIN personal AS pe ON pe.personalcode = edu.education_personal"
. " WHERE pe.deleted = ''"
. " AND edu.deleted = ''"
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

