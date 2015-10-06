<?php
// basic sequence with LDAP is connect, bind, search, interpret search
// result, close connection

// parameters
$conf = json_decode(file_get_contents('checkad_configuration.json'), TRUE);

echo "<h3>LDAP query test</h3>";
echo "Connecting ...";
$ad = ldap_connect($conf["ldap_connect"])
	or die("Couldn't connect to AD!");
ldap_set_option($ad, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ad, LDAP_OPT_REFERRALS, 0);

echo "connect result is " . $edir . "<br />";

if ($ad) { 
    echo "Binding ..."; 
	$ldaprdn = $conf["ldaprdn"];     // ldap rdn or dn
	$ldappass = $conf["ldappass"];  // associated password
    $ad_bind=ldap_bind($ad, $ldaprdn, $ldappass)
    	or die("Couldn't bind to AD!");
    echo "Bind result is " . $ad_bind . "<br />";

    echo "Searching users...";
    // Search surname entry
    $filter = "(&(objectclass=organizationalPerson)(!(ou:dn:=inactive)))";
    $attributes = array("displayname", "department");
    $sr=ldap_search($ad, "DC=irbbarcelona,DC=pcb,DC=ub,DC=es", $filter, $attributes);  
    echo "Search result is " . $sr . "<br />";

    echo "Number of entires returned is " . ldap_count_entries($ad, $sr) . "<br />";

    echo "Getting entries ...<p>";
    $info = ldap_get_entries($ad, $sr);
    echo "Data for " . $info["count"] . " items returned:<p>";

    for ($i=0; $i<$info["count"]; $i++) {
        echo "dn is: " . $info[$i]["dn"] . "<br />";
        echo "first cn entry is: " . $info[$i]["displayname"][0] . "<br />";
        echo "first email entry is: " . $info[$i]["department"][0] . "<br /><hr />";
    }

    echo "Closing connection";
    ldap_close($ad);

} else {
    echo "<h4>Unable to connect to LDAP server</h4>";
}

?>
