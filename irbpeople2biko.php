<?php
require_once 'Mail.php';
require_once('Mail/mime.php');

/*
 * Falta tratar las excepciones de Cristina
 */

/*
if (isset($_POST['boton']))
{
	echo sha1($_POST['execution_pass']);
	exit;
}
*/

/**
 * Copiado de http://es2.php.net/manual/en/function.str-getcsv.php
 * 
 * @param unknown_type $input
 * @param unknown_type $delimiter
 * @param unknown_type $enclosure
 * @param unknown_type $escape
 * @param unknown_type $eol
 */
function str_getcsv($input, $delimiter = ',', $enclosure = '"', $escape = '\\', $eol = '\n')
{
	if (is_string($input) && !empty($input)) {
            $output = array();
            $tmp    = preg_split("/".$eol."/",$input);
            if (is_array($tmp) && !empty($tmp)) {
                while (list($line_num, $line) = each($tmp)) {
                    if (preg_match("/".$escape.$enclosure."/",$line)) {
                        while ($strlen = strlen($line)) {
                            $pos_delimiter       = strpos($line,$delimiter);
                            $pos_enclosure_start = strpos($line,$enclosure);
                            if (
                                is_int($pos_delimiter) && is_int($pos_enclosure_start)
                                && ($pos_enclosure_start < $pos_delimiter)
                                ) {
                                $enclosed_str = substr($line,1);
                                $pos_enclosure_end = strpos($enclosed_str,$enclosure);
                                $enclosed_str = substr($enclosed_str,0,$pos_enclosure_end);
                                $output[$line_num][] = $enclosed_str;
                                $offset = $pos_enclosure_end+3;
                            } else {
                                if (empty($pos_delimiter) && empty($pos_enclosure_start)) {
                                    $output[$line_num][] = substr($line,0);
                                    $offset = strlen($line);
                                } else {
                                    $output[$line_num][] = substr($line,0,$pos_delimiter);
                                    $offset = (
                                                !empty($pos_enclosure_start)
                                                && ($pos_enclosure_start < $pos_delimiter)
                                                )
                                                ?$pos_enclosure_start
                                                :$pos_delimiter+1;
                                }
                            }
                            $line = substr($line,$offset);
                        }
                    } else {
                        $line = preg_split("/".$delimiter."/",$line);
   
                        /*
                         * Validating against pesky extra line breaks creating false rows.
                         */
                        if (is_array($line) && !empty($line[0])) {
                            $output[$line_num] = $line;
                        } 
                    }
                }
                return $output;
            } else {
                return false;
            }
        } else {
            return false;
        }
}



if (isset($_POST['boton']))
{
	if (sha1($_POST['execution_pass']) === '216835532e6f5e521f84389aad08043ddb657493')
	{	
		$db = mysql_connect("irbsvr3.irb.pcb.ub.es", "root", "X24mnt32" ) or die ( "I cannot connect to the database because: " . mysql_error());
		mysql_select_db("irbdb");

		$query = "SELECT DISTINCT p.personalcode"
		. ", p.name AS name"
		. ", p.surname1 AS surname"
		. ", ou.description AS department"
		. ", u.description AS unit"
		. ", rg.description AS research_group"
		. ", pro.email AS email"
		. ", pro.phone AS phone"
		. ", po.description AS position"
		. ", pro.location AS location"
		. " FROM personal AS p"
		. " LEFT JOIN professional AS pro ON pro.professional_personal = p.personalcode"
		. " LEFT JOIN position AS po ON po.positioncode = pro.position"
		. " LEFT JOIN unit AS u ON u.unitcode = pro.professional_unit"
		. " LEFT JOIN research_group AS rg ON rg.research_groupcode = pro.research_group"
		. " LEFT JOIN organization_unit AS ou ON ou.organization_unitcode = u.organization_unit"
		. " WHERE"
		//. " (pro.end_date IS NULL OR (pro.start_date < NOW() AND pro.end_date > NOW()))" // sin verificacion de fechas
		. " p.deleted = ''"
		. " AND pro.deleted = ''"
		. " AND pro.current != ''"
		. " AND p.state = '5'"
		//. " INTO OUTFILE '" . $filename . "'"
		//. " FIELDS TERMINATED BY '" . $sep . "'"
		//. " ENCLOSED BY '\"'"
		;
		$result = mysql_query($query);		
		if (!$result) {
			die('Invalid query: ' . mysql_error());
		}
		
		// separator es ; para evitar problemas con las comas en los nombres de la BD
		$sep = ";";
			
		// making the file
		$filerow = array();
		$filerow['0'] = "User Id" . $sep . "Name" . $sep . "Surname" . $sep . "Department" . $sep
		. "Unit" . $sep . "Research Group" . $sep . "Email" . $sep . "Phone" . $sep
		. "Position" . $sep . "Location" . "\n"
		;
		
		while ($row = mysql_fetch_assoc($result))
		{
			// uppercases and so on...
			$name =  ucwords(strtolower(strtr(utf8_encode($row['name']), utf8_encode("ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ"), utf8_encode("àáâãäåæçèéêëìíîïðñòóôõöøùüú"))));
			$surname =  ucwords(strtolower(strtr(utf8_encode($row['surname']), utf8_encode("ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÜÚ"), utf8_encode("àáâãäåæçèéêëìíîïðñòóôõöøùüú"))));
			
			$pc = ltrim($row['personalcode'], "0");
			
			$filerow[$pc] = $pc . $sep . $name . $sep . $surname . $sep . $row['department'] . $sep
			. $row['unit'] . $sep . $row['research_group'] . $sep . $row['email'] . $sep . $row['phone'] . $sep
			. $row['position'] . $sep . $row['location'] . "\n"
			;
		}
		
		// excepciones
		$lines = file("./irbpeople2biko_diff.csv");
		foreach($lines as $line)
		{
			$line_diff = str_getcsv($line);
			switch($line_diff[0][0])
			{
				case 'add':
					$name =  $line_diff[0][2];
					$surname =  $line_diff[0][3];
										
					$filerow[$line_diff[0][1] . "bis"] = $line_diff[0][1] . $sep . $name . $sep . $surname . $sep . $line_diff[0][4] . $sep
					. $line_diff[0][5] . $sep . $line_diff[0][6] . $sep . $line_diff[0][7] . $sep . $line_diff[0][8] . $sep
					. $line_diff[0][9] . $sep . $line_diff[0][10] . "\n"
					;
					break;
				case 'mod':
					// borro el antiguo
					unset($filerow[$line_diff[0][1]]);
					// creo el nuevo
					$name =  $line_diff[0][2];
					$surname =  $line_diff[0][3];
										
					$filerow[$line_diff[0][1]] = $line_diff[0][1] . $sep . $name . $sep . $surname . $sep . $line_diff[0][4] . $sep
					. $line_diff[0][5] . $sep . $line_diff[0][6] . $sep . $line_diff[0][7] . $sep . $line_diff[0][8] . $sep
					. $line_diff[0][9] . $sep . $line_diff[0][10] . "\n"
					;
					break;
				case 'ins':
					$name =  $line_diff[0][2];
					$surname =  $line_diff[0][3];
										
					$filerow[$line_diff[0][1]] = $line_diff[0][1] . $sep . $name . $sep . $surname . $sep . $line_diff[0][4] . $sep
					. $line_diff[0][5] . $sep . $line_diff[0][6] . $sep . $line_diff[0][7] . $sep . $line_diff[0][8] . $sep
					. $line_diff[0][9] . $sep . $line_diff[0][10] . "\n"
					;
					break;
				case 'del':
					unset($filerow[$line_diff[0][1]]);
					break;
			}
		}
				
		// mail
		$recipients = "roberto.bartolome@irbbarcelona.org";
		//$recipients .= ", ocer@irbbarcelona.org";
		
		// mail text
        $text = 'Please find attached to this mail the IRBpeople data file.';
        $crlf = "\n";	
        		
		$headers["From"] = "its@irbbarcelona.org";
		$headers["To"] = "alvaro.cornago@biko2.com, cristina.mendez@irbbarcelona.org";
		$headers["Cc"] = "ocer@irbbarcelona.org, roberto.bartolome@irbbarcelona.org";
		//$headers['Reply-To'] = "its@irbbarcelona.org";
		$headers["Subject"] = "Data from IRBPeople";

        // Creating the Mime message
        $mime = new Mail_mime($crlf);
        
        // Setting the body of the email
        $mime->setTXTBody($text);
        
        // Add an attachment
		$filename = "irbpeople_" . date("Ymd") . ".csv";		
        $content_type = "Application/csv";
		$filestr = '';
        foreach ($filerow as $value)
		{
			$filestr .= $value;
		}
      
        $mime->addAttachment($filestr, $content_type, $filename, false);  // Add the attachment to the email
        $body = $mime->get();
        $headers = $mime->headers($headers);
        		
		$smtpinfo["host"] = "smtp.pcb.ub.es";		
		$mail_object =& Mail::factory("smtp", $smtpinfo);
		$mail_object->send($recipients, $headers, $body);
		
		echo "The file has been sent.";
		exit;
	} else {
		echo "Sorry. Wrong execution password.";
		exit;
	}
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<h2>Sending information to Biko2</h2>
This script will send the IRBPeople data to Biko2. Please enter below the execution password to validate your rights.
<br>
<br>
<form name="form" method="post" action="irbpeople2biko.php">
	<table border="0">
	<tr>
		<td align="right">Execution Password:</td>
		<td><input name="execution_pass" type="password" size="50" maxlength="50"></td>
	</tr>
	<tr>
		<td colspan="2"><input type="submit" name="boton" value="Send file"></td>
	</tr>
	</table>
</form>
</body>
</html>
