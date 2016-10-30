<?php 
    // Subscription ID From GET Global array.
    $subscription_id = filter_input( INPUT_GET, 'es_subscription_id' );
    // Page title on Edit/Add action.
    $page_title = !empty($subscription_id) ? __( 'Update subscription', 'es-plugin' ) : __( 'Add new Subscription', 'es-plugin' );
    // Array of subscription periods.
    $periods = es_get_subscription_periods();
    $subscription = get_post($subscription_id);
    if ( ! empty( $subscription->ID ) ) {
        $prop_num = get_post_meta( $subscription->ID , 'es_properties_num', true );
        $prop_featured_num = get_post_meta( $subscription->ID , 'es_featured_properties_num', true );
        $price = get_post_meta( $subscription->ID , 'es_price', true );
        $renewal_price = get_post_meta( $subscription->ID , 'es_renewal_price', true );
        $prop_period = get_post_meta( $subscription->ID, 'es_subscription_period', true );
    }
?>
<form action="" method="post">
    <input type="hidden" name="es_subscription_action" value="save"/>
    <?php if ( !empty( $subscription_id ) ) : ?>
        <input type="hidden" name="es_subscription_id" value="<?php echo $subscription_id; ?>"/> 
    <?php endif; ?>
    
    <div class="es_wrapper"> 
        <div class="es_header clearFix">
            <h2><?php echo $page_title; ?></h2>
            <h3><img src="<?php echo DIR_URL.'admin_template/';?>images/estatik_pro.png" alt="#" /><small>Ver. <?php echo es_plugin_version(); ?></small></h3>
        </div>
        <div class="esHead clearFix">
            <p><?php _e( 'Please create or edit your subscription plan here. Do not forget to click on save button.', 'es-plugin' ); ?></p>
            <input type="submit" class="es save_close" value="<?php _e( "Save & Close", "es-plugin" ); ?>"  name="es_subscription_close" />
            <input type="submit" value="<?php _e( "Save", "es-plugin" ); ?>"  name="es_subscription_save" />
        </div>
        <div class="es_content_in">
            <div class="es_agent_info">
                <div class="es_settings_field clearFix">
                    <span><?php _e( "Name", "es-plugin" ); ?>:</span>
                    <input type="text" value="<?php echo !empty($subscription->post_title) ? $subscription->post_title : ''; ?>" name="name" required/>
                </div>
                <div class="es_settings_field clearFix">
                    <span><?php _e( "Number of properties", "es-plugin" ); ?>:</span>
                    <input type="number" value="<?php echo ! empty( $prop_num ) ? $prop_num : 10; ?>" name="properties_num" min="1"/>
                </div>
                <div class="es_settings_field clearFix">
                    <span><?php _e( "Featured properties", "es-plugin" ); ?>:</span>
                    <input type="number" value="<?php echo ! empty( $prop_featured_num ) ? $prop_featured_num : 5; ?>" name="featured_properties_num" min="1"/>
                </div>
                
                <?php if ( ! empty( $periods ) && is_array( $periods ) ) : ?>
                    <div class="es_settings_field clearFix">
                        <span><?php _e( "Subscription period", "es-plugin" ); ?>:</span>
                        <select name="subscription_period">
                            <?php foreach( $periods as $slug => $period ): ?>
                                <option value="<?php echo $slug; ?>" <?php selected( $prop_period, $slug ); ?>><?php echo $period['title']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
                
                <div class="es_settings_field clearFix">
                    <span><?php _e( "Price", "es-plugin" ); ?>:</span>
                    <input type="text" value="<?php echo $price; ?>" name="price"/>
                </div>
                
                <div class="es_settings_field clearFix">
                    <span><?php _e( "Renewal Price", "es-plugin" ); ?>:</span>
                    <input type="text" value="<?php echo $renewal_price; ?>" name="renewal_price"/>
                </div>
                <div class="es_settings_field clearFix">
                    <span><?php _e( "Description", "es-plugin" ); ?>:</span>
                    <textarea name="es_description"><?php echo ! empty( $subscription->post_content ) ? $subscription->post_content : ''; ?></textarea>
                </div>
            </div>
        </div>
    </div>
</form>     