<?php $orders = es_get_orders(); ?>
<div class="es_wrapper">
    <div class="es_header clearFix">
        <h2><?php _e( "Orders", "es-plugin" ); ?></h2>
        <h3><img src="<?php echo DIR_URL.'admin_template/';?>images/estatik_pro.png" alt="#" /><small>Ver. <?php echo es_plugin_version(); ?></small></h3>
    </div>
    <div class="es_content_in clearFix" id="es_agent_listing">
        <div class="es_all_listing_head clearFix">
            <div></div>
            <div style="width:30%; text-align: left; margin-left: 20px;"><?php _e( "Name", "es-plugin" ); ?></div>
            <div style="width:14%;"><?php _e( "Order status", "es-plugin" ); ?></div>
            <div style="width:14%;"><?php _e( "Subscription", "es-plugin" ); ?></div>
            <div style="width:14%;"><?php _e( "Price, $", "es-plugin" ); ?></div>
            <div style="width:14%;"><?php _e( "Agent", "es-plugin" ); ?></div>
        </div>
        <div class="es_all_listing clearFix">
            <?php if ( ! empty( $orders ) ) : ?>
                <ul>
                    <?php foreach ( $orders as $key => $order ) :
                        $status = get_post_meta($order->ID, 'es_order_status', true);
                        $currency_meta = get_post_meta($order->ID, 'es_order_currency', true);
                        $currency = !empty($currency_meta) ? get_post_meta($order->ID, 'es_order_currency', true) : ES_DEFAULT_CURRENCY;
                        $subscription = get_post(get_post_meta($order->ID, 'es_subscription_id', true));
                        $agent = get_userdata( $order->post_author ); ?>
                        <li class="clearFix">
                            <div><p><?php echo $key +1; ?></p></div>
                            <div style="width:30%; text-align: left; margin-left: 20px;"><p><?php echo $order->post_title; ?></p></div>
                            <div style="width:14%;"><p><?php echo $status == 'Approved' ? '<span style="color:green">' . $status . '</span>' : '<span style="color:red">' . $status . '</span>'; ?></p></div>
                            <div style="width:14%;"><p>
                                <?php if ( ! empty( $subscription->post_title ) ): ?>
                                    <a href="admin.php?page=es_add_new_subscription&es_subscription_id=<?php echo $subscription->ID; ?>"><?php echo $subscription->post_title; ?></a>
                                <?php else: ?>
                                <?php endif; ?></p>
                            </div>
                            <div style="width:14%;"><p><?php echo get_post_meta($order->ID, 'es_order_price', true) . ' ' . $currency; ?></p></div>
                            <div style="width:14%;"><p>
                                <a target="_blank" href="admin.php?page=es_add_new_agent&agent_id=<?php echo $order->post_author; ?>">
                                    <?php echo $agent->user_email; ?>
                                </a>
                                </p>
                            </div>
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
