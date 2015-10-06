<?php
require_once 'Zend/Loader.php';
require_once 'Mail.php';

Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
Zend_Loader::loadClass('Zend_Gdata_Gapps');

/*
if (isset($_POST['boton']))
{
	echo sha1($_POST['execution_pass']);
	exit;
}
*/

if (isset($_POST['boton']))
{
	if (sha1($_POST['execution_pass']) === 'cb548c0feb949b598e791043b9a526adac346807')
	{
		// busco el usuario en el directorio
		$edir = ldap_connect("irbsvr4.irb.pcb.ub.es");
		$ldaprdn  = 'cn=admin,o=irbbarcelona';
		$ldappass = 'irbbarcelona';  // associated password
    	$edir_bind = ldap_bind($edir, $ldaprdn, $ldappass);   	
    	$filter = "(&(objectclass=organizationalPerson)(givenName=" . $_POST['name'] . ")(sn=" . $_POST['surname'] . "))"; 
    	echo $filter;
    	$edir_sr = ldap_search($edir, "o=irbbarcelona", $filter);
    	$info = ldap_get_entries($edir, $edir_sr);
		
    	if ($info["count"] != 1)
    	{
    		echo "Numero incorrecto (cero o más de uno) de usuarios en eDirectory";
    		break;
    	}
    	
		// modifico los atributos de IRB-Email, mail, IRB-PasswordInicial
		$new = array();
		$new["IRB-Email"] = "true";
		$new["mail"] = $_POST['username'] . '@irbbarcelona.org';
		$new["IRB-PasswordInicial"] = $_POST['password'];
		$dn = $info[0]['dn'];
		ldap_modify($edir, $dn, $new); 
		
    	// cierro la conexión con eDir
    	ldap_close($edir);
		
		// datos de conexion
		$client = getClientLoginHttpClient('googleapi@irbbarcelona.org', 'google.support');
		$gapps = new Zend_Gdata_Gapps($client, 'irbbarcelona.org');
		
		// creacion de la cuenta
		$gapps->createUser($_POST['username'], $_POST['name'], $_POST['surname'], $_POST['password']);
		// subscripcion a las listas de correo
		$feed = addRecipientToEmailList($gapps, $_POST['username'] . '@irbbarcelona.org', 'irbscience');
		$feed = addRecipientToEmailList($gapps, $_POST['username'] . '@irbbarcelona.org', 'irbalert_social');
		$feed = addRecipientToEmailList($gapps, $_POST['username'] . '@irbbarcelona.org', 'irbalert_pcbannouncements');
		$feed = addRecipientToEmailList($gapps, $_POST['username'] . '@irbbarcelona.org', 'irbalert_planyourweek');
		$feed = addRecipientToEmailList($gapps, $_POST['username'] . '@irbbarcelona.org', 'irbalert_joboffers');
    	
    	// mail
		$recipients = 'cristina.mendez@irbbarcelona.org, its@irbbarcelona.org';
		//$recipients = 'roberto.bartolome@irbbarcelona.org';
		
		$headers["From"] = "roberto.bartolome@irbbarcelona.org";
		$headers["To"] = "cristina.mendez@irbbarcelona.org";
		$headers["Cc"] = "its@irbbarcelona.org";
		$headers['Reply-To'] = "its@irbbarcelona.org";
		$headers["Subject"] = "New email user account created for " . $_POST['name'] . " " . $_POST['surname'];
		
		// content
		$filename = "gmail_mail.txt";
		$handle = fopen($filename, "rb");
		$mailmsg = fread($handle, filesize($filename));
		
		// replacement
		$p1 = array("#name", "#username", "#password",  "#uid");
		$p2 = array($_POST['name'], $_POST['username'], $_POST['password'], $info[0]['uid'][0]);
		$mailmsg = str_replace($p1, $p2, $mailmsg);
			
		$smtpinfo["host"] = "smtp.pcb.ub.es";
		
		$mail_object =& Mail::factory("smtp", $smtpinfo);
		$mail_object->send($recipients, $headers, $mailmsg);
			
		echo "<br>Done!";
		
	} else {
		echo "Sorry. Wrong execution password.";
		exit;
	}
}


/**
* Returns a HTTP client object with the appropriate headers for communicating
* with Google using the ClientLogin credentials supplied.
*
* @param string $user The username, in e-mail address format, to authenticate
* @param string $pass The password for the user specified
* @return Zend_Http_Client
*/
function getClientLoginHttpClient($user, $pass) 
{
	$service = Zend_Gdata_Gapps::AUTH_SERVICE_NAME;
	$client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
	return $client;
}



/**
* Add an email address to a list on the current domain.
*
* @return array
*/
function addRecipientToEmailList($gapps, $recipient, $listName)
{		
	$feed = $gapps->addRecipientToEmailList($recipient, $listName);
	return $feed;		
}



/**
 * The letter l (lowercase L) and the number 1
 * have been removed, as they can be mistaken for each other.
 * 
 * @return array
 */
function createRandomPassword()
{
	$chars = "abcdefghijkmnopqrstuvwxyzABCDEFGHIJKMNOPQRSTUVWXYZ023456789";
    srand((double)microtime()*1000000);
    $i = 0;
    $pass = '' ;
    
    while ($i <= 7)
    {
    	$num = rand() % 58; // 0 to 58 = 59 characters
    	$tmp = substr($chars, $num, 1);
    	$pass = $pass . $tmp;
    	$i++;
    }
    return $pass;
}

$mypassword = createRandomPassword();

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<h2>Create a IRB Barcelona Gmail account</h2>
<form name="form" method="post" action="gmail_user.php">
	<table border="0">
	<tr>
		<td align="right">Name:</td>
		<td><input name="name" type="text" size="50" maxlength="50"></td>
		<td>eg. John</td>	
	</tr>
	<tr>
		<td align="right">Surname:</td>
		<td><input name="surname" type="text" size="50" maxlength="50"></td>
		<td>eg. Doe</td>
	</tr>
	<tr>
		<td align="right">Username:</td>
		<td><input name="username" type="text" size="50" maxlength="50"></td>
		<td>eg. john.doe</td>
	</tr>
	<tr>
		<td align="right">Password:</td>
		<td><input name="password" type="text" size="50" maxlength="50" value="<?php echo $mypassword; ?>"></td>
		<td>eg. Bt568yt3</td>
	</tr>
	<tr>
		<td align="right">Execution Password:</td>
		<td><input name="execution_pass" type="password" size="50" maxlength="50"></td>
		<td>info: Mandatory to validate the execution rights</td>
	</tr>
	<tr>
		<td colspan="3"><input type="submit" name="boton" value="Create account"></td>
	</tr>
	</table>
</form>
</body>
</html>
