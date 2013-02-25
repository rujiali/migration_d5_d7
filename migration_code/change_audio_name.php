<?php
db_set_active("staging");
$nodes = mysql_query("select * from field_data_body join node on field_data_body.revision_id = node.vid where node.type = 'audio' order by node.nid");
while ($node = mysql_fetch_object($nodes)) {
    $vid = $node->revision_id; 
    $body = $node->body_value;
    $nid = $node->nid;
    //print $nid."\n"; 

    if ($body != '') {
        $body = change_name($body, $nid);
    }
    //print "\n".mysql_real_escape_string($body)."\n";
    $update_query = mysql_query("update field_data_body set body_value = '".mysql_real_escape_string($body)."' where revision_id = '".$vid."'");
    $update_query1 = mysql_query("update field_revision_body set body_value = '".mysql_real_escape_string($body)."' where revision_id = '".$vid."'");
    if ($update_query == TRUE && $update_query1 == TRUE) { 
        print "finished";
    } else {
        print mysql_error();
        print "fail";
    }
}

function change_name($body) {
  $url = explode('"', $body);
  $newname = str_replace('+', '_', $url[1]);
  $newname = str_replace('%2C' , '', $newname);
  $newname = str_replace('system/files/', 'sites/default/files/', $newname);
  $newname = str_replace('ama.com.au', 'staging.ama.com.au', $newname);
  //echo($url[2]."\n");
  $oggname = str_replace('mp3', 'ogg', $newname);
  $url[2] = ' /><source src="'.$oggname.'"'.$url[2];
  //change file system files
  $path = str_replace('http://ama.com.au/system/files/', '/var/www/ama.com.au/sites/default/files/', $url[1]);
  $path = str_replace('+', ' ', $path);
  $path = str_replace('%2C', ',', $path);
  if (file_exists($path)) {
    $newpath = str_replace(' ', '_', $path);
    $newpath = str_replace(',', '', $newpath);
    rename($path, $newpath);
  }
  $body = $url[0].'"'.$newname.'"'.$url[2];
  echo ($body."\n");
  
  return $body;
}

function db_set_active($db_name) { 
    $server = mysql_connect("localhost", "root", "Km91OP12");
    mysql_select_db($db_name, $server);
    return;
}
?>
