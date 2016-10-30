<?php $subscriptions = es_get_subscriptions(); ?>
<div class="es_wrapper"> 
    
    <input type="hidden" value="<?php _e( "Are you sure you want to delete it?", "es-plugin" );  ?>" id="sureToDelete"  />
    
    <div class="es_alert_popup" id="sure_popup">
    	<div class="es_alert_popup_overlay"></div>
        <div class="es_alert_popup_in boxSizing">
        	<p></p>
            <ul>
            	<li><a class="es_ok" href="javascript:void(0)"><?php _e( "OK", "es-plugin" ); ?></a></li>
                <li><a class="es_cancel" href="javascript:void(0)"><?php _e( "Cancel", "es-plugin" ); ?></a></li>
            </ul>
            <a href="javascript:void(0)" class="es_close_popup"></a>
        </div>
    </div>
    
    <div class="es_header clearFix">
        <h2><?php _e( "Subscriptions", "es-plugin" ); ?></h2>
        <h3><img src="<?php echo DIR_URL.'admin_template/';?>images/estatik_pro.png" alt="#" /><small>Ver. <?php echo es_plugin_version(); ?></small></h3>
    </div>
    <div class="esHead clearFix">
        <p>
            <?php _e( 'Here you can create plans that will meet your website requirements and be most profitable for you.', 'es-plugin' ); ?>
        </p>
    </div>
    
    <div class="es_content_in clearFix" id="es_agent_listing">
        <div class="es_all_listing_head clearFix">
        	<div></div>
            <div style="width:50%; text-align: left; margin-left: 20px;"><?php _e( "Name", "es-plugin" ); ?></div>
            <div style="width:14%;"><?php _e( "Properties q-ty", "es-plugin" ); ?></div>
            <div style="width:14%;"><?php _e( "Featured", "es-plugin" ); ?></div>
            <div style="width:14%;"><?php _e( "Price, $", "es-plugin" ); ?></div>
        </div>
        <div class="es_all_listing clearFix">
            <?php if ( ! empty( $subscriptions ) ) : ?>
                <ul>
                    <?php foreach ( $subscriptions as $key => $subscription ) : ?>
                        <li class="clearFix">
                            <div><p><?php echo $key +1; ?></p></div>
                            <div style="width:50%; text-align: left; margin-left: 20px;"><p><?php echo $subscription->post_title; ?></p></div>
                            <div style="width:14%;"><p><?php echo get_post_meta( $subscription->ID, 'es_properties_num', true ); ?></p></div>
                            <div style="width:14%;"><p><?php echo get_post_meta( $subscription->ID, 'es_featured_properties_num', true ); ?></p></div>
                            <div style="width:14%;"><p><?php echo get_post_meta( $subscription->ID, 'es_price', true ); ?></p></div>
                            <span class="es_list_edit_del">
                                <a href="admin.php?page=es_add_new_subscription&es_subscription_id=<?php echo $subscription->ID; ?>"><?php _e( "Edit", "es-plugin" ); ?></a>
                                <a href="admin.php?page=es_my_subscriptions&es_subscription_delete_id=<?php echo $subscription->ID; ?>"><?php _e( "Delete", "es-plugin" ); ?></a>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
            <ul>
                <li class="es_no_record"><?php _e( 'No record Found.', 'es-plugin' ); ?></li>
            </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
