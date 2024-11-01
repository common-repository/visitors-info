<?php
//Database table versions
global $vi_db_table_version;
$vi_db_table_version = "1.0";

//Create dive table
function vi_create_table(){
    //Get the table name with the WP database prefix
    global $wpdb;
    $table_name = $wpdb->prefix . "visitors_info";

    global $vi_db_table_version;
    $installed_ver = get_option("vi_db_table_version");
     //Check if the table already exists and if the table is up to date, if not create it
    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name || $installed_ver != $vi_db_table_version ) {
        $sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (
			`id` bigint(20) NOT NULL AUTO_INCREMENT,
			`name` varchar(100) NOT NULL,
			`email` varchar(100) NOT NULL,
			`city` varchar(100) DEFAULT NULL,
			`state` varchar(100) DEFAULT NULL,
			`no_of_visits` bigint(20) DEFAULT '1',
			`last_visit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`)
            );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        update_option( "vi_db_table_version", $vi_db_table_version );
	}
    //Add database table versions to options
    add_option("vi_db_table_version", $vi_db_table_version);
	

	//aditional information about settings page
	$settings['city'] = true;
	$settings['state'] = true;
	$settings['vi_session_expire_days'] = 1;
	$settings['vi_session_expire_hours'] = 1;
	$settings['vi_session_expire_minutes'] = 1;
	$settings['vi_session_expire_seconds'] = 1;
	update_option('vi_settings',$settings);
}

?>