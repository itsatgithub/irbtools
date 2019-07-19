<?php
// basic sequence with LDAP is connect, bind, search, interpret search
// result, close connection

ini_set( 'display_errors', 0 );

$groups = array(
	array("name" => "administration", "edir_tree" => "ou=users,ou=admini,o=irbbarcelona", "ad_tree" => "OU=users,OU=admini,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "celdevbio", "edir_tree" => "ou=users,ou=celdevbio,o=irbbarcelona", "ad_tree" => "OU=users,OU=celdevbio,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "chemolpha", "edir_tree" => "ou=users,ou=chemolpha,o=irbbarcelona", "ad_tree" => "OU=users,OU=chemolpha,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "corfac", "edir_tree" => "ou=users,ou=corfac,o=irbbarcelona", "ad_tree" => "OU=users,OU=corfac,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "molmed", "edir_tree" => "ou=users,ou=molmed,o=irbbarcelona", "ad_tree" => "OU=users,OU=molmed,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "oncolo", "edir_tree" => "ou=users,ou=oncolo,o=irbbarcelona", "ad_tree" => "OU=users,OU=oncolo,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "strcombio", "edir_tree" => "ou=users,ou=strcombio,o=irbbarcelona", "ad_tree" => "OU=users,OU=strcombio,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	);
$array2 = array(
	array("name" => "proexp", "edir_tree" => "cn=proexp,ou=groups,ou=corfac,o=irbbarcelona", "ad_tree" => "CN=proexp,OU=groups,OU=corfac,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "masspe", "edir_tree" => "cn=masspe,ou=groups,ou=corfac,o=irbbarcelona", "ad_tree" => "CN=masspe,OU=groups,OU=corfac,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "moumut", "edir_tree" => "cn=moumut,ou=groups,ou=corfac,o=irbbarcelona", "ad_tree" => "CN=moumut,OU=groups,OU=corfac,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "genomics", "edir_tree" => "cn=genomics,ou=groups,ou=corfac,o=irbbarcelona", "ad_tree" => "CN=genomics,OU=groups,OU=corfac,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "biostats", "edir_tree" => "cn=biostats,ou=groups,ou=corfac,o=irbbarcelona", "ad_tree" => "CN=biostats,OU=groups,OU=corfac,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "adm", "edir_tree" => "cn=adm,ou=groups,ou=corfac,o=irbbarcelona", "ad_tree" => "CN=adm,OU=groups,OU=corfac,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "Cryopreservation Service", "edir_tree" => "cn=Cryopreservation Service,ou=groups,ou=corfac,o=irbbarcelona", "ad_tree" => "CN=Cryopreservation Service,OU=groups,OU=corfac,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "acelada_g", "edir_tree" => "cn=acelada_g,ou=groups,ou=molmed,o=irbbarcelona", "ad_tree" => "CN=acelada_g,OU=groups,OU=molmed,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "azorzano_g", "edir_tree" => "cn=azorzano_g,ou=groups,ou=molmed,o=irbbarcelona", "ad_tree" => "CN=azorzano_g,OU=groups,OU=molmed,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "ccaelles_g", "edir_tree" => "cn=ccaelles_g,ou=groups,ou=molmed,o=irbbarcelona", "ad_tree" => "CN=ccaelles_g,OU=groups,OU=molmed,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "guinovart_g", "edir_tree" => "cn=guinovart_g,ou=groups,ou=molmed,o=irbbarcelona", "ad_tree" => "CN=guinovart_g,OU=groups,OU=molmed,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "mpalacin_g", "edir_tree" => "cn=mpalacin_g,ou=groups,ou=molmed,o=irbbarcelona", "ad_tree" => "CN=mpalacin_g,OU=groups,OU=molmed,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "skyscan", "edir_tree" => "cn=skyscan,ou=groups,ou=molmed,o=irbbarcelona", "ad_tree" => "CN=skyscan,OU=groups,OU=molmed,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "ebatlle_g", "edir_tree" => "cn=ebatlle_g,ou=groups,ou=oncolo,o=irbbarcelona", "ad_tree" => "CN=ebatlle_g,OU=groups,OU=oncolo,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	//,array("name" => "esancho_g", "edir_tree" => "cn=esancho_g,ou=groups,ou=oncolo,o=irbbarcelona", "ad_tree" => "CN=esancho_g,OU=groups,OU=oncolo,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "metlab", "edir_tree" => "cn=metlab,ou=groups,ou=oncolo,o=irbbarcelona", "ad_tree" => "CN=metlab,OU=groups,OU=oncolo,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "tstracker_g", "edir_tree" => "cn=tstracker_g,ou=groups,ou=oncolo,o=irbbarcelona", "ad_tree" => "CN=tstracker_g,OU=groups,OU=oncolo,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "ebl", "edir_tree" => "cn=ebl,ou=groups,ou=strcombio,o=irbbarcelona", "ad_tree" => "CN=ebl,OU=groups,OU=strcombio,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "ifita_g", "edir_tree" => "cn=ifita_g,ou=groups,ou=strcombio,o=irbbarcelona", "ad_tree" => "CN=ifita_g,OU=groups,OU=strcombio,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "mcoll_g", "edir_tree" => "cn=mcoll_g,ou=groups,ou=strcombio,o=irbbarcelona", "ad_tree" => "CN=mcoll_g,OU=groups,OU=strcombio,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "mmacias_g", "edir_tree" => "cn=mmacias_g,ou=groups,ou=strcombio,o=irbbarcelona", "ad_tree" => "CN=mmacias_g,OU=groups,OU=strcombio,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "mpons_g", "edir_tree" => "cn=mpons_g,ou=groups,ou=strcombio,o=irbbarcelona", "ad_tree" => "CN=mpons_g,OU=groups,OU=strcombio,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "orozco", "edir_tree" => "cn=orozco,ou=groups,ou=strcombio,o=irbbarcelona", "ad_tree" => "CN=orozco,OU=groups,OU=strcombio,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "paloy_g", "edir_tree" => "cn=paloy_g,ou=groups,ou=strcombio,o=irbbarcelona", "ad_tree" => "CN=paloy_g,OU=groups,OU=strcombio,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "falbericio_g", "edir_tree" => "cn=falbericio_g,ou=groups,ou=chemolpha,o=irbbarcelona", "ad_tree" => "CN=falbericio_g,OU=groups,OU=chemolpha,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "eritja_g", "edir_tree" => "cn=eritja_g,ou=groups,ou=chemolpha,o=irbbarcelona", "ad_tree" => "CN=eritja_g,OU=groups,OU=chemolpha,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "egiralt_g", "edir_tree" => "cn=egiralt_g,ou=groups,ou=chemolpha,o=irbbarcelona", "ad_tree" => "CN=egiralt_g,OU=groups,OU=chemolpha,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "ariera_g", "edir_tree" => "cn=ariera_g,ou=groups,ou=chemolpha,o=irbbarcelona", "ad_tree" => "CN=ariera_g,OU=groups,OU=chemolpha,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "mrubiralta_g", "edir_tree" => "cn=mrubiralta_g,ou=groups,ou=chemolpha,o=irbbarcelona", "ad_tree" => "CN=mrubiralta_g,OU=groups,OU=chemolpha,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "lmb", "edir_tree" => "cn=lmb,ou=groups,ou=chemolpha,o=irbbarcelona", "ad_tree" => "CN=lmb,OU=groups,OU=chemolpha,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "fazorin_g", "edir_tree" => "cn=fazorin_g,ou=groups,ou=celdevbio,o=irbbarcelona", "ad_tree" => "CN=fazorin_g,OU=groups,OU=celdevbio,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "jcasanova_g", "edir_tree" => "cn=jcasanova_g,ou=groups,ou=celdevbio,o=irbbarcelona", "ad_tree" => "CN=jcasanova_g,OU=groups,OU=celdevbio,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "cgonzalez_g", "edir_tree" => "cn=cgonzalez_g,ou=groups,ou=celdevbio,o=irbbarcelona", "ad_tree" => "CN=cgonzalez_g,OU=groups,OU=celdevbio,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "jluders_g", "edir_tree" => "cn=jluders_g,ou=groups,ou=celdevbio,o=irbbarcelona", "ad_tree" => "CN=jluders_g,OU=groups,OU=celdevbio,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "mmilan_g", "edir_tree" => "cn=mmilan_g,ou=groups,ou=celdevbio,o=irbbarcelona", "ad_tree" => "CN=mmilan_g,OU=groups,OU=celdevbio,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "lribas_g", "edir_tree" => "cn=lribas_g,ou=groups,ou=celdevbio,o=irbbarcelona", "ad_tree" => "CN=lribas_g,OU=groups,OU=celdevbio,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	,array("name" => "esoriano_g", "edir_tree" => "cn=esoriano_g,ou=groups,ou=celdevbio,o=irbbarcelona", "ad_tree" => "CN=esoriano_g,OU=groups,OU=celdevbio,DC=irbbarcelona,DC=pcb,DC=ub,DC=es")
	);
	
echo "<h3>LDAP tests</h3>";
echo "Date: " . date("j/F/Y, g:i a") . "<br />";
echo "Connecting ...<br />";

$edir = ldap_connect("irbsvr4.irb.pcb.ub.es");
echo "eDir connect result is " . $edir . "<br />";

$ad = ldap_connect("irbsvr31.irb.pcb.ub.es");
echo "AD connect result is " . $ad . "<br />";
ldap_set_option($ad, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ad, LDAP_OPT_REFERRALS, 0);

if ($edir && $ad) { 
    echo "Binding ...<br />"; 
	$ldaprdn  = 'cn=admin,o=irbbarcelona';     // ldap rdn or dn
	$ldappass = 'irbbarcelona';  // associated password
    $edir_bind=ldap_bind($edir); // anonymous bind
    //$edir_bind=ldap_bind($edir, $ldaprdn, $ldappass);
    echo "Edir Bind result is " . $edir_bind . "<br />";
    
    $ldaprdn = 'CN=Administrator,CN=users,DC=irbbarcelona,DC=pcb,DC=ub,DC=es';
	$ldappass = '*********';  // associated password
    $ad_bind=ldap_bind($ad, $ldaprdn, $ldappass)
    //$ad_bind=ldap_bind($ad) // anonymous bind
    or die("Couldn't bind to AD!");
    echo "AD Bind result is " . $ad_bind . "<br />";
    echo "---------------------------------------------------------------------------------------<br />";
    
    // Preparación
    //prep_a("(objectclass=group)");
    
    // Tests
    test1("(&(objectclass=organizationalPerson)(IRB-ITActive=TRUE))");
    //test1("(&(objectclass=organizationalPerson)(!(ou:dn:=inactive)))");
    //test2("(objectclass=organizationalPerson)");
  	//test3("(objectclass=organizationalPerson)");
    //test4("(objectclass=group)");
    //test5("(objectclass=group)");
    //test6("(objectclass=organizationalPerson)");
    //test7("(&(objectclass=organizationalPerson)(IRB-ITActive=TRUE))");
    //test8("(objectclass=group)");
    
    echo "Closing connections<br />";
    ldap_close($edir);
    ldap_close($ad);
    
} else {
    echo "<h4>Unable to connect to LDAP server</h4>";
}

/**
 * Borra todos los atributos 'member Unix Workstation'
 * 
 * @param string $filter The filter used to look for objects
 * @return none
 */
function prep_a($filter)
{
	global $edir, $ad, $array2;
	
	echo "Preparacion A. Borra todos los atributos 'member Unix Workstation'<br />";
	$entry["member"][0] = "cn=UNIX Workstation - irbsvr4,ou=services,o=irbbarcelona"; 
	$entry["member"][1] = "cn=UNIX Workstation - irbsvr12,ou=services,o=irbbarcelona"; 
	foreach ($array2 as $group)
	{
		echo $group['name'] . ": ";
		$result = ldap_mod_del($edir, $group['edir_tree'], $entry); 
	    echo "<br />";
	}
    echo "---------------------------------------------------------------------------------------<br />";
}



/**
 * Cuenta usuarios en uno y otro directorio usando el mismo filtro de busqueda
 * 
 * @param string $filter The filter used to look for users
 * @return none
 */
function test1($filter)
{
	global $edir, $ad, $groups;	
	
	echo "test W1. Cuenta usuarios en uno y otro directorio usando el mismo filtro de busqueda.<br />";
    $edir_sr = ldap_search($edir, "o=irbbarcelona", $filter);
    $number_edir = ldap_count_entries($edir, $edir_sr);
    $ad_sr = ldap_search($ad, "DC=irbbarcelona,DC=pcb,DC=ub,DC=es", $filter);
    $number_ad = ldap_count_entries($ad, $ad_sr);

    echo "Getting entries ...<br />";
    $info = ldap_get_entries($edir, $edir_sr);
    echo "Data for " . $info["count"] . " items returned:<br />";

    for ($i=0; $i<$info["count"]; $i++) {
    	//print_r($info[$i]);
    	//echo "<br />";
    	
    	/*
        echo "dn is: " . $info[$i]["dn"] . "<br />";
        echo "first cn entry is: " . $info[$i]["cn"][0] . "<br />";
        echo "first email entry is: " . $info[$i]["mail"][0] . "<br /><hr />";
        */
    }

    echo "eDir: " . $number_edir . " AD: " . $number_ad . " ";
    echo $number_edir == $number_ad ? '<img src="ok.png" border="0" width="20" height="20" />' : '<img src="nok.png" border="0" width="20" height="20" />';
    echo "<br />";
    echo "---------------------------------------------------------------------------------------<br />";
}


/**
 * Comprueba el número de usuarios en los programas de investigación
 * 
 * @param string $filter The filter used to look for users
 * @return none
 */
function test2($filter)
{
	global $edir, $ad, $groups;
	
	echo "test W2. Comprueba el numero de usuarios en los programas de investigacion.<br />";
	foreach ($groups as $group)
	{
		echo $group['name'] . ": ";
	    $edir_sr = ldap_search($edir, $group['edir_tree'], $filter);
	    $number_edir = ldap_count_entries($edir, $edir_sr);    
	    $ad_sr = ldap_search($ad, $group['ad_tree'], $filter);
	    $number_ad = ldap_count_entries($ad, $ad_sr);
	    echo "eDir: " . $number_edir . " AD: " . $number_ad . " ";
	    echo $number_edir == $number_ad ? '<img src="ok.png" border="0" width="20" height="20" />' : '<img src="nok.png" border="0" width="20" height="20" />';
	    echo "<br />";
	}
    echo "---------------------------------------------------------------------------------------<br />";
}
	


/**
 * Comprueba que todos los usuarios en 'inactive' tienen IT-Active a false y login disable
 * 
 * @param string $filter The filter used to look for users
 * @return none
 */
function test3($filter)
{
	global $edir, $ad, $groups;
	
	echo "test W3. Comprueba que todos los usuarios en 'inactive' tienen IT-Active a false y login disable.<br />";

	echo "eDirectory<br />";	
	$edir_sr = ldap_search($edir, "ou=inactive,o=irbbarcelona", $filter);
	$info = ldap_get_entries($edir, $edir_sr);
    $number_edir = ldap_count_entries($edir, $edir_sr);
    for ($i=0; $i<$info["count"]; $i++)
    {
        echo "dn is: " . $info[$i]["dn"] . " IRB-ITActive is: " . $info[$i]["irb-itactive"][0] . " loginDisabled is: " . $info[$i]["logindisabled"][0];
        echo (($info[$i]["irb-itactive"][0] == "FALSE") && ($info[$i]["logindisabled"][0] == "TRUE")) ? '<img src="ok.png" border="0" width="20" height="20" />' : '<img src="nok.png" border="0" width="20" height="20" />';
	    echo "<br />";
    }
    
	echo "Active Directory<br />";
    $ad_sr = ldap_search($ad, "OU=inactive,DC=irbbarcelona,DC=pcb,DC=ub,DC=es", $filter);
    $info = ldap_get_entries($ad, $ad_sr);
    $number_ad = ldap_count_entries($ad, $ad_sr);
    for ($i=0; $i<$info["count"]; $i++)
    {
   		//print_r($info[$i]);
    	//echo "<br />";
    	
    	echo "displayName is: " . $info[$i]["displayname"][0] . " useraccountcontrol is: " . $info[$i]["useraccountcontrol"][0];
        echo $info[$i]["useraccountcontrol"][0] == 514 ? '<img src="ok.png" border="0" width="20" height="20" />' : '<img src="nok.png" border="0" width="20" height="20" />';
	    echo "<br />";
    }
    echo "---------------------------------------------------------------------------------------<br />";
}


/**
 * Comprueba todos los usuarios de los grupos de investigación
 * 
 * @param string $filter The filter used to look for users
 * @return none
 */
function test4($filter)
{
	global $edir, $ad, $array2;
	
	echo "test W4. Comprueba todos los usuarios de los grupos de investigación<br />";
	foreach ($array2 as $group)
	{
		echo $group['name'] . ": ";
		
	    $edir_sr = ldap_search($edir, $group['edir_tree'], $filter);
	    $entry = ldap_first_entry($edir, $edir_sr);
	    $attrs = ldap_get_attributes($edir, $entry);
	    $number_edir = $attrs["member"]["count"];
	    
	    $ad_sr = ldap_search($ad, $group['ad_tree'], $filter);
	    $entry = ldap_first_entry($ad, $ad_sr);
	    $attrs = ldap_get_attributes($ad, $entry);	    
	    $number_ad = $attrs["member"]["count"];
	    
	    echo "eDir: " . $number_edir . " AD: " . $number_ad . " ";
	    if ($number_edir == 0 || $number_ad == 0)
	    {
	    	echo '<img src="nok.png" border="0" width="20" height="20" />';
	    } else {
	    	echo $number_edir == $number_ad ? '<img src="ok.png" border="0" width="20" height="20" />' : '<img src="nok.png" border="0" width="20" height="20" />';
	    }
	    echo "<br />";
	}
    echo "---------------------------------------------------------------------------------------<br />";
}



/**
 * Comprueba que el numero de elementos en member y en memberUid sean los mismos para los dos directorios
 * 
 * @param string $filter The filter used to look for users
 * @return none
 */
function test5($filter)
{
	global $edir, $ad, $array2;
	
	echo "test W5. Comprueba que el numero de elementos en member y en memberUid sean los mismos para los dos directorios<br />";
	foreach ($array2 as $group)
	{
		echo $group['name'] . ": ";
		
	    $edir_sr = ldap_search($edir, $group['edir_tree'], $filter);
	    $entry = ldap_first_entry($edir, $edir_sr);
	    $attrs = ldap_get_attributes($edir, $entry);
	    $number_edir = $attrs["member"]["count"];
	    $number2_edir = $attrs["memberUid"]["count"];
	    
	    echo "eDir: " . $number_edir . " - " . $number2_edir . " ";
	    if ($number_edir == 0 || $number2_edir == 0)
	    {
	    	echo '<img src="nok.png" border="0" width="20" height="20" />';
	    } else {
	    	echo $number_edir == $number2_edir ? '<img src="ok.png" border="0" width="20" height="20" />' : '<img src="nok.png" border="0" width="20" height="20" />';
	    }
	    echo "<br />";
	}
    echo "---------------------------------------------------------------------------------------<br />";
}




/**
 * Comprueba que todos los usuarios tienen valor en ou
 * 
 * @param string $filter The filter used to look for users
 * @return none
 */
function test6($filter)
{
	global $edir, $ad, $groups;
	
	echo "test W6. Comprueba diversos valores obigatorios en los atributos de los usuarios.<br />";
	foreach ($groups as $group)
	{
		$edir_sr = ldap_search($edir, $group['edir_tree'], $filter);
		for ($entryID = ldap_first_entry($edir, $edir_sr); $entryID != false; $entryID = ldap_next_entry($edir, $entryID))
		{
			$string = '';
			
			// Valor en ou
			$values = ldap_get_values($edir, $entryID, 'ou');
			if ( $values[0] == '' ) {
	    		$string .= '<li>No tiene valor en ou <img src="nok.png" border="0" width="20" height="20" /></li>';
			} 
			// IRB-UserCode
			$values = ldap_get_values($edir, $entryID, 'IRB-UserCode');
			if ( $values[0] == '' ) {
	    		$string .= '<li>No tiene valor en IRB-UserCode <img src="nok.png" border="0" width="20" height="20" /></li>';
			} 
			// IRB-UserCode
			$values = ldap_get_values($edir, $entryID, 'IRB-Unit');
			if ( $values[0] == '' ) {
	    		$string .= '<li>No tiene valor en IRB-Unit <img src="nok.png" border="0" width="20" height="20" /></li>';
			} 
			// IRB-AD
			$values = ldap_get_values($edir, $entryID, 'IRB-AD');
			if ( $values[0] == '' ) {
	    		$string .= '<li>No tiene valor en IRB-AD <img src="nok.png" border="0" width="20" height="20" /></li>';
			} 
			// IRB-IdUnit
			$values = ldap_get_values($edir, $entryID, 'IRB-IdUnit');
			if ( $values[0] == '' ) {
	    		$string .= '<li>No tiene valor en IRB-IdUnit <img src="nok.png" border="0" width="20" height="20" /></li>';
			} 
			// IRB-PasswordInicial
			$values = ldap_get_values($edir, $entryID, 'IRB-PasswordInicial');
			if ( $values[0] == '' ) {
	    		//$string .= '<li>No tiene valor en IRB-PasswordInicial <img src="nok.png" border="0" width="20" height="20" /></li>';
			} 
			// IRB-FechaAlta
			$values = ldap_get_values($edir, $entryID, 'IRB-FechaAlta');
			if ( $values[0] == '' ) {
	    		$string .= '<li>No tiene valor en IRB-FechaAlta <img src="nok.png" border="0" width="20" height="20" /></li>';
			} 
			// IRB-GroupDel
			$values = ldap_get_values($edir, $entryID, 'IRB-GroupDel');
			if ( $values[0] == '' ) {
	    		//$string .= '<li>No tiene valor en IRB-GroupDel <img src="nok.png" border="0" width="20" height="20" /></li>';
			} 
			// IRB-State
			$values = ldap_get_values($edir, $entryID, 'IRB-State');
			if ( $values[0] == '' ) {
	    		$string .= '<li>No tiene valor en IRB-State <img src="nok.png" border="0" width="20" height="20" /></li>';
			} 
			// IRB-InitGroup
			$values = ldap_get_values($edir, $entryID, 'IRB-InitGroup');
			if ( $values[0] == '' ) {
	    		//$string .= '<li>No tiene valor en IRB-InitGroup <img src="nok.png" border="0" width="20" height="20" /></li>';
			} 
			
			// printing results
			if ($string) {
				$cn = ldap_get_values($edir, $entryID, "cn");
				echo "Usuario : " . $cn[0] . "<ul>" . $string . "</ul>";
			}
		}		
	}
    echo "---------------------------------------------------------------------------------------<br />";
}



/**
 * Comprueba, para cada usuario activo en el directorio, si esta incluido en los 'members' de su grupo
 * 
 * @param string $filter The filter used to look for users
 * @return none
 */
function test7($filter)
{
	global $edir, $ad, $groups;
	
	echo "test W7. Comprueba, para cada usuario activo en el directorio, si esta incluido en los 'members' de su grupo.<br />";
	
	$i=1;
	$edir_sr = ldap_search($edir, 'o=irbbarcelona', $filter); // busca todos los usuarios activos...
	for ($entryID = ldap_first_entry($edir, $edir_sr); $entryID != false; $entryID = ldap_next_entry($edir, $entryID))
	{		
		$cn = ldap_get_values($edir, $entryID, "cn");
		// el cn del usuario es $cn[0]
		// echo "Usuario" . $i . " : " . $cn[0] . '<br />';
		
		$gidNumber = ldap_get_values($edir, $entryID, "gidNumber");
		// el gidNumber del usuario es $gidNumber
		
		// ... para cada usuario, cual es su grupo
		$group_sr = ldap_search($edir, 'o=irbbarcelona', '(&(objectclass=posixGroup)(gidNumber=' . $gidNumber[0] . '))');
		$auxentryID = ldap_first_entry($edir, $group_sr);
		$description = ldap_get_values($edir, $auxentryID, "description");
		$members = ldap_get_values($edir, $auxentryID, "member");
		
		$ok = false;
		foreach ($members as $member)
		{
			// echo "member = " . $member . '<br />';
			// echo "cn[0] = " . $cn[0] . '<br />';
			$pos = strpos($member, $cn[0]);
			// echo "pos = " . $pos . '<br />';
			if ($pos) {
				$ok = true;
			}
		}
		
		if ($ok) {
			// echo "The user " . $cn[0] . " is a member of the group " . $description[0] . '<img src="ok.png" border="0" width="20" height="20" />' . '<br />';
		} else {
			echo "The user " . $cn[0] . " is not a member of the group " . $description[0] . '<img src="nok.png" border="0" width="20" height="20" />' . '<br />';
		}

		$i++;
	}
    echo "---------------------------------------------------------------------------------------<br />";
}



/**
 * Comprueba, para cada grupo, si todos los members le tienen como grupo
 * 
 * @param string $filter The filter used to look for users
 * @return none
 */
function test8($filter)
{
	global $edir, $ad, $array2;
	
	echo "test W8. Comprueba, para cada grupo, si todos los members le tienen como grupo.<br />";
	
	$edir_sr = ldap_search($edir, 'o=irbbarcelona', $filter); // busca todos los usuarios activos...
	for ($entryID = ldap_first_entry($edir, $edir_sr); $entryID != false; $entryID = ldap_next_entry($edir, $entryID))
	{		
		// fuera admin, para evitar muchos datos...
		if ($entry[0]["cn"][0] == 'admin') {
			continue;
		}
		
		// el gidNumber del grupo que analizamos
		$group_cn = ldap_get_values($edir, $entryID, "cn");
		$gidNumber = ldap_get_values($edir, $entryID, "gidNumber");
		
		$group_sr = ldap_search($edir, 'o=irbbarcelona', '(&(objectclass=posixGroup)(gidNumber=' . $gidNumber[0] . '))');
		$auxentryID = ldap_first_entry($edir, $group_sr);
		$description = ldap_get_values($edir, $auxentryID, "description");
		$members = ldap_get_values($edir, $auxentryID, "member"); // todos los members del grupo...
		
		$filter="(objectclass=*)"; // this command requires some filter
		$justthese = array("cn", "gidNumber");
		 		
		$ok = false;
		for ($i=0; $i < $members["count"]; $i++)
		{
			$sr = ldap_read($edir, $members[$i], $filter, $justthese);
			$entry = ldap_get_entries($edir, $sr); // ... para cada member, miro su gidNumber
			
			// ...quito las workstations...
			if ($entry[0]["cn"][0] == 'UNIX Workstation - irbsvr4' || $entry[0]["cn"][0] == 'UNIX Workstation - irbsvr12') {
				continue;
			}
			
			if ($entry[0]["gidnumber"][0] != $gidNumber[0]) {
				echo "grupo = " . $group_cn[0] . " usuario = " . $entry[0]["cn"][0] . " gidNumber del grupo = " . $gidNumber[0] . " gidNumber del usuario = " . $entry[0]["gidnumber"][0] . "<br />";
			}
		}
	}
    echo "---------------------------------------------------------------------------------------<br />";
}




function test()
{
	global $edir, $ad;
	
	echo "test 2<br />";
	
	
	
    echo "---------------------------------------------------------------------------------------<br />";
}

?>
