<?php	
require_once("captcha/captcha_generator.php");
$captcha = new CaptchaCode();
$code = str_encrypt($captcha->generateCode(6));

if(is_singular('properties')) {
	
	global $wpdb;
	$sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}estatik_properties WHERE prop_id='%d' 
							ORDER BY prop_id DESC", get_the_ID());
	$es_prop_single = $wpdb->get_row($sql); 
	 
	$prop_agent = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}estatik_agents 
									WHERE agent_id='{$es_prop_single->agent_id}'");
	if(isset($_POST['learn_more_about_send'])){
    
            $your_name 	= $_POST['your_name'];
            $your_email	 = $_POST['your_email'];
            $your_phone = $_POST['your_phone'];
            $request_message = $_POST['request_message'];

            if($send_message_to=="agent"){
                    $to  = $prop_agent->agent_email;
            } else if($send_message_to=="admin"){
                    $to  = get_option('admin_email');
            } else if($send_message_to=="admin_agent"){
                    $to  = $prop_agent->agent_email;
                    $cc  = 'Cc: '.get_option('admin_email'). "\r\n";
            }

            $subject = __("Estatik Request Info from", 'es-plugin');
            $message = __("Name", 'es-plugin').": $your_name\r\n";
            $message.= __("Email", 'es-plugin').": $your_email \r\n";
            $message.= __("Phone", 'es-plugin').": $your_phone\r\n";
            $message.= __("Property ID", 'es-plugin').": $es_prop_single->prop_id\r\n";
            $message.= __("Property Address", 'es-plugin').": $es_prop_single->prop_address\r\n";
            $message.= __("Request", 'es-plugin').": $request_message";
            $headers = 'From: '.$your_email. "\r\n" .
                    $cc .
                    'Reply-To: '.$your_email. "\r\n" .
                    'X-Mailer: PHP/' . phpversion();

            mail($to, $subject, $message, $headers);
	}
?>
    <div id="es_request_form">
       
       <?php if(isset($_POST['learn_more_about_send'])){ ?>
       
        <div id="es_request_form_popup" class="es_request_info_popup" style="display: block;">
            <div class="es_request_info_popup_overlay"></div>
            <div class="es_request_info_popup_in">
                <h4><?php _e("Your message was sent", 'es-plugin'); ?></h4>
                <p><?php _e("Thank you for your message! We will contact you as soon as we can.", 'es-plugin'); ?></p>
                <a class="es_close_pop" href="javascript:void(0)"></a>
            </div>
        </div>
        
        <?php } ?>
       
        <div class="es_learn_aboot_prop">
            <h3><?php echo $request_title?></h3>
            <input type="hidden" value="<?php _e("Email is required.", 'es-plugin'); ?>" id="enterYourEmail" />
            <input type="hidden" value="<?php _e("Email not valid.", 'es-plugin'); ?>" id="notValidYourEmail" />
            <input type="hidden" value="<?php _e("Please Enter the security code.", 'es-plugin'); ?>" id="enterSecurityCode" />
            <input type="hidden" value="<?php _e("Incorrect Code Entered.", 'es-plugin'); ?>" id="incorrectCodeEntered" />
            <form action="" id="es_request_form" method="post">
            	<label><?php _e("Your name", 'es-plugin'); ?></label>
                <input type="text" name="your_name" placeholder="<?php _e("Your name", 'es-plugin'); ?>" />
            	<label><?php _e("Your email", 'es-plugin'); ?></label>
                <input type="text" name="your_email" placeholder="<?php _e("Your email", 'es-plugin'); ?>" />
            	<label><?php _e("Phone number", 'es-plugin'); ?></label>
                <input type="text" name="your_phone" placeholder="<?php _e("Phone number", 'es-plugin'); ?>" />
            	<label><?php _e("Request message", 'es-plugin'); ?></label>
                <textarea name="request_message"><?php echo $request_message?></textarea>
                <div><img src="<?php echo DIR_URL;?>/front_templates/widgets/captcha/captcha_images.php?width=150&height=50&code=<?php echo $code?>" /></div>
                <input id="captcha_code" name="captcha_code" type="text" />
                <div id="request_form_error" class="es_error" style="display:none;"></div>
                <input type="submit" value="<?php _e("Send", 'es-plugin'); ?>" name="learn_more_about_send" />
		<input type="hidden" name="captcha_check" value="<?php echo str_decrypt($code)?>" />
            </form>
        </div>
    </div>
<?php } ?>