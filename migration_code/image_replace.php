<?php
db_set_active("staging");
$nodes = mysql_query("select * from field_data_body join node on field_data_body.revision_id = node.vid where node.type = 'ausmed_article' order by node.nid");
$j = 0; 
while ($node = mysql_fetch_object($nodes)) {
    $j++;
    $vid = $node->revision_id; 
    $body = $node->body_value;
    $nid = $node->nid;
    //print $nid."\n"; 

    if ($body != '') {
        $body = image_replace($body, $nid);
    }
    db_set_active('staging');
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

function image_replace($body) {
    db_set_active('ausmed');
    $subject = $body;
    $pattern = "/[[\|\]]/";
    $chars = preg_split($pattern, $subject, -1, PREG_SPLIT_NO_EMPTY);
    $count = count($chars);
    print_r ($chars);
    if ($count < 8) {
        print "\nWe have less than 8 pipe elements\n";
        return $body;
    }
    for ($i=0; $i<$count; $i++) {

        if (substr($chars[$i], 0, 3) == 'nid') {      
            $nid = substr($chars[$i], 4);      
        } 
        if (substr($chars[$i], 0, 5) == 'align') {
            $align = substr($chars[$i], 6);
        } 
        if (substr($chars[$i], 0, 5) == 'width') {
            $width = substr($chars[$i], 6);
        } 
        if (substr($chars[$i], 0, 4) == 'link') {
            $link = substr($chars[$i], 5);
        }
        if (substr($chars[$i], 0, 4) == 'desc') {
            $desc = substr($chars[$i], 5);
        }
        if (substr($chars[$i], 0, 5) == 'title') {
            $title = substr($chars[$i], 6);
        }
        if (substr($chars[$i], 0, 3) == 'url') {
            $url = substr($chars[$i], 4);
            print ("url = ".$url."\n");
        } 
         
        
        if (substr($chars[$i], 0, 6) == 'height') {
            $height = substr($chars[$i], 7);
            break;
        } 

    }
    if (!$nid) {
        return $body;
    }
    $image_paths = mysql_query("select filepath from files where nid = '".$nid."' limit 1");
    $image_path = mysql_fetch_object($image_paths);
    $image_path = $image_path->filepath;
    $image_path = str_replace('ausmed.ama.com.au/files', 'default/files/ausmed', $image_path);
    if (isset($url)) {
        $image_code ='<a href="'.$url.'">';
        $image_code .= '<img src="/'.$image_path.'" width="'.$width.'" height="'.$height.'" align="'.$align.'">';
        $image_code .= '</a>';
        $search_string = '[img_assist|nid='.$nid.'|title='.$title.'|desc='.$desc.'|link='.$link.'|url='.$url.'|align='.$align.'|width='.$width.'|height='.$height.']';
    } else {
        $image_code = '<img src="/'.$image_path.'" width="'.$width.'" height="'.$height.'" align="'.$align.'">';
        $search_string = '[img_assist|nid='.$nid.'|title='.$title.'|desc='.$desc.'|link='.$link.'|align='.$align.'|width='.$width.'|height='.$height.']';
    }
    $body = str_replace($search_string,$image_code, $body);
    unset ($nid);
    unset ($width);
    unset ($height);
    unset ($desc);
    unset ($title);
    unset ($url);
    unset ($link);
    unset ($align);

    return image_replace($body);
}
function db_set_active($db_name) { 
    $server = mysql_connect("localhost", "root", "Km91OP12");
    mysql_select_db($db_name, $server);
    return;
}

?>
