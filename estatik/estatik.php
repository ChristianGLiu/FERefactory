<?php
/*
Plugin Name: Estatik
Description: A full-featured Wordpress real estate plugin from Estatik.
Version: 2.4.0
Author: Estatik
Author URI: http://www.estatik.net/
License: GPL2
Text Domain: es-plugin
Domain Path: /languages/
*/
ini_set('max_execution_time', 600);
ini_set('memory_limit', '128M');
define("DIR_URL", plugins_url( '/',__FILE__));
define("PATH_DIR", plugin_dir_path( __FILE__ ));
add_action( 'admin_menu', 'register_estatik' );
function register_estatik(){
    add_menu_page( 'Estatik', 'Estatik', 'manage_options', 'es_dashboard', 'es_dashboard', DIR_URL.'admin_template/images/es_menu_icon.png', '20.7' );
    add_submenu_page( 'es_dashboard', __( "Dashboard", "es-plugin" ), __( "Dashboard", "es-plugin" ), 'manage_options', 'es_dashboard', 'es_dashboard');
    add_submenu_page( 'es_dashboard', __( "My listings", "es-plugin" ), __( "My listings", "es-plugin" ), 'manage_options', 'es_my_listings', 'es_my_listings');
    add_submenu_page( 'es_dashboard', __( "Add New Property", "es-plugin" ), __( "Add New Property", "es-plugin" ), 'manage_options', 'es_add_new_property', 'es_add_new_property');
    add_submenu_page( 'es_dashboard', __( "Import CSV Property", "es-plugin" ), __( "Import CSV Property", "es-plugin" ), 'manage_options', 'es_import_csv_property', 'es_import_csv_property');
    add_submenu_page( 'es_dashboard', __( "Data Manager", "es-plugin" ), __( "Data Manager", "es-plugin" ), 'manage_options', 'es_data_manager', 'es_data_manager');
    if (es_is_enabled_subscription()) {
        add_submenu_page( 'es_dashboard', __( "Subscriptions", "es-plugin" ), __( "Subscriptions", "es-plugin" ), 'manage_options', 'es_my_subscriptions', 'es_my_subscriptions');
        add_submenu_page( 'es_dashboard', __( "Add Subscription", "es-plugin" ), __( "Add Subscription", "es-plugin" ), 'manage_options', 'es_add_new_subscription', 'es_add_new_subscription');
        add_submenu_page( 'es_dashboard', __( "Orders", "es-plugin" ), __( "Orders", "es-plugin" ), 'manage_options', 'es_orders', 'es_orders');
    }
    add_submenu_page( 'es_dashboard', __( "Agents", "es-plugin" ), __( "Agents", "es-plugin" ), 'manage_options', 'es_agents', 'es_agents');
    add_submenu_page( 'es_dashboard', __( "Add New Agent", "es-plugin" ), __( "Add New Agent", "es-plugin" ), 'manage_options', 'es_add_new_agent', 'es_add_new_agent');
    add_submenu_page( 'es_dashboard', __( "Settings", "es-plugin" ), __( "Settings", "es-plugin" ), 'manage_options', 'es_settings', 'es_settings');
}
include("activation_deactivation_hook.php");
register_activation_hook( __FILE__, 'estatik_activate' );
/**
 * Callback add new subscription function for render page.
 *
 * @return void
 */
function es_add_new_subscription() {
    require_once('admin_template/es_subscriptions/es_add_new_subscription.php');
}
/**
 * Callback listing subscriptions function for render page.
 *
 * @return void
 */
function es_my_subscriptions() {
    require_once('admin_template/es_subscriptions/es_my_subscriptions.php');
}
function es_dashboard(){
    include("admin_template/es_dashboard.php");
}
function es_orders() {
    include("admin_template/es_orders.php");
}
function es_my_listings(){
    include("admin_template/es_property/es_my_listings.php");
}
function es_add_new_property(){
    include("admin_template/es_property/es_add_new_property.php");
}
function es_import_csv_property(){
    include("admin_template/es_property/es_import_csv_property.php");
}
function es_data_manager(){
    include("admin_template/es_manager/es_data_manager.php");
}
function es_agents(){
    include("admin_template/es_agents/es_agents.php");
}
function es_add_new_agent(){
    include("admin_template/es_agents/es_add_new_agent.php");
}
function es_settings(){
    include("admin_template/es_settings.php");
}
require_once('admin_template/es_admin_functions.php');
require_once('front_templates/es_front_functions.php');
require_once('front_templates/es_shortcodes.php');
function es_plugin_version() {
    if ( ! function_exists( 'get_plugins' ) )
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    $plugin_folder = get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
    $plugin_file = basename( ( __FILE__ ) );
    return $plugin_folder[$plugin_file]['Version'];
}
function es_load_plugin_textdomain(){
    load_plugin_textdomain( 'es-plugin', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'es_load_plugin_textdomain' );
function es_front_settings(){
    global $wpdb;
    if($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix. "estatik_settings'") == $wpdb->prefix.'estatik_settings') {
        $es_settings_data = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'estatik_settings');
        return !empty($es_settings_data[0]) ? $es_settings_data[0] : null;
    } else {
        return null;
    }
}
$es_settings = es_front_settings();
add_filter('site_transient_update_plugins', 'es_remove_update_notification');
function es_remove_update_notification($value) {
    if(is_object($value)) {
        unset($value->response[plugin_basename(__FILE__)]);
    }
    return $value;
}
function es_init_add_new_tables(){
    global $wpdb;
    $create_category_meta = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'estatik_category_meta (
                            `id` INT NOT NULL AUTO_INCREMENT,
                            `cat_id` INT NOT NULL,
                            `meta_key` VARCHAR(255) NOT NULL,
                            `meta_value` VARCHAR(255) NOT NULL,
                            PRIMARY KEY (`id`)
                        )
                        ENGINE=InnoDB
                        DEFAULT CHARSET=utf8';
    $wpdb->query($create_category_meta);
}
add_action('init', 'es_init_add_new_tables');
if ( !function_exists('es_news') ) {
    function es_news() {
        $labels = array(
            'name'               => _x( 'News', 'post type general name' ),
            'singular_name'      => _x( 'News', 'post type singular name' ),
            'add_new'            => _x( 'Add New', 'News' ),
            'add_new_item'       => __( 'Add New News' ),
            'edit_item'          => __( 'Edit News' ),
            'new_item'           => __( 'New News' ),
            'all_items'          => __( 'All News' ),
            'view_item'          => __( 'View News' ),
            'search_items'       => __( 'Search News' ),
            'not_found'          => __( 'No News found' ),
            'not_found_in_trash' => __( 'No News found in the Trash' ),
            'parent_item_colon'  => '',
            'menu_name'          => 'News'
        );
        $args = array(
            'labels'        => $labels,
            'description'   => 'Holds our products and product specific data',
            'taxonomies' => array('category', 'post_tag'),
            'public'        => true,
            'menu_position' => 5,
            'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
            'has_archive'   => true,
        );
        register_post_type( 'news', $args );
    }
    add_action( 'init', 'es_news' );
}
function get_category_list($prop_category) {
    global $wpdb;
    $categories = maybe_unserialize($prop_category);
    if ( is_array($categories) ) {
        array_walk($categories, function(&$item) { $item = "'$item'"; });
        $categories = implode(', ', $categories);
        $where = "WHERE cat_id IN ($categories)";
    } else {
        $where = "WHERE cat_id = '$categories'";
    }
    $categories = $wpdb->get_results("SELECT cat_title FROM {$wpdb->prefix}estatik_manager_categories $where");
    array_walk($categories, function(&$item) { $item = $item->cat_title; });
    $categories = implode(', ', $categories);
    return $categories;
}
if ( !function_exists('dump') ) {
    function dump($var) {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }
}
