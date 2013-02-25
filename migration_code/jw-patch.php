<?php
db_set_active("staging");
$nodes = mysql_query("select * from field_data_body join node on field_data_body.revision_id = node.vid where node.type = 'audio' order by node.nid");
while ($node = mysql_fetch_object($nodes)) {
    $vid = $node->revision_id; 
    $body = $node->body_value;
    $nid = $node->nid;
    //print $nid."\n"; 

    if ($body != '') {
        $body = audio_replace($body, $nid);
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

function audio_replace($body) {
  $body = str_replace('/sites/all/libraries/jwplayer/player.swf?file=', '', $body);
  return $body;
}

function db_set_active($db_name) { 
    $server = mysql_connect("localhost", "root", "Km91OP12");
    mysql_select_db($db_name, $server);
    return;
}
?>
