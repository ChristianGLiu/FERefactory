<?php
// Set Default currency for Paypal.
DEFINE( 'ES_DEFAULT_CURRENCY', 'USD' );
DEFINE( 'ES_PAYPAL_STANDARD_URL', 'https://www.paypal.com/cgi-bin/webscr' );
// Start Session.
if ( ! session_id() ) {
    session_start();
}
// Include composer extensions.
require_once( __DIR__ . '/../../vendor/autoload.php' );
// Activation / Deactivation hooks for scheduler event.
register_activation_hook( __FILE__, 'es_set_expired_subscription_mail_schedule' );
register_deactivation_hook( __FILE__, 'es_remove_expired_subscription_mail_schedule' );
// Use paypal.
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\Amount;
use PayPal\Api\PaymentExecution;
/**
 * Return all subscription periods. 
 * 
 * Duration is num of seconds in our period. 8600 = 24 * 60 * 60 - seconds in one day.
 * Date T - num of days in current year. Date L - return 1 If year is leap.
 * 
 * @return array
 */
function es_get_subscription_periods() {
    return apply_filters( 'es_get_subscription_periods', array(
        'week'  => array( 'title' => __( 'Week',  'es-plugin' ), 'duration' => 7 * 86400 ),
        'month' => array( 'title' => __( 'Month', 'es-plugin' ), 'duration' => date( 't' ) * 86400 ),
        'year'  => array( 'title' => __( 'Year',  'es-plugin' ), 'duration' => date( 'L' ) ? 366 * 86400 : 365 * 86400 ),
    ) );
}
/**
 * Save or update subscription handler.
 * 
 * @return void
 */
function es_save_subscription() {
    $action = filter_input(INPUT_POST, 'es_subscription_action');
    
    // Validate handler.
    if ( ! isset( $action ) && isset( $action ) ) {
        return null;
    }
    
    $name                    = trim(filter_input( INPUT_POST, 'name' ));
    $price                   = filter_input( INPUT_POST, 'price', FILTER_VALIDATE_FLOAT );
    $renewal_price           = filter_input( INPUT_POST, 'renewal_price', FILTER_VALIDATE_FLOAT );
    $properties_num          = filter_input( INPUT_POST, 'properties_num', FILTER_VALIDATE_INT );
    $featured_properties_num = filter_input( INPUT_POST, 'featured_properties_num', FILTER_VALIDATE_INT );
    $subscription_period     = filter_input( INPUT_POST, 'subscription_period' );
    $description             = filter_input( INPUT_POST, 'es_description' );
    $id                      = filter_input( INPUT_POST, 'es_subscription_id' );
    
    if ( empty( $name ) || $price < 0 || ! array_key_exists( $subscription_period, es_get_subscription_periods() ) ) {
        return null;
    }
    
    $postarr = array(
        'post_title' => $name,
        'post_type' => 'es_subscription',
        'post_status' => 'private',
        'post_author' => get_current_user_id(),
        'post_content' => $description,
    );
    
    if ( ! empty( $id ) ) {
        $postarr['ID'] = $id;
    }
    
    // Insert / Update new subscription.
    $post_id = wp_insert_post( $postarr );
    
    if ( $post_id ) {
        update_post_meta( $post_id, 'es_price', $price );
        update_post_meta( $post_id, 'es_renewal_price', $renewal_price );
        update_post_meta( $post_id, 'es_properties_num', $properties_num );
        update_post_meta( $post_id, 'es_featured_properties_num', $featured_properties_num );
        update_post_meta( $post_id, 'es_subscription_period', $subscription_period );
        
        if ( $action == 'saveclose' ) {
            wp_redirect( 'admin.php?page=es_my_subscriptions' );
            exit; // For valid work of wp_redirect.
        } else {
            wp_redirect( 'admin.php?page=es_add_new_subscription&es_subscription_id=' . $post_id );
            exit; // For valid work of wp_redirect.
        }
    }
    
    wp_redirect( 'admin.php?page=es_my_subscriptions' );
    exit; // For valid work of wp_redirect.
}
add_action( 'init', 'es_save_subscription' );
/**
 * Return list of all subscriptions.
 * 
 * @return array
 */
function es_get_subscriptions() {
    return get_posts(array(
        'post_status' => 'private',
        'post_type' => 'es_subscription',
        'posts_per_page' => -1, // All subscriptions
        'order' => 'ASC',
    ));
}
/**
 * Delete Subscription by GET parameter.
 * 
 * @return void
 */
function es_delete_subscription_handler() {
    $id = filter_input(INPUT_GET, 'es_subscription_delete_id');
    if ( ! empty( $id ) ) {
        wp_delete_post( $id, true );
        wp_redirect( 'admin.php?page=es_my_subscriptions' );
        exit;
    }
}
add_action( 'init', 'es_delete_subscription_handler' );
/**
 * Insert Default subscription on hook activate plugin.
 * 
 * @return void
 */
function es_create_default_subscriptions() {
    $default_subscriptions = es_get_default_subscriptions();
    if ( ! empty( $default_subscriptions ) && is_array( $default_subscriptions ) ) {
        global $wpdb;
        foreach ( $default_subscriptions as $key => $subscription ) {
            $post = $wpdb->get_var( "SELECT ID FROM " . $wpdb->posts . " WHERE post_type='es_subscription' AND post_name='" . $key . "' LIMIT 1" );
            if ( ! $post ) {
                $post_id = wp_insert_post( array(
                    'post_title' => $subscription['name'],
                    'post_type' => 'es_subscription',
                    'post_status' => 'private',
                ) );
                if ( $post_id ) {
                    update_post_meta( $post_id, 'es_price', $subscription['price'] );
                    update_post_meta( $post_id, 'es_properties_num', $subscription['properties_num'] );
                    update_post_meta( $post_id, 'es_featured_properties_num', $subscription['featured_properties_num'] );
                    update_post_meta( $post_id, 'es_subscription_period', $subscription['period'] );
                    update_post_meta( $post_id, 'es_renewal_price', $subscription['renewal_price'] );
                }
            }
        }
    }
}
/**
 * Return default subscriptions for insertation on activation hook.
 * 
 * @return array
 */
function es_get_default_subscriptions() {
    return apply_filters('es_get_default_subscriptions', array(
        'basic' => array( 'name' => 'Basic', 'price' => 10, 'featured_properties_num' => 2, 'properties_num' => 5, 'period' => 'week', 'renewal_price' => '10' ),
        'plus' => array( 'name' => 'Plus', 'price' => 50, 'featured_properties_num' => 5, 'properties_num' => 10, 'period' => 'month', 'renewal_price' => '30' ),
        'deluxe' => array( 'name' => 'Deluxe', 'price' => 150, 'featured_properties_num' => 10, 'properties_num' => 30, 'period' => 'year', 'renewal_price' => '100' ),
    ));
}
/**
 * Save subscription options from General Settings page.
 * 
 * @return void
 */
function es_save_subscription_settings() {
    $action = filter_input( INPUT_POST, 'es_settings_submit' );
    if ( ! empty( $action ) ) {
        update_option( 'es_paypal_email', filter_input( INPUT_POST, 'paypal_email' ) );
        update_option( 'es_enable_subscription', filter_input( INPUT_POST, 'enable_subscription' ) );
        update_option( 'es_listing_publishing', filter_input( INPUT_POST, 'listing_publishing' ) );
        update_option( 'es_register_page', filter_input( INPUT_POST, 'es_register_page' ) );
        update_option( 'es_subscription_table_page', filter_input( INPUT_POST, 'es_subscription_table_page' ) );
        update_option( 'es_get_expired_subscription_body', filter_input( INPUT_POST, 'es_get_expired_subscription_body' ) );
        update_option( 'es_get_expired_subscription_subject', filter_input( INPUT_POST, 'es_get_expired_subscription_subject' ) );
        update_option( 'es_paypal_key', filter_input( INPUT_POST, 'es_paypal_key' ) );
        update_option( 'es_paypal_secret', filter_input( INPUT_POST, 'es_paypal_secret' ) );
        update_option( 'es_paypal_sandbox_key', filter_input( INPUT_POST, 'es_paypal_sandbox_key' ) );
        update_option( 'es_paypal_sandbox_secret', filter_input( INPUT_POST, 'es_paypal_sandbox_secret' ) );
        update_option( 'es_paypal_mode', filter_input( INPUT_POST, 'es_paypal_mode' ) );
        update_option( 'es_paypal_type', filter_input( INPUT_POST, 'es_paypal_type' ) );
        update_option( 'es_currency_sign_place', filter_input( INPUT_POST, 'es_currency_sign_place' ) );
        update_option( 'es_currency', filter_input( INPUT_POST, 'es_currency' ) );
        update_option( 'es_manage_page', filter_input( INPUT_POST, 'es_manage_page' ) );
    }
}
add_action( 'init', 'es_save_subscription_settings' );
/**
 * Return ID of manage listings page.
 *
 * @return mixed|void
 */
function es_get_manage_page() {
    return get_option('es_manage_page');
}
/**
 * Return subscription status code (0, 1).
 * 
 * @return string
 */
function es_is_enabled_subscription() {
    return get_option( 'es_enable_subscription', 0 );
}
/**
 * Return publishing type (1 - Automatic, 0 - Manual).
 * 
 * @return string
 */
function es_listing_publishing_type() {
    return get_option( 'es_listing_publishing', 0 );
}
/**
 * Return paypal email if exists.
 * 
 * @return string
 */
function es_get_paypal_email() {
    return get_option( 'es_paypal_email' );
}
/**
 * Return Subscription HTML Table.
 *
 * @param array $atts
 * @return string
 */
function es_subscription_table( $atts = array() ) {
    // If subscription option is enabled.
    if ( es_is_enabled_subscription() ) {
    $subscriptions = es_get_subscriptions();
    $checked = !empty( $atts['checked'] ) ? $atts['checked'] : 0;
    $current_subscription = es_get_user_subscription( get_current_user_id() );
    $err = filter_input( INPUT_GET, 'es_err' );
    $success = filter_input( INPUT_GET, 'es_payment' );
    $currency = es_get_currency();
    $currencies_list = es_get_currencies();
    $currency_pos = es_currency_position();
    if ( !empty( $subscriptions ) && is_array( $subscriptions ) ) : ob_start(); ?>
        <?php if ( $err == 'paypal_proceed' ) : ?>
            <div class="es-error">
                <?php _e( 'Something were wrong with paypal checkout. Please support admin of the site with this problem.', 'es-plugin' ); ?>
            </div>
        <?php elseif( $success == 'paypal_success' ) : ?>
            <div class="es-success">
                <?php _e( 'Order successfully created. Wait for paypal approve.', 'es-plugin' ); ?>
            </div>
        <?php endif; ?>
        <div class="es-subscription-table-wrapper">
            <?php if (es_get_paypal_type() == 'express') : ?>
                <form method="post" action="">
            <?php endif; ?>
            <table class="es-subscription-table" border="0">
                <thead>
                    <tr>
                        <th width="5%"></th>
                        <th width="22%"><?php _e( 'Name', 'es-plugin' ); ?></th>
                        <th width="16%"><?php _e( 'All listings', 'es-plugin' ); ?></th>
                        <th width="20%"><?php _e( 'Featured listings', 'es-plugin' ); ?></th>
                        <th width="12%"><?php _e( 'Price', 'es-plugin' ); ?></th>
                        <th><?php _e( 'Renewal price', 'es-plugin' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $subscriptions as $subscription ) :
                        $price = get_post_meta( $subscription->ID , 'es_price', true );
                        $renewal_price = get_post_meta( $subscription->ID , 'es_renewal_price', true );
                        $next_price = es_get_user_subscription_next_price( get_current_user_id(), $subscription->ID );
                        if ( ! $next_price && ! es_is_user_subscription_end( get_current_user_id() ) ) {
                            $disabled = 'disabled';
                        } else {
                            $disabled = '';
                        }
                        ?>
                        <tr>
                            <td colspan="6">
                                <table class="es-table-inner" border="0">
                                    <tr class="es-fields">
                                        <td width="5%">
                                            <label>
                                                <input <?php echo $disabled; ?> required type="radio" name="subscription" data-price="<?php echo es_get_formatted_price(es_get_user_subscription_next_price( get_current_user_id(), $subscription->ID )); ?>" value="<?php echo $subscription->ID; ?>" <?php checked( $checked, $subscription->ID ); ?>/>
                                            </label>
                                        </td>
                                        <td class="es-title" width="22%">
                                            <b><?php echo $subscription->post_title; ?></b>
                                            <?php if ( $current_subscription == $subscription->ID ) : ?>
                                                <?php if (es_is_user_subscription_end( get_current_user_id() ) ) : ?>
                                                    <i>(<?php _e( 'Subscription expired', 'es-plugin' ); ?>)</i>
                                                <?php else : ?>
                                                    <i>(<?php _e( 'Already in use', 'es-plugin' ); ?>)</i>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td width="20%"><?php echo get_post_meta( $subscription->ID , 'es_properties_num', true ); ?></td>
                                        <td width="16%"><?php echo get_post_meta( $subscription->ID , 'es_featured_properties_num', true ); ?></td>
                                        <td width="12%">
                                            <?php if ( $price ) : ?>
                                                <?php echo es_get_formatted_price( $price ); ?>
                                            <?php else : ?>
                                                <?php _e( 'Free', 'es-plugin' ); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <b>
                                                <?php if ( $renewal_price ) : ?>
                                                    <?php echo es_get_formatted_price( $renewal_price ); ?>
                                                <?php else : ?>
                                                    <?php _e( 'Free', 'es-plugin' ); ?>
                                                <?php endif; ?>
                                            </b>
                                        </td>
                                    </tr>
                                    <tr class="es-description">
                                        <td width="5%"></td>
                                        <td colspan="5"><?php echo $subscription->post_content; ?></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td width="5%"></td>
                        <td colspan="2"><span class="es-table-total"><?php _e( 'Total', 'es-plugin' ); ?>: <span class="es-table-total-value"></</span></td>
                        <td colspan="3" class="es-proceed-td">
                            <?php if ( get_current_user_id() ) : ?>
                                <?php if ( es_get_paypal_type() == 'standard' ) : ?>
                                    <?php $_SESSION['paypal_standard_hash'] = md5(time()); ?>
                                    <?php foreach( $subscriptions as $subscription ) : ?>
                                        <form action="<?php echo ( es_get_user_subscription_next_price( get_current_user_id(), $subscription->ID ) ) ? es_get_paypal_standard_url() : ''; ?>" class="es_paypal_standard_form es_paypal_standard_form_<?php echo $subscription->ID; ?>" method="POST">
                                            <input type="hidden" name="business" value="<?php echo es_get_paypal_email(); ?>"/>
                                            <input type="hidden" name="cmd" value="_xclick"/>
                                            <input type="hidden" name="item_name" value="<?php echo $subscription->post_title; ?>"/>
                                            <input type="hidden" name="item_number" value="<?php echo $subscription->ID; ?>"/>
                                            <input type="hidden" name="amount" value="<?php echo es_get_user_subscription_next_price( get_current_user_id(), $subscription->ID ); ?>"/>
                                            <input type="hidden" name="currency_code" value="<?php echo $currency; ?>"/>
                                            <input type="hidden" name="quantity" value="1"/>
                                            <input type="hidden" name="rm" value="2"/>
                                            <input type="hidden" name="return" value="<?php echo add_query_arg(array(
                                                'es_paypal_standard' => 'success',
                                                'hash' => $_SESSION['paypal_standard_hash'],
                                                'item_id' => $subscription->ID
                                            ), get_permalink( es_get_subscription_table_page() ) ); ?>"/>
                                            <input type="hidden" name="cancel_return" value="<?php echo add_query_arg( 'es_err', 'paypal_proceed',
                                                get_permalink( es_get_subscription_table_page() ) ); ?>"/>
                                            <input type="hidden" name="no_shipping" value="1"/>
                                            <input type="submit" class="btn-lnk btn-orange" value="<?php _e( 'Proceed', 'es-plugin' ); ?>" name="es_proceed_order_handler"/>
                                        </form>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <input type="submit" class="btn-lnk btn-orange" value="<?php _e( 'Proceed', 'es-plugin' ); ?>" name="es_proceed_order_handler"/>
                                <?php endif; ?>
                            <?php elseif( $subscription_table_page = es_get_register_page() ) : ?>
                                <a href="<?php echo get_permalink( $subscription_table_page ); ?>" class="btn-lnk btn-orange"><?php _e( 'Proceed', 'es-plugin' ); ?></a>
                            <?php else : ?>
                                <?php _e( 'Please select registration page in subscription settings.', 'es-plugin' ); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
            <?php if (es_get_paypal_type() == 'express') : ?>
                </form>
            <?php endif; ?>
        </div>
    <?php return ob_get_clean(); endif;
    } else {
        _e( 'Firstly, you need to enable subscription option in admin panel.' );
    }
}
add_shortcode( 'es_subscription_table', 'es_subscription_table' );
/**
 * @param $price
 * @return mixed|void
 */
function es_get_formatted_price( $price ) {
    $currencies_list = es_get_currencies();
    $currency_pos = es_currency_position();
    if ( $currency_pos == 'before' ) {
        $price_str = $currencies_list[es_get_currency()] . $price;
    } else {
        $price_str = $price . ' ' .$currencies_list[es_get_currency()];
    }
    return apply_filters( 'es_get_formatted_price', $price_str, $price, $currencies_list, $currency_pos, es_get_currency() );
}
/**
 * Handler for paypal standard after pay.
 */
function es_paypal_standard_response() {
    $es_paypal_standard = filter_input(INPUT_GET, 'es_paypal_standard');
    $item_id = filter_input(INPUT_GET, 'item_id');
    $hash = filter_input(INPUT_GET, 'hash');
    if ( $es_paypal_standard == 'success' && !empty( $item_id ) && $_SESSION['paypal_standard_hash'] == $hash ) {
        es_subscribe_user( get_current_user_id(), $item_id );
        es_insert_order( $item_id, get_current_user_id(), array(
            'status' => 'Approved'
        ) );
	    $manage_page = es_get_manage_page();
	    $table_page = es_get_subscription_table_page();
	    if ( ! empty( $manage_page ) ) {
		    wp_redirect( get_permalink( $manage_page ) );
	    } else if ( ! empty( $table_page ) ) {
		    wp_redirect( add_query_arg( 'es_payment', 'paypal_success', get_permalink( $table_page ) ) );
	    } else {
		    wp_redirect( home_url() );
	    }
	    exit;
    }
}
add_action( 'init', 'es_paypal_standard_response' );
/**
 *
 */
function es_free_subscription_handler() {
    $item_number = filter_input(INPUT_POST, 'item_number');
    $item_name = filter_input(INPUT_POST, 'item_name');
    $cmd = filter_input(INPUT_POST, 'cmd');
    if ( !empty( $item_name ) && !empty( $item_number ) && ! empty( $cmd ) ) {
        $next_price = es_get_user_subscription_next_price( get_current_user_id(), $item_number );
        if ( ! $next_price && es_is_user_subscription_end( get_current_user_id() ) ) {
            es_subscribe_user( get_current_user_id(), $item_number );
            $manage_page = es_get_manage_page();
            $table_page = es_get_subscription_table_page();
            if ( ! empty( $manage_page ) ) {
                wp_redirect( get_permalink( $manage_page ) );
            } else if ( ! empty( $table_page ) ) {
                wp_redirect( add_query_arg( 'es_payment', 'paypal_success', get_permalink( $table_page ) ) );
            } else {
                wp_redirect( home_url() );
            }
            exit;
        } else {
            wp_redirect( add_query_arg( 'es_err', 'paypal_proceed', get_permalink( es_get_subscription_table_page() ) ) );
            exit;
        }
    }
}
add_action( 'init', 'es_free_subscription_handler' );
/**
 * Return Paypal Standard URL basics on Paypal mode.
 * @return string
 */
function es_get_paypal_standard_url() {
    if (es_get_paypal_mode() == 'sandbox') {
        return "https://www.sandbox.paypal.com/cgi-bin/webscr";
    } else {
        return ES_PAYPAL_STANDARD_URL;
    }
}
/**
 * Subscribe User function.
 * 
 * @param int $user_id
 * @param int $subscription_id
 * @return boolean
 */
function es_subscribe_user( $user_id, $subscription_id ) {
    // If user and subscription exists.
    if (get_user_by( 'id' , $user_id ) && get_post_status( $subscription_id ) ) {
        $period = get_post_meta( $subscription_id, 'es_subscription_period', true );
        $periods = es_get_subscription_periods();
        $featured = get_post_meta( $subscription_id, 'es_featured_properties_num', true );
        $listings = get_post_meta( $subscription_id, 'es_properties_num', true );
        if ( !empty( $period ) && array_key_exists( $period, es_get_subscription_periods() ) ) {
            $duration = $periods[$period]['duration'];
            update_user_meta( $user_id, 'es_subscription', $subscription_id );
            update_user_meta( $user_id, 'es_subscription_start', time() );
            update_user_meta( $user_id, 'es_subscription_duration', $duration );
            update_user_meta( $user_id, 'es_subscription_period', $period );
            update_user_meta( $user_id, 'es_properties_num', $listings );
            update_user_meta( $user_id, 'es_featured_properties_num', $featured );
            update_user_meta( $user_id, 'es_is_expired_email_sended', 0 );
        }
    }
}
/**
 * Check is user subscribe ends.
 * 
 * @param int $user_id
 * @return boolean
 */
function es_is_user_subscription_end( $user_id ) {
    if ( $user_id instanceof WP_User ) {
        $user_id = $user_id->ID;
    }
    if ( get_user_by( 'id', $user_id ) ) {
        $start_date = get_user_meta( $user_id, 'es_subscription_start', true );
        $current_date = time();
        $duration = get_user_meta( $user_id, 'es_subscription_duration', true );
        
        if ( ! empty( $start_date ) && ! empty( $duration ) ) {
            if ( $current_date - $start_date > $duration ) {
                return true;
            } else {
                return false;
            }
        }
    }
    return true;
}
/**
 * Return ID of user subscription.
 * 
 * @param int $user_id
 * @return int
 */
function es_get_user_subscription( $user_id ) {
    return get_user_meta( $user_id, 'es_subscription', true );
}
/**
 * Unsubscribe User function.
 * 
 * @param int $user_id
 * @return bool
 */
function es_unsubscribe_user( $user_id ) {
    if ( get_user_by( 'id', $user_id ) ) {
        delete_user_meta( $user_id, 'es_subscription' );
        delete_user_meta( $user_id, 'es_subscription_start' );
        delete_user_meta( $user_id, 'es_subscription_duration' );
        delete_user_meta( $user_id, 'es_subscription_period' );
        delete_user_meta( $user_id, 'es_is_expired_email_sended' );
        return true;
    } 
    return false;
}
/**
 * Return page objects for selectboxes in use.
 * 
 * @return array
 */
function es_get_pages_helper() {
    return get_posts(array(
        'post_status' => 'publish',
        'post_type' => 'page',
        'posts_per_page' => -1,
    ));
}
/**
 * Return ID of register page.
 * 
 * @return int
 */
function es_get_register_page() {
    return get_option( 'es_register_page' );
}
/**
 * Return ID of subscription table page.
 * 
 * @return int
 */
function es_get_subscription_table_page() {
    return get_option( 'es_subscription_table_page' );
}
if ( ! function_exists( 'es_login_redirect' ) ) {
    
    /**
     * Redirect users with Agent role to subscription table page.
     * 
     * @param string $redirect
     * @param string $request
     * @param stdClass $user
     * @return string
     */
    function es_login_redirect( $redirect, $request, $user ) {
        if ( !empty( $user->roles ) && is_array( $user->roles ) ) {
            $is_agent_role = in_array( 'agent_role', $user->roles );
            $subscription_table_page = es_get_subscription_table_page();
            if ( $is_agent_role && get_post_status( $subscription_table_page ) == 'publish' && es_is_enabled_subscription() ) {
                return get_permalink( $subscription_table_page );
            }
        }
        
        return $redirect; //Default URL for redirect.
    }
    add_filter( 'login_redirect', 'es_login_redirect', 10, 3 );
}
/**
 * Handler for subscription table
 */
function es_proceed_order_handler() {
    $action = filter_input( INPUT_POST, 'es_proceed_order_handler' );
    if ( empty( $action ) ) {
        return false;
    }
    
    $subscription_id = filter_input( INPUT_POST, 'subscription', FILTER_SANITIZE_NUMBER_INT );
 
    if ( get_post_type( $subscription_id ) == 'es_subscription' && es_is_user_agent( get_current_user_id() ) ) {
        if ( es_get_user_subscription_next_price( get_current_user_id(), $subscription_id ) ) {
            es_make_payment( $subscription_id, get_current_user_id() );
        } else {
            es_subscribe_user( get_current_user_id(), $subscription_id );
            $manage_page = es_get_manage_page();
            $table_page = es_get_subscription_table_page();
            if ( ! empty( $manage_page ) ) {
                wp_redirect( get_permalink( $manage_page ) );
            } else if ( ! empty( $table_page ) ) {
                wp_redirect( add_query_arg( 'es_payment', 'paypal_success', get_permalink( $table_page ) ) );
            } else {
                wp_redirect( home_url() );
            }
            exit;
        }
    }
}
add_action( 'init', 'es_proceed_order_handler' );
/**
 * Check is user has agent role.
 * 
 * @param int $user_id
 * @return boolean
 */
function es_is_user_agent( $user_id ) {
    $user = get_user_by( 'id', $user_id );
    if ( !empty( $user->roles ) && is_array( $user->roles ) && ( in_array( 'agent_role', $user->roles ) || in_array( 'administrator', $user->roles ) ) ) {
        return true;
    }
    return false;
}
/**
 * Get subscription price by user.
 *
 * @param $user_id
 * @param $subscription_id
 * @return mixed
 */
function es_get_user_subscription_next_price( $user_id, $subscription_id ) {
    $current_subscription_id = es_get_user_subscription( $user_id );
    if ( $subscription_id != $current_subscription_id ) {
        return get_post_meta( $subscription_id, 'es_price', true );
    } else {
        return get_post_meta( $subscription_id, 'es_renewal_price', true );
    }
}
/**
 * Make paypal payment.
 * 
 * @param int $subscription_id
 * @param int $user_id
 */
function es_make_payment( $subscription_id, $user_id ) {
    $subscription = get_post( $subscription_id );
    $price = es_get_user_subscription_next_price( $user_id, $subscription_id );
    $page_id = es_get_subscription_table_page();
    if ( !empty( $subscription ) && es_is_user_agent( $user_id ) ) {
        // Create payer and set payment method.
        $payer = new Payer();
        $payer->setPaymentMethod( 'paypal' );
        // Create product for Paypal.
        $item = new Item();
        $item->setName( $subscription->post_title )
             ->setPrice( $price )
             ->setCurrency( es_get_currency() )
             ->setQuantity( 1 );
        // Create list of product (for transaction).
        $item_list = new ItemList();
        $item_list->addItem( $item );
        $amount = new Amount();
        $amount->setCurrency( es_get_currency() )
            ->setTotal( $price );
        // Create payment transaction.
        $transaction = new Transaction();
        $transaction->setItemList( $item_list )
            ->setAmount( $amount )
            ->setInvoiceNumber( uniqid() );
        // Create Redirect URL after pay.
        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl( add_query_arg( 'es_payment', 'paypal_success', get_permalink( $page_id ) ) )
            ->setCancelUrl( add_query_arg( 'es_err', 'paypal_proceed', get_permalink( $page_id ) ) );
        // Create Paypal payment.
        $payment = new Payment();
        $payment->setIntent( 'sale' )
            ->setPayer( $payer )
            ->setRedirectUrls( $redirectUrls )
            ->setTransactions( array( $transaction ) );
        $request = clone $payment;
        $approvalUrl = false;
        try {
            $payment->create( es_get_api_context() );
            $approvalUrl = $payment->getApprovalLink();
        } catch ( Exception $ex ) {
            if ( get_post_status( $page_id ) ) {
                wp_redirect( add_query_arg( 'es_err', 'paypal_proceed', get_permalink( $page_id ) ) );
                exit;
            } else {
                wp_redirect( home_url() );
                exit;
            }
        }
        if ( ! empty( $approvalUrl ) ) {
            $order_id = es_insert_order( $subscription_id, $user_id );
            if ( $order_id ) {
                $_SESSION['es_order']['order_id'] = $order_id;
                $_SESSION['es_order']['user_id'] = $user_id;
                $_SESSION['es_order']['subscription_id'] = $subscription_id;
                wp_redirect( $approvalUrl );
                exit;
            }
        }
        wp_redirect( add_query_arg( 'es_err', 'paypal_proceed', get_permalink( $page_id ) ) );
        exit;
    }
}
/**
 * Return paypal credentials.
 * @param string $mode
 * @return array
 */
function es_get_paypal_credentials( $mode = 'sandbox' ) {
    if ( $mode == 'sandbox' ) {
        return array(
            'clientId'   => get_option( 'es_paypal_sandbox_key' ),
            'clientSecret' => get_option( 'es_paypal_sandbox_secret' ),
        );
    } else {
        return array(
            'clientId'   => get_option( 'es_paypal_key' ),
            'clientSecret' => get_option( 'es_paypal_secret' ),
        );
    }
}
/**
 * Return paypal mode (sandbox/live)
 * @return mixed|string|void
 */
function es_get_paypal_mode() {
    $mode = get_option( 'es_paypal_mode' );
    return empty( $mode ) ? 'sandbox' : $mode;
}
/**
 * Return paypal current type (Standard | Express)
 * @return mixed|string|void
 */
function es_get_paypal_type() {
    $mode = get_option( 'es_paypal_type' );
    return empty( $mode ) ? 'standard' : $mode;
}
/**
 * Return current currency.
 * @return string
 */
function es_get_currency() {
    $code = get_option( 'es_currency' );
    return !empty( $code ) ? $code : ES_DEFAULT_CURRENCY;
}
/**
 * Return Paypal API Configs.
 *
 * @return ApiContext
 */
function es_get_api_context()
{
    $credentials = es_get_paypal_credentials( es_get_paypal_mode() );
    $apiContext = new ApiContext(
        new OAuthTokenCredential(
            ! empty( $credentials['clientId'] ) ? $credentials['clientId'] : false,
            ! empty( $credentials['clientSecret'] ) ? $credentials['clientSecret'] : false
        )
    );
    // Comment this line out and uncomment the PP_CONFIG_PATH
    // 'define' block if you want to use static file
    // based configuration
    if ( es_get_paypal_mode() == 'sandbox' ) {
        $apiContext->setConfig(
            array(
                'mode' => 'sandbox',
                'log.LogEnabled' => true,
                'log.FileName' => '../PayPal.log',
                'log.LogLevel' => 'DEBUG', // PLEASE USE `FINE` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
                'cache.enabled' => false,
                // 'http.CURLOPT_CONNECTTIMEOUT' => 30
                // 'http.headers.PayPal-Partner-Attribution-Id' => '123123123'
            )
        );
    }
    // Partner Attribution Id
    // Use this header if you are a PayPal partner. Specify a unique BN Code to receive revenue attribution.
    // To learn more or to request a BN Code, contact your Partner Manager or visit the PayPal Partner Portal
    // $apiContext->addRequestHeader('PayPal-Partner-Attribution-Id', '123123123');
    return $apiContext;
}
/**
 * Return list of active agents IDs.
 *
 * @return array
 */
function es_get_subscribed_agents_ids() {
    // Get array of user IDs.
    $users = get_users( array(
        'blog_id' => get_current_blog_id(),
        'role' => 'agent_role',
        'meta_key' => 'es_subscription',
        'fields' => array( 'id' ),
    ) );
    if ( !empty( $users ) ) {
        // Reset "id" keys.
        $users = array_map( 'reset', $users );
    }
    return apply_filters( 'es_get_subscribed_agents_ids', $users );
}
/**
 * Send emails when subscription is expired for all agents.
 *
 * @return void
 */
function es_send_subscription_end_emails() {
    // Get subscribed agents IDs.
    $agents = es_get_subscribed_agents_ids();
    if ( !empty( $agents ) ) {
        foreach ( $agents as $id ) {
            if ( es_get_user_subscription( $id ) && es_is_user_subscription_end( $id ) && ! is_subscription_expired_email_sent( $id ) ) {
                // Load user data.
                $userdata = get_userdata( $id );
                // Email data.
                $subject = es_get_expired_subscription_subject();
                $message = es_get_expired_subscription_body();
                if ( ! empty( $subject ) && !empty( $message ) ) {
                    if ( wp_mail( $userdata->data->user_email, $subject, $message ) ) {
                        update_user_meta( $id, 'es_is_expired_email_sended', 1 );
                    }
                }
            }
        }
    }
}
add_action('wp', 'es_send_subscription_end_emails');
/**
 * Return is expired subscription email sended.
 *
 * @param $user_id
 * @return bool
 */
function is_subscription_expired_email_sent( $user_id ) {
    return (bool) get_user_meta( $user_id, 'es_is_expired_email_sended', true );
}
/**
 * Return message of subscription ended email notification.
 *
 * @return mixed|void
 */
function es_get_expired_subscription_body() {
    return apply_filters( 'es_get_expired_subscription_body', get_option( 'es_get_expired_subscription_body' ) );
}
/**
 * Return subject of subscription ended email notification.
 *
 * @return mixed|void
 */
function es_get_expired_subscription_subject() {
    return apply_filters( 'es_get_expired_subscription_subject', get_option( 'es_get_expired_subscription_subject' ) );
}
/**
 * Return filtered body.
 *
 * @param $body
 * @return mixed|void
 */
function es_get_expired_subscription_body_filter( $body ) {
    if ( ! empty( $body ) ) {
        return $body;
    } else {
        return apply_filters( 'es_get_expired_subscription_body_default',
            __( 'Hello, </br>Your subscription plan period ends today. Please renew your subscription
            and click <a href="' . get_permalink( es_get_subscription_table_page() ) . '"> here >></a> to proceed. Otherwise your listings will be unpublished.
            Thank you.', 'es-plugin' ) );
    }
}
add_filter( 'es_get_expired_subscription_body', 'es_get_expired_subscription_body_filter' );
/**
 * Return filter of subject message.
 *
 * @param $subject
 * @return mixed|void
 */
function es_get_expired_subscription_subject_filter( $subject ) {
    if ( ! empty( $subject ) ) {
        return $subject;
    } else {
        return apply_filters( 'es_get_expired_subscription_subject_default', __( 'Subscription is expired.', 'es-plugin' ) );
    }
}
add_filter( 'es_get_expired_subscription_subject', 'es_get_expired_subscription_subject_filter' );
/**
 * Set headers for email as HTML.
 *
 * @param $content_type
 * @return string
 */
function es_set_email_content_type( $content_type ){
    return 'text/html';
}
add_filter( 'wp_mail_content_type', 'es_set_email_content_type' );
/**
 * Create schedule of event.
 */
function es_set_expired_subscription_mail_schedule() {
    if( ! wp_next_scheduled( 'es_set_expired_subscription_mail_schedule_event' ) ) {
        wp_schedule_event(time(), 'hourly', 'es_set_expired_subscription_mail_schedule_event');
    }
}
add_action( 'es_set_expired_subscription_mail_schedule_event', 'es_send_subscription_end_emails' );
add_action('wp', 'es_set_expired_subscription_mail_schedule');
/**
 * Remove schedule of event.
 */
function es_remove_expired_subscription_mail_schedule() {
    wp_clear_scheduled_hook('es_set_expired_subscription_mail_schedule_event');
}
/**
 * Listen for GET parameter of Paypal payment ID.
 */
function es_paypal_listen_ipn() {
    $payment_id = filter_input( INPUT_GET, 'paymentId' );
    $payer_id = filter_input( INPUT_GET, 'PayerID' );
    // If not empty paypal variables.
    if ( !empty( $payment_id ) && ! empty( $payer_id ) ) {
        $order = $_SESSION['es_order'];
        // If valid order.
        if ( $order ) {
            es_insert_order( $order['subscription_id'], $order['user_id'], array(
                'ID' => $order['order_id'],
                'payment_id' => $payment_id,
                'payer_id' => $payer_id
            ) );
            if ( es_get_subscription_table_page() ) {
                wp_redirect( add_query_arg( 'es_payment', 'paypal_success', es_get_subscription_table_page() ) );
            } else {
                wp_redirect( home_url() );
            }
            exit;
        }
    }
}
/**
 * Subscribe customer if order is approved.
 */
function es_check_orders() {
    // Get unapproved orders for preparing.
    $unapproved_orders = es_get_unapproved_orders();
    // If we have unapproved orders then preparing it.
    if ( ! empty( $unapproved_orders ) ) {
        // API Credentials and config.
        $api_context = es_get_api_context();
        // Subscription table page ID.
        $page_id = es_get_subscription_table_page();
        foreach ( $unapproved_orders as $order ) {
            // Paypal API payment ID.
            $payment_id = get_post_meta( $order->ID, 'es_payment_id', true );
            // Paypal API payer ID.
            $payer_id = get_post_meta( $order->ID, 'es_payer_id', true );
            // Product ID (subscription ID).
            $subscription_id = get_post_meta( $order->ID, 'es_subscription_id', true );
            // If correct data then load payment.
            if ( ! empty( $payer_id ) && ! empty( $payment_id ) ) {
                // Get order from the Paypal server.
                $payment = Payment::get( $payment_id, $api_context );
                $execution = new PaymentExecution();
                $execution->setPayerId( $payer_id );
                // If payment aren't approved - execute it and subscribe user if approved.
                if ( $payment->state != 'approved' ) {
                    $result = null;
                    try {
                        $result = $payment->execute( $execution, $api_context );
                        // If subscription exists and order is approved.
                        if ( ! empty( $result->state ) && $result->state == 'approved' && get_post_status( $subscription_id ) ) {
                            // Subscribe User.
                            es_subscribe_user( $order->post_author, $subscription_id );
                            // Approve order.
                            update_post_meta( $order->ID, 'es_order_status', 'Approved' );
                        }
                    } catch ( Exception $ex ) {
                        wp_redirect( add_query_arg( 'es_err', 'paypal_proceed', get_permalink( $page_id ) ) );
                        exit;
                    }
                } else {
                    if ( get_post_status( $subscription_id ) ) {
                        // Subscribe user.
                        es_subscribe_user( $order->post_author, $subscription_id );
                        // Approve order.
                        update_post_meta( $order->ID, 'es_order_status', 'Approved' );
                    } else {
                        wp_redirect( add_query_arg( 'es_err', 'paypal_proceed', get_permalink( $page_id ) ) );
                        exit;
                    }
                }
            }
        }
    }
}
add_action( 'init', 'es_check_orders' );
/**
 * Return list of unapproved orders.
 *
 * @return array
 */
function es_get_unapproved_orders() {
    return get_posts( array(
        'post_type' => 'es_order',
        'posts_per_page' => -1,
        'post_status' => 'private',
        'meta_query' => array(
            array(
                'key' => 'es_order_status',
                'value' => 'Unapproved'
            )
        )
    ) );
}
/**
 * Register new post types for subscription/order Entity.
 *
 * @return void
 */
function es_register_subscription_post_types() {
    register_post_type( 'es_subscription', array(
        'has_archive'   => false,
        'public'        => false,
    ) );
    register_post_type( 'es_order', array(
        'has_archive'   => false,
        'public'        => false,
    ) );
}
add_action( 'init', 'es_register_subscription_post_types' );
/**
 * Create order by user and subscription ID.
 *
 * @param $subscription_id
 * @param $user_id
 * @param array $options
 * @return bool|int|WP_Error
 */
function es_insert_order( $subscription_id, $user_id, array $options = array() ) {
    if ( $subscription = get_post( $subscription_id ) && es_is_user_agent( $user_id ) ) {
        // Args for post Inserting / Updating.
        $args = array(
            'post_status' => 'private',
            'post_title' => apply_filters( 'es_create_order_name', ! empty( $subscription->post_title ) ?
                $subscription->post_title : 'Subscription order ' . date( 'd-m-Y H:i:s' ) ),
            'post_type' => 'es_order',
            'post_author' => $user_id
        );
        // For updating Order post.
        if ( ! empty( $options['ID'] ) ) {
            $args['ID'] = $options['ID'];
        }
        // Creating order.
        $order_id = wp_insert_post( $args );
        if ( $order_id ) {
            $payment_id = ! empty( $options['payment_id'] ) ? $options['payment_id'] : false;
            $payer_id = ! empty( $options['payer_id'] ) ? $options['payer_id'] : false;
            // Update/Insert Payment data.
            update_post_meta( $order_id, 'es_order_price', es_get_user_subscription_next_price( $user_id, $subscription_id ) );
            update_post_meta( $order_id, 'es_order_currency', es_get_currency() );
            update_post_meta( $order_id, 'es_subscription_id', $subscription_id );
            update_post_meta( $order_id, 'es_order_status', !empty( $options['status'] ) ? $options['status'] : 'Unapproved' );
            if ( ! empty( $payer_id ) && ! empty( $payment_id ) ) {
                update_post_meta( $order_id, 'es_payment_id', $payment_id );
                update_post_meta( $order_id, 'es_payer_id', $payer_id  );
            }
            return $order_id;
        } else {
            return false;
        }
    } else {
        return false;
    }
}
/**
 * Enqueue JS scripts for subscription functionality.
 */
function es_subscription_scripts() {
    wp_register_script( 'es-subscription-admin',
        plugins_url( 'js/es_admin_subscription.js' , __FILE__ )
    );
    wp_enqueue_script( 'es-subscription-admin' );
}
add_action( 'admin_enqueue_scripts', 'es_subscription_scripts' );
/**
 * Return list of orders.
 * @return array
 */
function es_get_orders() {
    return get_posts(array(
        'post_type' => 'es_order',
        'post_status' => 'private',
        'posts_per_page' => -1
    ));
}
/**
 * Return option of automatically listing publishing check.
 * @return bool
 */
function es_is_listing_publish_automatic() {
    return (bool) get_option('es_listing_publishing', false);
}
/**
 * Enable google font.
 */
function es_add_google_fonts() {
    wp_enqueue_style( 'es_wpb-google-fonts', 'https://fonts.googleapis.com/css?family=Open+Sans&subset=latin,cyrillic', false );
}
add_action( 'wp_enqueue_scripts', 'es_add_google_fonts' );
/**
 * @return mixed|void
 */
function es_get_currencies() {
    return apply_filters( 'es_get_currencies', array(
        'USD' => __( '$', 'es-plugin' ),
        'EUR' => __( 'EUR', 'es-plugin' ),
        'GBP' => __( 'GBP', 'es-plugin' ),
        'RUB' => __( 'RUB', 'es-plugin' ),
    ) );
}
/**
 * Return currency position.
 * @return mixed|string|void
 */
function es_currency_position() {
    $pos = get_option( 'es_currency_sign_place' );
    return !empty( $pos ) ? $pos : 'before';
}