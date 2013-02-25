<?php
set_time_limit(0);
//MySQL globals
$mysql_server = "localhost";//change this server for your Drupal installations
$mysql_user = "root";//Ideally, should be root
$mysql_pass = "Km91OP12";//Corresponding password
$conn = mysql_connect($mysql_server,$mysql_user,$mysql_pass);//MySQL connection string


//first, we delete all users, if any, from the new table, except for the admin
$query = "delete from staging.users where uid not in (0,1)";
mysql_query($query);
print "User table cleared. <br />";
//now we start inserting users into the new table
$query = "select * from ama.users where uid not in (0,1)";
$queryresult = mysql_query($query);
while ($row = mysql_fetch_row($queryresult)) {
    $query = "insert into staging.users values('" . $row[0] . "','" . mysql_real_escape_string($row[1]) . "','" . mysql_real_escape_string($row[2]) . "','" . $row[3] . "','" . $row[7] . "','" . mysql_real_escape_string($row[8]) . "',NULL,'" . $row[9] . "','" . $row[10] . "','" . $row[11] . "','" . $row[12] . "','" . $row[13] . "','" . $row[14] . "','" . $row[15] . "','" . mysql_real_escape_string($row[16]) . "','" . $row[17] . "')";
    if (!mysql_query($query)) {
        print $query;
    }
    else {
        $ucnt += 1;
    }
}
print $ucnt . " users inserted.";


?>
