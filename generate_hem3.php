<?php

// connect the db
$conf = json_decode(file_get_contents('generate_hem3_configuration.json'), TRUE);
$db = mysql_connect($conf["host"], $conf["user"], $conf["password"]) or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db($conf["database"]);

// select the professional data
$query = "SELECT pro.professional_personal AS 'Employee N'"
. ", '' AS 'LineNum'"
. ", pro.professional_personal AS 'Employee N'"
. ", DATE_FORMAT(pro.start_date, '%Y%m%d') AS 'Start Date'"
. ", DATE_FORMAT(pro.end_date, '%Y%m%d') AS 'End Date'"
. ", '' AS 'Description'"

. ", pro.`type_of_contract` AS 'Type of Contract'"
. ", ty.col4 AS 'Code Type of Contract'"

. ", pro.`position` AS 'Job Position'"
. ", po.col3 AS 'Code Job Position'"

. ", pro.payroll_institution AS 'Payroll Institution 1'"
. ", pro.payroll_institution_2 AS 'Payroll Institution 2'"

. ", pro.research_group AS 'Research Group 1'"
. ", pro.research_group_2 AS 'Research Group 2'"
. ", pro.research_group_3 AS 'Research Group 3'"
. ", pro.research_group_4 AS 'Research Group 4'"
. ", re1.col3 AS 'Code Research Group 1'"
. ", re2.col3 AS 'Code Research Group 2'"
. ", re3.col3 AS 'Code Research Group 3'"
. ", re4.col3 AS 'Code Research Group 4'"

. ", pro.professional_unit AS 'Unit 1'"
. ", pro.professional_unit_2 AS 'Unit 2'"
. ", pro.professional_unit_3 AS 'Unit 3'"
. ", pro.professional_unit_4 AS 'Unit 4'"
. ", un1.col3 AS 'Code Unit 1'"
. ", un2.col3 AS 'Code Unit 2'"
. ", un3.col3 AS 'Code Unit 3'"
. ", un4.col3 AS 'Code Unit 4'"

//. ", CAST(pro.current AS UNSIGNED) AS 'Current'"
. ", CASE WHEN pro.current = '1' THEN 'Y' WHEN pro.current = '0' THEN 'N' END AS 'Current'"
. " FROM `professional` AS pro"
. " INNER JOIN personal AS pe ON pe.personalcode = pro.professional_personal"
. " LEFT JOIN hem3_type_of_contract_final_HR AS ty ON ty.col1 = pro.type_of_contract"
. " LEFT JOIN hem3_position_final_HR AS po ON po.col1 = pro.position"
. " LEFT JOIN hem3_research_group_final_HR AS re1 ON re1.col1 = pro.research_group"
. " LEFT JOIN hem3_research_group_final_HR AS re2 ON re2.col1 = pro.research_group_2"
. " LEFT JOIN hem3_research_group_final_HR AS re3 ON re3.col1 = pro.research_group_3"
. " LEFT JOIN hem3_research_group_final_HR AS re4 ON re4.col1 = pro.research_group_4"
. " LEFT JOIN hem3_unit_final_HR AS un1 ON un1.col1 = pro.professional_unit"
. " LEFT JOIN hem3_unit_final_HR AS un2 ON un2.col1 = pro.professional_unit_2"
. " LEFT JOIN hem3_unit_final_HR AS un3 ON un3.col1 = pro.professional_unit_3"
. " LEFT JOIN hem3_unit_final_HR AS un4 ON un4.col1 = pro.professional_unit_4"

. " WHERE pro.deleted = ''"
. " AND (pe.state = '5' OR pe.state = '6')"
. "	ORDER BY pro.professional_personal"
;

$result = mysql_query($query);
if (!$result) {
	die('Invalid query: ' . mysql_error());
}

while ($row = mysql_fetch_assoc($result))
{
	 //var_dump($row);
	
	// if there is a Type of Contract, insert one line
	if (!empty($row['Code Type of Contract']))
	{
		$insert = "INSERT INTO `hem3_step2` ("
		. "`employee_n`"
		. ", `linenum`"
		. ", `employee_n2`"
		. ", `start_date`"
		. ", `end_date`"
		. ", `description`"
		. ", `type_of_contract`"
		. ", `current`"
		. " ) VALUES ("
		. "'" . $row['Employee N'] . "'"
		. ", ''"
		. ", '" . $row['Employee N'] . "'"
		. ", '" . $row['Start Date'] . "'"
		. ", '" . $row['End Date'] . "'"
		. ", 'Contract'"
		. ", '" . $row['Code Type of Contract'] . "'"
		. ", '" . $row['Current'] . "'"
		. ")"
		;		
		$result2 = mysql_query($insert);
		if (!$result2) {
			die('Invalid query: ' . mysql_error());
		}
	}
		
	// if there is a Job Position, insert one line
	if (!empty($row['Code Job Position']))
	{
		$insert = "INSERT INTO `hem3_step2` ("
		. "`employee_n`"
		. ", `linenum`"
		. ", `employee_n2`"
		. ", `start_date`"
		. ", `end_date`"
		. ", `description`"
		. ", `job_position`"
		. ", `current`"
		. " ) VALUES ("
		. "'" . $row['Employee N'] . "'"
		. ", ''"
		. ", '" . $row['Employee N'] . "'"
		. ", '" . $row['Start Date'] . "'"
		. ", '" . $row['End Date'] . "'"
		. ", 'JobPosition'"
		. ", '" . $row['Code Job Position'] . "'"
		. ", '" . $row['Current'] . "'"
		. ")"
		;		
		$result2 = mysql_query($insert);
		if (!$result2) {
			die('Invalid query: ' . mysql_error());
		}
	}	
	
	// if there is a Payroll Institution, insert one line
	if (!empty($row['Payroll Institution 1']))
	{
		$insert = "INSERT INTO `hem3_step2` ("
		. "`employee_n`"
		. ", `linenum`"
		. ", `employee_n2`"
		. ", `start_date`"
		. ", `end_date`"
		. ", `description`"
		. ", `payroll_institution`"
		. ", `current`"
		. " ) VALUES ("
		. "'" . $row['Employee N'] . "'"
		. ", ''"
		. ", '" . $row['Employee N'] . "'"
		. ", '" . $row['Start Date'] . "'"
		. ", '" . $row['End Date'] . "'"
		. ", 'Payroll'"
		. ", '" . $row['Payroll Institution 1'] . "'"
		. ", '" . $row['Current'] . "'"
		. ")"
		;
		$result2 = mysql_query($insert);
		if (!$result2) {
			die('Invalid query: ' . mysql_error());
		}
	}
	if (!empty($row['Payroll Institution 2']))
	{
		$insert = "INSERT INTO `hem3_step2` ("
		. "`employee_n`"
		. ", `linenum`"
		. ", `employee_n2`"
		. ", `start_date`"
		. ", `end_date`"
		. ", `description`"
		. ", `payroll_institution`"
		. ", `current`"
		. " ) VALUES ("
		. "'" . $row['Employee N'] . "'"
		. ", ''"
		. ", '" . $row['Employee N'] . "'"
		. ", '" . $row['Start Date'] . "'"
		. ", '" . $row['End Date'] . "'"
		. ", 'Payroll'"
		. ", '" . $row['Payroll Institution 2'] . "'"
		. ", '" . $row['Current'] . "'"
		. ")"
		;
		$result2 = mysql_query($insert);
		if (!$result2) {
			die('Invalid query: ' . mysql_error());
		}
	}
	
	// if there is a Research Group, insert one line
	if (!empty($row['Code Research Group 1']))
	{
		$insert = "INSERT INTO `hem3_step2` ("
		. "`employee_n`"
		. ", `linenum`"
		. ", `employee_n2`"
		. ", `start_date`"
		. ", `end_date`"
		. ", `description`"
		. ", `research_group`"
		. ", `current`"
		. " ) VALUES ("
		. "'" . $row['Employee N'] . "'"
		. ", ''"
		. ", '" . $row['Employee N'] . "'"
		. ", '" . $row['Start Date'] . "'"
		. ", '" . $row['End Date'] . "'"
		. ", 'ResearchGroup'"
		. ", '" . $row['Code Research Group 1'] . "'"
		. ", '" . $row['Current'] . "'"
		. ")"
		;
		$result2 = mysql_query($insert);
		if (!$result2) {
			die('Invalid query: ' . mysql_error());
		}
	}
	if (!empty($row['Code Research Group 2']))
	{
		$insert = "INSERT INTO `hem3_step2` ("
		. "`employee_n`"
		. ", `linenum`"
		. ", `employee_n2`"
		. ", `start_date`"
		. ", `end_date`"
		. ", `description`"
		. ", `research_group`"
		. ", `current`"
		. " ) VALUES ("
		. "'" . $row['Employee N'] . "'"
		. ", ''"
		. ", '" . $row['Employee N'] . "'"
		. ", '" . $row['Start Date'] . "'"
		. ", '" . $row['End Date'] . "'"
		. ", 'ResearchGroup'"
		. ", '" . $row['Code Research Group 2'] . "'"
		. ", '" . $row['Current'] . "'"
		. ")"
		;
		$result2 = mysql_query($insert);
		if (!$result2) {
			die('Invalid query: ' . mysql_error());
		}
	}
	if (!empty($row['Code Research Group 3']))
	{
		$insert = "INSERT INTO `hem3_step2` ("
		. "`employee_n`"
		. ", `linenum`"
		. ", `employee_n2`"
		. ", `start_date`"
		. ", `end_date`"
		. ", `description`"
		. ", `research_group`"
		. ", `current`"
		. " ) VALUES ("
		. "'" . $row['Employee N'] . "'"
		. ", ''"
		. ", '" . $row['Employee N'] . "'"
		. ", '" . $row['Start Date'] . "'"
		. ", '" . $row['End Date'] . "'"
		. ", 'ResearchGroup'"
		. ", '" . $row['Code Research Group 3'] . "'"
		. ", '" . $row['Current'] . "'"
		. ")"
		;
		$result2 = mysql_query($insert);
		if (!$result2) {
			die('Invalid query: ' . mysql_error());
		}
	}
	if (!empty($row['Code Research Group 4']))
	{
		$insert = "INSERT INTO `hem3_step2` ("
		. "`employee_n`"
		. ", `linenum`"
		. ", `employee_n2`"
		. ", `start_date`"
		. ", `end_date`"
		. ", `description`"
		. ", `research_group`"
		. ", `current`"
		. " ) VALUES ("
		. "'" . $row['Employee N'] . "'"
		. ", ''"
		. ", '" . $row['Employee N'] . "'"
		. ", '" . $row['Start Date'] . "'"
		. ", '" . $row['End Date'] . "'"
		. ", 'ResearchGroup'"
		. ", '" . $row['Code Research Group 4'] . "'"
		. ", '" . $row['Current'] . "'"
		. ")"
		;
		$result2 = mysql_query($insert);
		if (!$result2) {
			die('Invalid query: ' . mysql_error());
		}
	}
	
	// if there is a Unit, insert one line
	if (!empty($row['Code Unit 1']))
	{
		$insert = "INSERT INTO `hem3_step2` ("
		. "`employee_n`"
		. ", `linenum`"
		. ", `employee_n2`"
		. ", `start_date`"
		. ", `end_date`"
		. ", `description`"
		. ", `unit`"
		. ", `current`"
		. " ) VALUES ("
		. "'" . $row['Employee N'] . "'"
		. ", ''"
		. ", '" . $row['Employee N'] . "'"
		. ", '" . $row['Start Date'] . "'"
		. ", '" . $row['End Date'] . "'"
		. ", 'Unit'"
		. ", '" . $row['Code Unit 1'] . "'"
		. ", '" . $row['Current'] . "'"
		. ")"
		;
		$result2 = mysql_query($insert);
		if (!$result2) {
			die('Invalid query: ' . mysql_error());
		}
	}
	if (!empty($row['Code Unit 2']))
	{
		$insert = "INSERT INTO `hem3_step2` ("
		. "`employee_n`"
		. ", `linenum`"
		. ", `employee_n2`"
		. ", `start_date`"
		. ", `end_date`"
		. ", `description`"
		. ", `unit`"
		. ", `current`"
		. " ) VALUES ("
		. "'" . $row['Employee N'] . "'"
		. ", ''"
		. ", '" . $row['Employee N'] . "'"
		. ", '" . $row['Start Date'] . "'"
		. ", '" . $row['End Date'] . "'"
		. ", 'Unit'"
		. ", '" . $row['Code Unit 2'] . "'"
		. ", '" . $row['Current'] . "'"
		. ")"
		;
		$result2 = mysql_query($insert);
		if (!$result2) {
			die('Invalid query: ' . mysql_error());
		}
	}
	if (!empty($row['Code Unit 3']))
	{
		$insert = "INSERT INTO `hem3_step2` ("
		. "`employee_n`"
		. ", `linenum`"
		. ", `employee_n2`"
		. ", `start_date`"
		. ", `end_date`"
		. ", `description`"
		. ", `unit`"
		. ", `current`"
		. " ) VALUES ("
		. "'" . $row['Employee N'] . "'"
		. ", ''"
		. ", '" . $row['Employee N'] . "'"
		. ", '" . $row['Start Date'] . "'"
		. ", '" . $row['End Date'] . "'"
		. ", 'Unit'"
		. ", '" . $row['Code Unit 3'] . "'"
		. ", '" . $row['Current'] . "'"
		. ")"
		;
		$result2 = mysql_query($insert);
		if (!$result2) {
			die('Invalid query: ' . mysql_error());
		}
	}
	if (!empty($row['Code Unit 4']))
	{
		$insert = "INSERT INTO `hem3_step2` ("
		. "`employee_n`"
		. ", `linenum`"
		. ", `employee_n2`"
		. ", `start_date`"
		. ", `end_date`"
		. ", `description`"
		. ", `unit`"
		. ", `current`"
		. " ) VALUES ("
		. "'" . $row['Employee N'] . "'"
		. ", ''"
		. ", '" . $row['Employee N'] . "'"
		. ", '" . $row['Start Date'] . "'"
		. ", '" . $row['End Date'] . "'"
		. ", 'Unit'"
		. ", '" . $row['Code Unit 4'] . "'"
		. ", '" . $row['Current'] . "'"
		. ")"
		;
		$result2 = mysql_query($insert);
		if (!$result2) {
			die('Invalid query: ' . mysql_error());
		}
	}
}

echo "Done!";

