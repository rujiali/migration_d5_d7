<?php
//MySQL globals
$mysql_server = "localhost";//change this server for your Drupal installations
$mysql_user = "root";//Ideally, should be root
$mysql_pass = "Km91OP12";//Corresponding password
$conn = mysqli_connect($mysql_server,$mysql_user,$mysql_pass, 'staging');//MySQL connection string
mysqli_set_charset($conn, "utf8");

$query = 'select field_data_field_file.field_file_fid from staging.field_data_field_file join staging.node on field_data_field_file.entity_id = node.nid where node.type != "media" and node.type != "audio" and node.type != "image"';
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_object($result)) {
  $fid = $row->field_file_fid;
  $path_query = "select uri from staging.file_managed where fid = '$fid'";
  $path_result = mysqli_query($conn, $path_query);
  $path_row = mysqli_fetch_object($path_result);
  $path = $path_row->uri;
  $pri_path = str_replace('sites/default/files/', '/var/www/staging.ama.com.au-files/', $path);
  $element = explode("/", $pri_path);
  $nid = $element[count($element) - 2];
  if (!file_exists('/var/www/staging.ama.com.au-files/node/'.$nid)) {
    mkdir("/var/www/staging.ama.com.au-files/node/".$nid);
  }
  $phy_path = '/var/www/dev.ama.com.au/'.$path;
  if (!file_exists($phy_path)) {
    //$phy_path = str_replace('…', '?', $phy_path);
    echo $phy_path."\n";
    echo file_exists($phy_path) ? "TRUE\n" : "FALSE\n";
  }
  copy($phy_path, $pri_path);
  chmod($pri_path, 777);
  $pri_path = str_replace('sites/default/files/', 'private://', $path);
  //echo $pri_path."\n";
  mysqli_query($conn, "update staging.file_managed set uri = '$pri_path' where fid = '$fid'"); 
}  
echo "finished as Josh said";
?>
