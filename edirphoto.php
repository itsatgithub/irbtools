<?php
// basic sequence with LDAP is connect, bind, search, interpret search
// result, close connection

ini_set( 'display_errors', 0 );

echo "<h3>eDir photo</h3>";
echo "Date: " . date("j/F/Y, g:i a") . "<br />";
echo "Connecting ...<br />";

$edir = ldap_connect("irbsvr4.irb.pcb.ub.es");
echo "eDir connect result is " . $edir . "<br />";

if ($edir) { 
    echo "Binding ...<br />"; 
	$ldaprdn  = 'cn=admin,o=irbbarcelona';     // ldap rdn or dn
	$ldappass = 'irbbarcelona';  // associated password
    $edir_bind=ldap_bind($edir); // anonymous bind
    //$edir_bind=ldap_bind($edir, $ldaprdn, $ldappass);
    echo "Edir Bind result is " . $edir_bind . "<br /><br />";

    $array[] = "(&(objectclass=organizationalPerson)(IRB-UserType=I)(IRB-ITActive=TRUE))";
    $array[] = "(&(objectclass=organizationalPerson)(IRB-ITActive=TRUE)(IRB-Email=TRUE)(!(IRB-UserType=I)))";
    $array[] = "(&(objectclass=organizationalPerson)(IRB-ITActive=FALSE)(IRB-Email=TRUE)(IRB-UserType=I))";
    $array[] = "(&(objectclass=organizationalPerson)(IRB-ITActive=FALSE)(IRB-UserType=I)(IRB-Email=TRUE))";
    
    // Counters
    foreach ($array as $element)
    {
    	showthis($element);
    }
    
    echo "<br />Closing connections<br />";
    ldap_close($edir);
    ldap_close($ad);
    
} else {
    echo "<h4>Unable to connect to LDAP server</h4>";
}

function showthis($filter)
{
	global $edir;
	
	$edir_sr = ldap_search($edir, "o=irbbarcelona", $filter);
	echo $filter . " = " . ldap_count_entries($edir, $edir_sr) . "<br />";
}


?>
