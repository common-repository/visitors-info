<?php
if(!isset($_COOKIE['vi_popup_show'])) :
$settings = get_option('vi_settings');
?>

<script type="text/javascript">
var date = new Date();
var days = parseInt(<?php echo $settings['vi_session_expire_days']; ?>);
var hours =  parseInt(<?php echo $settings['vi_session_expire_hours']; ?>);
var minutes =  parseInt(<?php echo $settings['vi_session_expire_minutes']; ?>);
var seconds =  parseInt(<?php echo $settings['vi_session_expire_seconds']; ?>);
date.setTime(date.getTime() + (((days*24*60*60)+(hours*60*60)+(minutes*60)+seconds) * 1000));

jQuery(document).ready(function(){
	var fullH = jQuery(window).height();
	var popH = jQuery("#vi_popupBox").height();
	var popupTop = ((fullH - popH)/2)-40;
	jQuery("#vi_popupBox").css({"margin-top":popupTop+"px","display":"block"});
	jQuery('body').addClass('noscroll');
	
	jQuery(".vi-txtFields").bind('click focus', function(){
		jQuery(this).parent().find('span').fadeOut('slow');
	})
	
	jQuery('#vi-entersite').click(function(){
		var error = false;
		var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
		
		var name = jQuery.trim(jQuery("#vi-name").val());
		var email = jQuery.trim(jQuery("#vi-email").val());
		var city = "";
		var state = "";
		
		if(jQuery("#vi-city").length>0) {
			city = jQuery.trim(jQuery("#vi-city").val());
		}
		if(jQuery("#vi-state").length>0) {
			state = jQuery.trim(jQuery("#vi-state").val());
		}

		var captchaArr = jQuery("#vi-captchaDiv > div > b").html().split(" ");
		var captchaStr = parseInt(captchaArr[0]) + parseInt(captchaArr[2]);
		var captchaAns = jQuery.trim(jQuery("#vi-captchaAns").val());
		
		jQuery(".vi-txtFields").each(function(){
			if(jQuery.trim(jQuery(this).val())=="") {
				jQuery(this).parent().find('span').fadeIn('slow');
				error = true;
			}
		})

		if (!emailReg.test(email)){
			jQuery("#vi-emailDiv span").fadeIn('slow');
			error = true;
		}
		
		if (!error){
			if (captchaAns == ""){
				jQuery("#vi-captchaDiv span").fadeIn('slow').html("Required");
				error = true;
			} else if (captchaAns != captchaStr){
				jQuery("#vi-captchaDiv span").fadeIn('slow').html("Wrong");
				error = true;
			}
		}
		
		if (!error){
			jQuery("#vi_popup").fadeOut('slow',function(){
				jQuery("#vi_popup").remove();
			})
	
			jQuery.ajax({
				url: "<?php echo get_bloginfo('url'); ?>/wp-admin/admin-ajax.php",
				data: {'action':'vi_add_visitor_action','name':name, 'email':email, 'city':city, 'state':state},
				type: "POST",
				success: function(response){
					//alert(response);
					if(jQuery.trim(response) > 0){
						jQuery.cookie('vi_popup_show',true,{expires: date});
					}
				}
			});
		}

	});

});

</script>

<div id="vi_popup">
<div id="vi_popupBox">
	<div id="vi-popupContent">
    <?php echo $_COOKIE['vi_popup_show']; ?>
		<h2>Welcome to the <?php echo bloginfo('name'); ?> Network!</h2>
		<h4>Please provide the information below to gain access...</h4>
		<div id="vi-nameDiv">
			<input type="text" id="vi-name" class="vi-txtFields" placeholder="firstname..." />
			<span>Enter name</span>
		</div>
		<div id="vi-emailDiv">
			<input type="text" id="vi-email" class="vi-txtFields" placeholder="email address..." />
			<span>Enter email</span>
		</div>

		<?php if($settings['city']) { ?>
		<div id="vi-cityDiv">
			<input type="text" id="vi-city" class="vi-txtFields" placeholder="city..." />
			<span>Enter city</span>
		</div>
		<?php } ?>

		<?php if($settings['state']) { ?>
		<div id="vi-stateDiv">
			<input type="text" id="vi-state" class="vi-txtFields" placeholder="state..." />
			<span>Enter state</span>
		</div>
		<?php } ?>
		
		<div id="vi-captchaDiv">
			<div><?php mathCaptcha(); ?></div>
			<input type="text" id="vi-captchaAns" class="vi-txtFields" />
			<span></span>
		</div>

		<div id="vi-note">
			<strong>NOTE:</strong>
			We absolutely will not share your information with anyone.
		</div>
		<div>
			<input type="button" id="vi-entersite" value="Enter" />
		</div>
		
	</div>
</div>
</div>
<?php endif; ?>