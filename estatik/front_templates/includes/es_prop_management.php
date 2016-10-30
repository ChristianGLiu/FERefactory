<?php $es_settings = es_front_settings(); ?>
<div id="es_content" class="clearfix">
<?php $current_user = wp_get_current_user();
if ( is_user_logged_in()  && $current_user->roles[0] =='agent_role' ) { ?>
<div class="es_prop_managemnt">
    
    <?php if ( es_is_enabled_subscription() && ! es_is_user_subscription_end( $current_user ) ) :?>
        <?php if(isset($_GET['add_new_prop']) || isset($_GET['prop_id'])) { ?>
            <?php  include('agent_files/es_add_new_property.php'); ?>
        <?php } else if(isset($_GET['import_csv']))  { ?>
            <?php  include('agent_files/es_import_csv_property.php'); ?>
        <?php } else { ?>
            <?php  include('agent_files/es_my_listings.php'); ?>
        <?php } ?>
    <?php elseif ( ! es_is_enabled_subscription() ) : ?>
        <?php if(isset($_GET['add_new_prop']) || isset($_GET['prop_id'])) { ?>
            <?php  include('agent_files/es_add_new_property.php'); ?>
        <?php } else if(isset($_GET['import_csv']))  { ?>
            <?php  include('agent_files/es_import_csv_property.php'); ?>
        <?php } else { ?>
            <?php  include('agent_files/es_my_listings.php'); ?>
        <?php } ?>   
    <?php else : ?>
        <div class="es_not_found">
    	<p>
            <?php _e( 'You are not subscribed for view this page. You need to select your subscription.' ); ?></br>
            <?php if ( es_get_subscription_table_page() ) : ?>
                <a href="<?php echo get_permalink( es_get_subscription_table_page() ); ?>"><?php _e( 'Add new subscription', 'es-plugin' ); ?></a>
            <?php endif; ?>
        </p>
        </div>
    <?php endif; ?>
    
</div>
<?php }
else { ?>
    <div class="es_not_found">
    	<p>
            <?php _e("You are not Agent.", 'es-plugin'); ?> 
            <?php _e("Please", 'es-plugin'); ?> 
            <a href="<?php echo es_get_url_by_shortcode('[es_register]'); ?>" >
                <?php _e('register', 'es-plugin'); ?>
            </a> 
            <!-- <a href="<?php echo home_url()?>/register/"><?php _e("register", 'es-plugin'); ?></a>  -->
            <?php _e("and", 'es-plugin'); ?>
            <a href="<?php echo es_get_url_by_shortcode('[es_login]');?>"><?php _e('log in', 'es-plugin') ?></a>
            <?php _e('as Agent to submit your properties.', 'es-plugin') ?>
            <!-- <a href="<?php echo home_url()?>/login/">log in</a> as Agent to submit your properties.  -->
            <?php if ( is_user_logged_in()) { ?>
                <a  class="esLogOut" href="<?php echo wp_logout_url(get_option('home')); ?>">
                    <?php _e("logout", 'es-plugin'); ?>
                </a>
            <?php } ?>
        </p>
    </div>
<?php } ?>
	<?php if($es_settings->powered_by_link==1) { ?>
        <div class="es_powered_by">
            <p><?php _e("Powered by", 'es-plugin'); ?> <a href="http://www.estatik.net" target="_blank">Estatik</a></p>
        </div>    
    <?php } ?>
</div>