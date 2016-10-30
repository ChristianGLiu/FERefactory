<div id="es_content">
<?php
$es_settings = es_front_settings();
if ( is_user_logged_in() ) { ?>
    
    <div class="es_not_found">
    	<?php global $current_user; ?>
    
        <h1><?php echo $current_user->user_login?> <?php _e("you are logged in", 'es-plugin'); ?>.</h1>
        <p><a class="esLogOut" href="<?php echo wp_logout_url(get_option('home')); ?>"><?php _e("logout", 'es-plugin'); ?></a></p>
    </div>
 
<?php } else { ?>
	
	<div class="es_login">
        <h1><?php _e("Log in into your account", 'es-plugin'); ?></h1>
        <div class="es_login_in">
            <form action="<?php echo get_option('home'); ?>/wp-login.php" method="post">
                <label><?php _e("Username", 'es-plugin'); ?></label>
                <input type="text" name="log" id="log" placeholder="<?php _e("Username", 'es-plugin'); ?>" value="" size="20" />
                <label><?php _e("Password", 'es-plugin'); ?></label>
                <input type="password" name="pwd" placeholder="<?php _e("Password", 'es-plugin'); ?>" id="pwd" size="20" />
                <input type="submit" name="submit" value="<?php _e("Login", 'es-plugin'); ?>" />
                <input type="hidden" value="<?php echo es_get_url_by_shortcode('[es_prop_management]')?>" name="redirect_to">
            </form>
            <p><a href="<?php echo add_query_arg( 'action', 'lostpassword', get_option('home').'/wp-login.php');?>"><?php _e("I forgot my password", 'es-plugin'); ?></a></p>
            <p><a href="<?php echo get_option('home')?>/register/"><?php _e("I need to register for a new account", 'es-plugin'); ?></a></p>
        </div>
    </div>
<?php }?>
	<?php if($es_settings->powered_by_link==1) { ?>
        <div class="es_powered_by
">
            <p><?php _e("Powered by", 'es-plugin'); ?> <a href="http://www.estatik.net" target="_blank">Estatik</a></p>
        </div>    
    <?php } ?>
	
</div>