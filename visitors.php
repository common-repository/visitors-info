<?php
global $wpdb;
$table = $wpdb->prefix . "visitors_info";

$query = "select * from ".$table;
$result = $wpdb->get_results($query);

$resultArr = array();
$resultArr[0] = array("ID","Name","Email Address","City","State","No of Visits","Last Visit");
foreach ($result as $key=>$fields) {
    $resultArr[$key+1]['id'] = $fields->id;
    $resultArr[$key+1]['name'] = $fields->name;
    $resultArr[$key+1]['email'] = $fields->email;
    $resultArr[$key+1]['city'] = $fields->city;
    $resultArr[$key+1]['state'] = $fields->state;
    $resultArr[$key+1]['no_of_visits'] = $fields->no_of_visits;
    $resultArr[$key+1]['last_visit'] = date('M d, Y \a\t h:i A',strtotime($fields->last_visit));
}

$filepath = plugin_dir_path(__FILE__).'export_visitors_info.csv';
$fileURL = plugin_dir_url(__FILE__).'export_visitors_info.csv';

$fp = fopen($filepath, 'w');
foreach ($resultArr as $fields) {
    fputcsv($fp, $fields);
}
fclose($fp);
?>



<div class="wrap">
    <div id="icon-visitors" class="icon32" style="background: url('<?php echo plugin_dir_url(__FILE__); ?>/images/visitors-icon-36.png') no-repeat"><br></div>
    <h2>Visitors</h2><br>

    <div style="float:left">
    <a href="<?php echo $fileURL; ?>" class="button-primary">Export to CSV</a>
    </div>

<?php

function get_table_data() {
	global $wpdb;
	
	$records = array();
	$query = "select * from `".$wpdb->prefix."visitors_info` order by last_visit DESC";
	$result = mysql_query($query);
	if( mysql_num_rows($result) > 0 ) {
		while($row = mysql_fetch_assoc($result)) {
			$records[] = $row;
		}
	}
	return $records;
}

$myListTable = new My_Example_List_Table();

$myListTable->example_data = get_table_data();
$myListTable->prepare_items(); 
$myListTable->display(); 

?>
</div>

