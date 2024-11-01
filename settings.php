<?php
if(isset($_POST['submit'])){
	$new_settings[] = array();
	$new_settings['city'] = isset($_POST['vi_city_field'])?true:false;
	$new_settings['state'] = isset($_POST['vi_state_field'])?true:false;
	
	$new_settings['vi_session_expire_days'] = $_POST['vi_session_expire_days'];
	$new_settings['vi_session_expire_hours'] = $_POST['vi_session_expire_hours'];
	$new_settings['vi_session_expire_minutes'] = $_POST['vi_session_expire_minutes'];
	$new_settings['vi_session_expire_seconds'] = $_POST['vi_session_expire_seconds'];

	update_option('vi_settings', $new_settings);
}

$settings = get_option('vi_settings');

function print_select_list($id, $from, $to, $default, $value) {
	$output = '<select name="'.$id.'">';
	$output.= '<option value="0">'.$default.'</option>';
	for( $i=$from; $i<=$to; $i++ ) {
		$output.= '<option value="'.$i.'"'.(($i==$value)?' selected="selected"':'').'>'.$i.'</option>';
	}
	$output.= '</select>';
	echo $output;
}

?>

<div class="wrap">
<div id="icon-visitors" class="icon32" style="background: url('<?php echo get_bloginfo('url'); ?>/wp-admin/images/icons32.png?ver=20121105') no-repeat -492px -5px"><br></div>
<h2>Visitors Info - Settings</h2><br>

<div class="metabox-holder">
	<div class="postbox">
		<h3 class="hndle"><span>How to implement</span></h3>
		<div class="inside">
		<p>Its works automatically after activation. In any case if its not working then please follow one of the following steps:</p>
		<p>
			<strong>PHP Function:</strong> You can call this anywhere in the wordpress in any PHP file.<br />
			<cite>&lt;?php if(function_exists('vi_popup')) vi_popup(); ?&gt;</cite>
		</p>
		<p>
			<strong>Shortcode:</strong> Use this in posts, pages and widgets.<br />
			<cite>[vi_popup]</cite>
		</p>
		</div>
	</div>

	<div class="postbox">
		<h3 class="hndle"><span>Enable/Disable Fields</span></h3>
		<div class="inside">
			<form name="vi_edit_settings" method="POST" enctype="multipart/form-data">
			<p>
				<label for="vi_session_expire_field">Session Expires after: 
                <?php
				print_select_list('vi_session_expire_days',0,10,'Days',$settings['vi_session_expire_days']);
				print_select_list('vi_session_expire_hours',0,23,'Hours',$settings['vi_session_expire_hours']);
				print_select_list('vi_session_expire_minutes',0,59,'Minutes',$settings['vi_session_expire_minutes']);
				print_select_list('vi_session_expire_seconds',0,59,'Seconds',$settings['vi_session_expire_seconds']);
				?>
			</p><br />
			<p>
				<label for="vi_city_field">
				<input name="vi_city_field" type="checkbox" id="vi_city_field" <?php if($settings['city']==1) echo 'checked="checked"'; ?>> City Field</label>
			</p>
			<p>
				<label for="vi_state_field">
				<input name="vi_state_field" type="checkbox" id="vi_state_field" <?php if($settings['state']==1) echo 'checked="checked"'; ?>> State Field</label>
			</p>
			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes">
			</p>
			</form>
		</div>
	</div>
</div>

</div>

