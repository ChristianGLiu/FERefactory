<?php
global $wpdb;
$es_settings = es_front_settings();
$upload_dir = wp_upload_dir();
$current_user = wp_get_current_user();
if(isset($_POST['agent_name'])){
    $agent_id			 = sanitize_text_field($_POST['agent_id']);
    $agent_name 		 = sanitize_text_field($_POST['agent_name']);
    $agent_name 		 = stripcslashes($agent_name);
    $agent_user_name 	 = sanitize_text_field($_POST['agent_user_name']);
    $agent_email		 = sanitize_email($_POST['agent_email']);
    $agent_company 		 = sanitize_text_field($_POST['agent_company']);
    $agent_tel 			 = sanitize_text_field($_POST['agent_tel']);
    $agent_web 			 = sanitize_text_field($_POST['agent_web']);
    $agent_about 		 = $_POST['agent_about'];
    $agent_about 		 = stripcslashes($agent_about);
    $agent_pic 			 = $_FILES['agent_pic'];
    $agent_pic_hidden   = $_POST['agent_pic_hidden'];
    if(!empty($agent_pic['name'])){
        require_once(PATH_DIR. 'admin_template/wideimage/WideImage.php');
        if ($agent_pic["error"] == 0){
            $image_name = time()."_".$agent_pic['name'];
            $sourcePath = $agent_pic['tmp_name'];
            $targetPath = $upload_dir['path']."/".$image_name;
            move_uploaded_file($sourcePath,$targetPath);
            es_crop($targetPath,$upload_dir['path']."/agent_".$image_name,$es_settings->agents_width, $es_settings->agents_height);
            @unlink($upload_dir['basedir'].$agent_pic_hidden);
            $agent_image_name = explode("/",$agent_pic_hidden);
            $agent_image_name = end($agent_image_name);
            $agent_image_path = str_replace($agent_image_name,"",$agent_pic_hidden);
            $agent_image = $agent_image_path.'agent_'.$agent_image_name;
            @unlink($upload_dir['basedir'].$agent_image);
        }
        $agent_pic_val = $upload_dir['subdir']."/".$image_name;
    }
    if(empty($agent_pic['name'])){
        $agent_pic_val = $agent_pic_hidden;
    }
    $agent_meta 	= (isset($_POST['agent_meta']))?$_POST['agent_meta']:'';
    if(!empty($agent_meta)){
        $agent_meta = serialize($agent_meta);
    }
    if(@$current_user->roles[0] !='agent_role'){
        $user_pass = wp_generate_password();
        $user_data = array(
            'ID' => '',
            'user_pass' => $user_pass,
            'user_login' => $agent_user_name,
            'user_email' => $agent_email,
            'role' => 'agent_role' // Use default role or another role, e.g. 'editor'
        );
        $user_id = wp_insert_user( $user_data );
        $user = new WP_User($user_id);
        $user_login = stripslashes(@$user->user_login);
        $user_email = stripslashes(@$user->user_email);
        $message  = sprintf(__('New user registration on your blog %s:'), get_option('blogname')) . "\r\n\r\n";
        $message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
        $message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";
        @wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), get_option('blogname')), $message);
        $message_user  = __('Hi there,') . "\r\n\r\n";
        $message_user .= sprintf(__("Welcome to %s! Here's how to log in:"), get_option('blogname')) . "\r\n\r\n";
        $message_user .= wp_login_url() . "\r\n";
        $message_user .= sprintf(__('Username: %s'), $user_login) . "\r\n";
        $message_user .= sprintf(__('Password: %s'), $user_pass) . "\r\n\r\n";
        $message_user .= sprintf(__('If you have any problems, please contact me at %s.'), get_option('admin_email')) . "\r\n\r\n";
        $message_user .= __('Estatik Team!');
        @wp_mail($user_email, sprintf(__('[%s] Your username and password'), get_option('blogname')), $message_user);
        /*'agent_rating' 			=> $agent_rating,
        'agent_prop_quantity' 	=> $agent_prop_quantity,
        'agent_sold_prop' 		=> $agent_sold_prop, */
        global $wpdb;
        $wpdb->insert(
            $wpdb->prefix.'estatik_agents',
            array(
                'agent_id' 				=> $user_id,
                'agent_name' 			=> $agent_name,
                'agent_user_name' 		=> $agent_user_name,
                'agent_email' 			=> $agent_email,
                'agent_company' 	  	=> $agent_company,
                'agent_tel' 			=> $agent_tel,
                'agent_web' 			=> $agent_web,
                'agent_about' 			=> $agent_about,
                'agent_pic' 			=> $agent_pic_val,
                'agent_meta' 			=> $agent_meta
            )
        );
        $agent_id = $wpdb->insert_id;
    }else{
        $wpdb->update(
            $wpdb->prefix.'estatik_agents',
            array(
                'agent_name' 			=> $agent_name,
                'agent_user_name' 		=> $agent_user_name,
                'agent_email' 			=> $agent_email,
                'agent_company' 	  	=> $agent_company,
                'agent_tel' 			=> $agent_tel,
                'agent_web' 			=> $agent_web,
                'agent_about' 			=> $agent_about,
                'agent_pic' 			=> $agent_pic_val,
                'agent_meta' 			=> $agent_meta
            ),
            array( 'agent_id' => $agent_id )
        );
        $user_data = array(
            'ID' => $agent_id,
            'user_email' => $agent_email,
            'role' => 'agent_role' // Use default role or another role, e.g. 'editor'
        );
        wp_update_user( $user_data );
        $wpdb->update($wpdb->users, array('user_login' => $agent_user_name), array('ID' => $agent_id));
    }
}
if(isset($_POST['es_agent_save']) && isset($es_register) && $es_register==1){
    echo '<p style="text-align:center">'.__( "You are registered to our website. Your username and password Emailed to you.", "es-plugin" ).'</p>';
    ?>
<?php } else { ?>
    <form action="" id="es_agent_form" method="post" enctype="multipart/form-data" onsubmit="return es_agent_submit();">
        <div class="es_wrapper">
            <input type="hidden" value="<?php _e( "Email is required.", "es-plugin" ); ?>" id="agentEmailRequired"  />
            <input type="hidden" value="<?php _e( "Email is not valid.", "es-plugin" ); ?>" id="agentEmailNotValid"  />
            <input type="hidden" value="<?php _e( "This email is already exist. Please choose another one.", "es-plugin" ); ?>" id="agentEmailExist"  />
            <input type="hidden" value="<?php _e( "User name is required.", "es-plugin" ); ?>" id="agentUserNameRequired"  />
            <input type="hidden" value="<?php _e( "This user name is already registered. Please choose another one.", "es-plugin" ); ?>" id="agentUserNameExist"  />
            <div class="esHead clearFix">
                <h1 class="es_mainHeading"><?php the_title(); ?></h1>
                <?php if(isset($current_user->roles[0]) && $current_user->roles[0] =='agent_role' ){ ?>
                    <p><?php _e( "Please edit your agent information below and click save to finish.", "es-plugin" ); ?></p>
                    <a class="es_LogOut" href="<?php echo wp_logout_url(home_url())?>"><?php _e( "Logout", "es-plugin" ); ?></a>
                    <input type="submit" value="<?php _e( "Save", "es-plugin" ); ?>"  name="es_agent_save" />
                <?php } else{ ?>
                    <input type="submit" value="<?php _e( "Register", "es-plugin" ); ?>"  name="es_agent_save" />
                <?php } ?>
            </div>
            <?php if(isset($_POST['agent_name'])) { ?>
                <div class="es_success" style="margin-bottom:0px;"><?php _e( "Yout profile has been updated.", "es-plugin" ); ?></div>
            <?php } ?>
            <div class="es_content_in">
                <div class="es_agent_info">
                    <?php
                    $edit_agent = "";
                    if(isset($current_user->ID)){
                        $agentid = $current_user->ID;
                        $sql = 'SELECT * FROM '.$wpdb->prefix.'estatik_agents WHERE agent_id = "'.$agentid.'"';
                        $edit_agent = $wpdb->get_row($sql);
                    }
                    ?>
                    <div id="es_agent_info_in">
                        <input type="hidden" name="agent_id" id="agent_id" value="<?php echo (!empty($edit_agent))?$edit_agent->agent_id:'' ?>"  />
                        <div class="es_agent_field clearFix">
                            <label><?php _e( "Name", "es-plugin" ); ?>:</label>
                            <input type="text" value="<?php echo (!empty($edit_agent))?$edit_agent->agent_name:'' ?>" name="agent_name" />
                        </div>
                        <div class="es_agent_field clearFix">
                            <label><?php _e( "User name", "es-plugin" ); ?>:</label>
                            <input id="agent_user_name" onblur="uservalidate(this)" type="text" value="<?php echo (!empty($edit_agent))?$edit_agent->agent_user_name:'' ?>" name="agent_user_name" />
                            <div class="es_agent_error" id="user_name_error"></div>
                            <small class="es_agent_loader" id="es_user_name_loader"></small>
                        </div>
                        <div class="es_agent_field clearFix">
                            <label><?php _e( "Email", "es-plugin" ); ?>:</label>
                            <input type="text" id="agent_email" onblur="emailvalidate(this)" value="<?php echo (!empty($edit_agent))?$edit_agent->agent_email:'' ?>" name="agent_email" />
                            <div class="es_agent_error" id="email_error"></div>
                            <small class="es_agent_loader" id="es_email_loader"></small>
                        </div>
                        <div class="es_agent_field clearFix">
                            <label><?php _e( "Company", "es-plugin" ); ?>:</label>
                            <input type="text" value="<?php echo (!empty($edit_agent))?$edit_agent->agent_company:'' ?>" name="agent_company" />
                        </div>
                        <div class="es_agent_field clearFix">
                            <label><?php _e( "Tel", "es-plugin" ); ?>:</label>
                            <input type="text" value="<?php echo (!empty($edit_agent) && $edit_agent->agent_tel!=0)?$edit_agent->agent_tel:'' ?>" name="agent_tel" />
                        </div>
                        <div class="es_agent_field clearFix">
                            <label><?php _e( "www", "es-plugin" ); ?>:</label>
                            <input type="text" value="<?php echo (!empty($edit_agent))?$edit_agent->agent_web:'' ?>" name="agent_web" />
                        </div>
                        <?php $subscripion_id = es_get_user_subscription( get_current_user_id() ); ?>
                        <?php if ( $subscripion_id && es_is_enabled_subscription() ) : $subscription = get_post( $subscripion_id ); ?>
                            <div class="es_agent_field clearFix">
                                <label><?php _e( "Subscription", "es-plugin" ); ?>:</label>
                                <input type="text" name="__subscription" value="<?php echo $subscription->post_title; ?>" disabled/>
                                <?php if ( es_get_subscription_table_page() ) : ?>
                                    <i>
                                        <?php if ( es_is_user_subscription_end( get_current_user_id() ) ) : ?>
                                            <a href="<?php echo get_permalink( es_get_subscription_table_page() ); ?>">(<?php _e( 'Renew subscription', 'es_plugin' ); ?>)</a>
                                        <?php else : ?>
                                            <a href="<?php echo get_permalink( es_get_subscription_table_page() ); ?>">(<?php _e( 'Change subscription', 'es_plugin' ); ?>)</a>
                                        <?php endif; ?>
                                    </i>
                                <?php endif; ?>
                            </div>
                        <?php elseif ( es_is_enabled_subscription() && es_get_subscription_table_page() ) : ?>
                            <div class="es_agent_field clearFix">
                                <label><?php _e( "Subscription", "es-plugin" ); ?>:</label>
                                <a href="<?php echo get_permalink( es_get_subscription_table_page() ); ?>"><?php _e( 'Add new subscription', 'es-plugin' ); ?></a>
                            </div>
                        <?php endif; ?>
                        <div class="es_agent_field clearFix">
                            <label><?php _e( "About", "es-plugin" ); ?>:</label>
                            <textarea name="agent_about"><?php echo (!empty($edit_agent))?$edit_agent->agent_about:'' ?></textarea>
                        </div>
                        <div class="es_agent_field clearFix">
                            <label><?php _e( "Photo/Logo", "es-plugin" ); ?>:</label>
                            <div class="es_agent_photo">
                                <?php
                                if(!empty($edit_agent)){
                                    $image_name = explode("/",$edit_agent->agent_pic);
                                    $image_name = end($image_name);
                                    $image_path = str_replace($image_name,"",$edit_agent->agent_pic);
                                    $latest_image = $image_path.'agent_'.$image_name;
                                }
                                ?>
                                <img src="<?php if(!empty($edit_agent) && $edit_agent->agent_pic!=''){ echo $upload_dir['baseurl'].$latest_image; } else { echo DIR_URL.'admin_template/images/es_agent_pic.jpg'; }?>" alt="#" />
                                <span>
                                    <?php _e( "Upload photo", "es-plugin" ); ?>
                                    <input type="file" value="" name="agent_pic" onchange="es_agent_pic_url(this)" />
                                    <input type="hidden" name="agent_pic_hidden" value="<?php echo (!empty($edit_agent))?$edit_agent->agent_pic:'' ?>"  />
                                </span>
                            </div>
                        </div>
                        <?php
                        $agent_meta ="";
                        if(!empty($edit_agent)){
                            $agent_meta_ser = $edit_agent->agent_meta;
                            $agent_meta = unserialize($agent_meta_ser);
                        }
                        if(!empty($agent_meta)){
                            foreach( $agent_meta as $key=>$val ) {
                                $key_val = str_replace("'","",$key);
                                ?>
                                <div class="es_agent_field clearFix">
                                    <label><?php echo $key_val?></label>
                                    <input type="text" value="<?php echo $val?>" name="agent_meta['<?php echo $key_val?>']" />
                                    <a class="field_del" onclick="es_field_del(this)" href="javascript:void(0)"></a>
                                </div>
                            <?php 		}
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php } ?>