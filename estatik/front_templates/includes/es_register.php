<?php $es_settings = es_front_settings(); ?>
<div id="es_content" class="clearfix">
	<?php if ( !is_user_logged_in()) { ?>
    
    <div class="es_prop_managemnt">
        <?php  
        $es_register = 1;
        include('agent_files/es_add_new_agent.php'); ?>
    </div>
 
    <?php } else {
        if ( is_user_logged_in()) { ?>
        	<div class="es_not_found">
                <p><?php _e("You are already logged in.", 'es-plugin'); ?> <?php if ( is_user_logged_in()) { ?><a class="esLogOut" href="<?php echo wp_logout_url(get_option('home')); ?>"><?php _e("logout", 'es-plugin'); ?></a><?php } ?></p>
            </div>
        
    	<?php }
		
	}?>
    
    <?php if($es_settings->powered_by_link==1) { ?>
        <div class="es_powered_by
">
            <p><?php _e("Powered by", 'es-plugin'); ?> <a href="http://www.estatik.net" target="_blank">Estatik</a></p>
        </div>    
    <?php } ?>
    
</div>