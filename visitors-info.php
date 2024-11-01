<?php
/*
Plugin Name: Visitors Info
Plugin URI: http://codegambler.com/my_wordpress/visitors-info.zip
Description: Plugin shows a popup for your website visitors to fill some information (i.e. name, email, city, state) on first visit. Also enables admin to review users that came to website.
Author: Angrej Kumar
Version: 1.0
Author URI: http://codegambler.com/about-me/
*/

//database tables
include("database.php");
register_activation_hook(__FILE__,'vi_create_table');

// add CSS & JS to website header
add_action('wp_enqueue_scripts', 'vi_inclusions');
function vi_inclusions(){
	wp_enqueue_script('jquery-cookie', plugin_dir_url(__FILE__).'jquery.cookie.js', array(), '1.0', true);
	wp_enqueue_style('vi-stylesheet', plugin_dir_url(__FILE__).'style.css');
}


add_action('wp_footer', 'vi_popup');
add_shortcode('vi_popup', 'vi_popup');
function vi_popup() {
	include("popup.php");
}


if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	class My_Example_List_Table extends WP_List_Table {
	
		function get_columns(){
		  $columns = array(
			'name'			=> 'Name',
			'email'			=> 'Email Address',
			'city'			=> 'City',
			'state'			=> 'State',
			'no_of_visits'	=> 'Total Visits',
			'last_visit'	=> 'Last Visit',
		  );
		  return $columns;
		}
		
		function prepare_items() {
		  $columns = $this->get_columns();
		  $hidden = array();
		  $sortable = $this->get_sortable_columns();
		  $this->_column_headers = $this->get_column_info();
	
		  usort( $this->example_data, array( &$this, 'usort_reorder' ) );
		  
		  $per_page = $this->get_items_per_page('records_per_page', 20);
		  $current_page = $this->get_pagenum();
		  $total_items = count($this->example_data);
		
		  // only ncessary because we have sample data
		  $this->found_data = array_slice($this->example_data,(($current_page-1)*$per_page),$per_page);
		
		  $this->set_pagination_args( array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page'    => $per_page                     //WE have to determine how many items to show on a page
		  ) );
		  $this->items = $this->found_data;
		}
		
		function no_items() {
			_e( 'No Visitors yet.' );
		}

  		function column_default( $item, $column_name ) {
		  switch( $column_name ) { 
			case 'name':
			case 'email':
			case 'city':
			case 'state':
			case 'no_of_visits':
			  return $item[ $column_name ];
			case 'last_visit':
			  return date('M d, Y \a\t h:i A',strtotime($item[ $column_name ]));
			default:
			  return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
		  }
		}
		
		function get_sortable_columns() {
		  $sortable_columns = array(
			'name' => array('name',false),
			'email'   => array('email',false),
			'city' => array('city',false),
			'state' => array('state',false),
			'no_of_visits' => array('no_of_visits',false),
			'last_visit' => array('last_visit',true)
		  );
		  return $sortable_columns;
		}
		
		function usort_reorder( $a, $b ) {
		  // If no sort, default to title
		  $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'last_visit';
		  // If no order, default to asc
		  $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'desc';
		  // Determine sort order
		  $result = strcmp( $a[$orderby], $b[$orderby] );
		  // Send final sort direction to usort
		  return ( $order === 'asc' ) ? $result : -$result;
		}
	}

}

/* ADMIN AREA*/
add_action('admin_menu', 'vi_admin_actions');
add_action('editor_menu', 'vi_admin_actions');

function vi_admin_actions() {  
    $hook = add_menu_page("Visitors Info", "Visitors Info", "edit_pages", "vi_visitors", "vi_visitors", plugin_dir_url(__FILE__) . 'images/visitors-icon.png');
	add_submenu_page("vi_visitors","Settings","Settings","manage_options","vi_settings","vi_settings");
	add_action( "load-$hook", 'add_vi_list_table' );
}


function add_vi_list_table() {
	global $myListTable;
  $option = 'per_page';
  $args = array(
         'label' => 'Records',
         'default' => 20,
         'option' => 'records_per_page'
         );
  add_screen_option( $option, $args );
  $myListTable = new My_Example_List_Table;
}

add_filter('set-screen-option', 'vi_screen_options', 10, 3);
function vi_screen_options($status, $option, $value) {
  return $value;
}

function vi_visitors(){
	include('visitors.php');
}

$role_object = get_role( 'editor' );
$role_object->add_cap( 'vi_visitors' );


function vi_settings(){
	include('settings.php');
}

// MATH CAPTCHA
session_start();
function mathCaptcha(){
	$first_number = mt_rand(1, 94);
	$second_number = mt_rand(1, 5);
	$_SESSION["mathCaptcha"] = ($first_number+$second_number);
	$operation = "<b>".$first_number ." + ". $second_number."</b> ?";
	echo "How much is: ".$operation;		
}

// add visitor via AJAX
function vi_add_visitor(){
	$name = $_POST['name'];
	$email = $_POST['email'];
	$city = $_POST['city'];
	$state = $_POST['state'];
	
	global  $wpdb;
	$table = $wpdb->prefix."visitors_info";
//	if($_POST['mathCaptchaAns'] == $_SESSION["mathCaptcha"]){
		$select_query = "select count(email) from ".$table." where email='".$email."'";
		$select_result = $wpdb->get_var($select_query);
		if($select_result>0){
			$query = "update ".$table." set no_of_visits=no_of_visits+1 where email='".$email."'";
		} else {
			$query = "insert into ".$table."(name,email,city,state) VALUES('".$name."','".$email."','".$city."','".$state."')";
		}
		$wpdb->get_results($query);
		echo mysql_affected_rows();
/*	} else {
		echo "wrong captcha";
	}
*/
	die();
}

add_action('wp_ajax_vi_add_visitor_action', 'vi_add_visitor');
add_action('wp_ajax_nopriv_vi_add_visitor_action', 'vi_add_visitor');


// DELETE PLUGIN WILL DELETE DATABASE TABLE
register_uninstall_hook(__FILE__, "pluginUninstall");
function pluginUninstall() {
	global  $wpdb;
	$table = $wpdb->prefix."visitors_info";
	
	$wpdb->get_results("DROP TABLE IF EXISTS ".$table);
}

?>