<?php

// connect the db
$conf = json_decode(file_get_contents('generate_configuration.json'), TRUE);
$db = mysql_connect($conf["host"], $conf["user"], $conf["password"]) or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db($conf["database"]);

// select the professional data
$query = "SELECT pro.professional_personal AS 'Employee N'"
. ", pro.research_group AS 'Research Group 1'"
. ", pro.professional_unit AS 'Unit 1'"
. ", re1.col5 AS 'Responsible RG'"
. ", un1.col6 AS 'Responsible Unit'"
. ", CASE WHEN pro.research_group != '' THEN re1.col5 WHEN pro.professional_unit != '' THEN un1.col6 END AS 'Responsible'"
. " FROM `professional` AS pro"
. " INNER JOIN personal AS pe ON pe.personalcode = pro.professional_personal"
. " LEFT JOIN hem3_research_group_final_HR AS re1 ON re1.col1 = pro.research_group"
. " LEFT JOIN hem3_unit_final_HR AS un1 ON un1.col1 = pro.professional_unit"
. " WHERE pro.deleted = ''"
. " AND pro.current != ''"
. " AND (pe.state = '5' OR pe.state = '6')"
. "	ORDER BY pro.professional_personal"
;

/*
$query = "SELECT pe.personalcode AS 'Employee N'"
. ", pro.research_group AS 'Research Group 1'"
. ", pro.professional_unit AS 'Unit 1'"
. ", re1.col5 AS 'Responsible RG'"
. ", un1.col6 AS 'Responsible Unit'"
. " FROM `personal` AS pe"
. " LEFT JOIN professional AS pro ON pro.professional_personal = pe.personalcode"
. " LEFT JOIN hem3_research_group_final_HR AS re1 ON re1.col1 = pro.research_group"
. " LEFT JOIN hem3_unit_final_HR AS un1 ON un1.col1 = pro.professional_unit"
. " WHERE pe.deleted = ''"
. " AND (pe.state = '5' OR pe.state = '6')"
. "	ORDER BY pe.personalcode"
;
*/

$result = mysql_query($query);
if (!$result) {
	die('Invalid query: ' . mysql_error());
}

while ($row = mysql_fetch_assoc($result))
{
	 var_dump($row);	 
}

echo "Done!";
