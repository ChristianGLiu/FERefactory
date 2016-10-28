<?php
/****
 * Helper file for handling action and deaction of plugins.
 * On activation we have to create database tables and on deactivation we to drops the created tables
 */
function estatik_activate() {
    create_estatik_tables();
    add_role('agent_role', 'Agent', array('read' => true));
    $menu_name = 'view_first';
    $menu_exists = wp_get_nav_menu_object($menu_name);
    if (!$menu_exists) {
        wp_create_nav_menu($menu_name);
    }
    if ( function_exists( 'es_create_default_subscriptions' ) ) {
        es_create_default_subscriptions();
    }
}
if ( !function_exists('create_or_update') ) {
    function create_or_update($table, $fields, $primary) {
        global $wpdb;
        $db_name = DB_NAME;
        $fields_to_create = '';
        $fields_to_update = '';
        foreach ( $fields as $name => $format ) {
            $fields_to_create[] = "`$name` $format";
        }
        $fields_to_create = implode(', ', $fields_to_create);
        $wpdb->query("CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}$table` ($fields_to_create, PRIMARY KEY (`$primary`))
						ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");
        foreach ( $fields as $name => $format ) {
            $count = $wpdb->get_var("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
								        WHERE  table_name = '{$wpdb->prefix}$table'
								        AND table_schema = '$db_name'
								        AND column_name = '$name'");
            if ( !empty($count) ) continue;
            $wpdb->query("ALTER TABLE `{$wpdb->prefix}$table` ADD `$name` $format;");
        }
    }
}
function insert_default($setting_name, $values) {
    global $wpdb;
    $table = "{$wpdb->prefix}estatik_manager_$setting_name";
    $rows_number = $wpdb->get_row("SELECT COUNT(*) AS count FROM $table")->count;
    if ( $rows_number > 0 ) {
        return;
    }
    foreach ( $values as $value ) {
        $wpdb->insert($table, array( "{$setting_name}_title" => $value ));
    }
}
function create_estatik_tables()
{
    global $wpdb;
    create_or_update('estatik_settings', array(
        "address" => "int(1) NOT NULL DEFAULT '1'",
        "agent" => "int(1) NOT NULL DEFAULT '1'",
        "agents_height" => "int(11) NOT NULL DEFAULT '350'",
        "agents_width" => "int(11) NOT NULL DEFAULT '265'",
        "currency_sign_place" => "varchar(255) NOT NULL DEFAULT 'after'",
        "date_format" => "varchar(255) NOT NULL DEFAULT 'd/m/y'",
        "default_currency" => "varchar(255) NOT NULL DEFAULT 'USD,$'",
        "facebook_link" => "varchar(255) NOT NULL DEFAULT '1'",
        "google_plus_link" => "varchar(255) NOT NULL DEFAULT '1'",
        "date_added" => "int(1) NOT NULL DEFAULT '1'",
        "labels" => "int(1) NOT NULL DEFAULT '1'",
        "linkedin_link" => "varchar(255) NOT NULL DEFAULT '1'",
        "listing_layout" => "char(20) NOT NULL DEFAULT 'list'",
        "no_of_listing" => "int(11) NOT NULL DEFAULT '3'",
        "pdf_player" => "varchar(255) NOT NULL DEFAULT '1'",
        "powered_by_link" => "int(11) NOT NULL DEFAULT '1'",
        "price" => "int(1) NOT NULL DEFAULT '1'",
        "price_format" => "varchar(255) NOT NULL DEFAULT '2|.|,'",
        "prop_listview_2column_height" => "int(11) NOT NULL DEFAULT '220'",
        "prop_listview_2column_width" => "int(11) NOT NULL DEFAULT '370'",
        "prop_listview_list_height" => "int(11) NOT NULL",
        "prop_listview_list_width" => "int(11) NOT NULL",
        "prop_listview_table_height" => "int(11) NOT NULL DEFAULT '150'",
        "prop_listview_table_width" => "int(11) NOT NULL DEFAULT '260'",
        "prop_singleview_photo_center_height" => "int(11) NOT NULL DEFAULT '285'",
        "prop_singleview_photo_center_width" => "int(11) NOT NULL DEFAULT '800'",
        "prop_singleview_photo_lr_height" => "int(11) NOT NULL DEFAULT '285'",
        "prop_singleview_photo_lr_width" => "int(11) NOT NULL DEFAULT '500'",
        "prop_singleview_photo_thumb_height" => "int(11) NOT NULL DEFAULT '48'",
        "prop_singleview_photo_thumb_width" => "int(11) NOT NULL DEFAULT '84'",
        "resize_method" => "varchar(255) NOT NULL DEFAULT 'crop'",
        "single_property_layout" => "int(11) NOT NULL DEFAULT '3'",
        "theme_style" => "varchar(255) NOT NULL DEFAULT 'light'",
        "property_slug" => "varchar(255)",
        "title" => "int(1) NOT NULL DEFAULT '1'",
        "twitter_link" => "varchar(255) NOT NULL DEFAULT '1'",
        "view_first_on_off" => "int(11) NOT NULL DEFAULT '1'",
        "setting_id" => "int(11) NOT NULL AUTO_INCREMENT",
    ), 'setting_id');
    $date_format = $wpdb->get_var("SELECT date_format FROM {$wpdb->prefix}estatik_settings
		WHERE `setting_id`='1'");
    if ( empty($date_format) ) {
        $wpdb->query("UPDATE {$wpdb->prefix}estatik_settings SET `date_format`='d/m/y'
			WHERE `setting_id`='1'");
    }
    $property_slug = $wpdb->get_var("SELECT property_slug FROM {$wpdb->prefix}estatik_settings
		WHERE `setting_id`='1'");
    if ( empty($property_slug) ) {
        $wpdb->query("UPDATE {$wpdb->prefix}estatik_settings SET `property_slug`='properties'
			WHERE `setting_id`='1'");
    }
    $theme_style = $wpdb->get_var("SELECT theme_style FROM {$wpdb->prefix}estatik_settings
		WHERE `setting_id`='1'");
    if ( empty($theme_style) ) {
        $wpdb->query("UPDATE {$wpdb->prefix}estatik_settings SET `theme_style`='light'
			WHERE `setting_id`='1'");
    }
    $view_first_on_off = $wpdb->get_var("SELECT view_first_on_off FROM {$wpdb->prefix}estatik_settings
		WHERE `setting_id`='1'");
    if ( empty($view_first_on_off) ) {
        $wpdb->query("UPDATE {$wpdb->prefix}estatik_settings SET `view_first_on_off`='1'
			WHERE `setting_id`='1'");
    }
    $wpdb->query("ALTER TABLE {$wpdb->prefix}estatik_settings 
    	CHANGE `listing_layout` `listing_layout` char(20) NOT NULL");
    create_or_update('estatik_agents', array(
        'agent_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'agent_name' => 'varchar(255) NOT NULL',
        'agent_user_name' => 'varchar(255) NOT NULL',
        'agent_email' => 'varchar(255) NOT NULL',
        'agent_company' => 'varchar(255) NOT NULL',
        'agent_prop_quantity' => 'int(11) NOT NULL',
        'agent_sold_prop' => 'int(11) NOT NULL',
        'agent_tel' => 'char(20) NOT NULL',
        'agent_web' => 'varchar(255) NOT NULL',
        'agent_rating' => 'varchar(255) NOT NULL',
        'agent_about' => 'TEXT NOT NULL',
        'agent_pic' => 'varchar(255) NOT NULL',
        'agent_meta' => 'varchar(255) NOT NULL',
        'agent_status' => 'int(11) NOT NULL',
    ), 'agent_id');
    $wpdb->query("ALTER TABLE {$wpdb->prefix}estatik_agents 
    	CHANGE `agent_tel` `agent_tel` char(20) NOT NULL");
    $wpdb->query("ALTER TABLE {$wpdb->prefix}estatik_agents 
    	CHANGE `agent_about` `agent_about` TEXT NOT NULL");
    create_or_update('estatik_properties', array(
        'prop_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'agent_id' => 'int(11) NOT NULL',
        'prop_number' => 'int(11) NOT NULL',
        'prop_pub_unpub' => 'int(11) NOT NULL',
        'prop_date_added' => 'int(11) NOT NULL',
        'prop_title' => 'varchar(255) NOT NULL',
        'prop_type' => 'varchar(255) NOT NULL',
        'prop_category' => 'varchar(255) NOT NULL',
        'prop_status' => 'int(11) NOT NULL',
        'prop_featured' => 'varchar(255) NOT NULL',
        'prop_hot' => 'int(1) NOT NULL',
        'prop_open_house' => 'int(1) NOT NULL',
        'prop_foreclosure' => 'int(1) NOT NULL',
        'prop_price' => 'DECIMAL( 50, 2 ) NOT NULL ',
        'prop_period' => 'varchar(255) NOT NULL',
        'prop_bedrooms' => 'int(11) NOT NULL',
        'prop_bathrooms' => 'DECIMAL(4,1) NOT NULL',
        'prop_floors' => 'int(11) NOT NULL',
        'prop_area' => 'int(11) NOT NULL',
        'prop_lotsize' => 'int(11) NOT NULL',
        'prop_builtin' => 'varchar(255) NOT NULL',
        'prop_description' => 'text NOT NULL',
        'country_id' => 'int(11) NOT NULL',
        'state_id' => 'int(11) NOT NULL',
        'city_id' => 'int(11) NOT NULL',
        'prop_zip_postcode' => 'varchar(255) NOT NULL',
        'prop_street' => 'varchar(255) NOT NULL',
        'prop_address' => 'varchar(255) NOT NULL',
        'prop_longitude' => 'varchar(255) NOT NULL',
        'prop_latitude' => 'varchar(255) NOT NULL',
        'prop_meta_keywords' => 'varchar(255) NOT NULL',
        'prop_meta_description' => 'varchar(255) NOT NULL'
    ), 'prop_id');
    $wpdb->query("ALTER TABLE {$wpdb->prefix}estatik_properties 
    	CHANGE `prop_bathrooms` `prop_bathrooms` DECIMAL(4,1) NOT NULL");
    create_or_update('estatik_properties_meta', array(
        'prop_meta_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'prop_id' => 'int(11) NOT NULL',
        'prop_meta_key' => 'varchar(255) NOT NULL',
        'prop_meta_value' => 'TEXT NOT NULL',
    ), 'prop_meta_id');
    create_or_update('estatik_properties_neighboarhood', array(
        'prop_neigh_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'neigh_id' => 'int(11) NOT NULL',
        'neigh_distance' => 'varchar(255) NOT NULL',
        'prop_id' => 'int(11) NOT NULL',
    ), 'prop_neigh_id');
    create_or_update('estatik_properties_features', array(
        'prop_feature_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'feature_id' => 'int(11) NOT NULL',
        'prop_id' => 'int(11) NOT NULL',
    ), 'prop_feature_id');
    create_or_update('estatik_properties_appliances', array(
        'prop_app_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'appliance_id' => 'int(11) NOT NULL',
        'prop_id' => 'int(11) NOT NULL',
    ), 'prop_app_id');
    create_or_update('estatik_manager_appliances', array(
        'appliance_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'appliance_title' => 'varchar(255) NOT NULL',
    ), 'appliance_id');
    create_or_update('estatik_manager_categories', array(
        'cat_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'cat_title' => 'varchar(255) NOT NULL',
    ), 'cat_id');
    create_or_update('estatik_manager_cities', array(
        'city_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'city_title' => 'varchar(255) NOT NULL',
        'state_id' => 'int(11) NOT NULL',
    ), 'city_id');
    create_or_update('estatik_manager_countries', array(
        'country_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'country_title' => 'varchar(255) NOT NULL',
    ), 'country_id');
    create_or_update('estatik_manager_currency', array(
        'currency_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'currency_title' => 'varchar(255) NOT NULL',
        'currency_status' => 'int(11) NOT NULL',
    ), 'currency_id');
    create_or_update('estatik_manager_dimension', array(
        'dimension_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'dimension_title' => 'varchar(255) NOT NULL',
        'dimension_status' => 'int(11) NOT NULL',
    ), 'dimension_id');
    create_or_update('estatik_manager_features', array(
        'feature_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'feature_title' => 'varchar(255) NOT NULL',
    ), 'feature_id');
    create_or_update('estatik_manager_neighboarhood', array(
        'neigh_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'neigh_title' => 'varchar(255) NOT NULL',
    ), 'neigh_id');
    create_or_update('estatik_manager_rent_period', array(
        'period_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'period_title' => 'varchar(255) NOT NULL',
    ), 'period_id');
    create_or_update('estatik_manager_states', array(
        'state_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'state_title' => 'varchar(255) NOT NULL',
        'country_id' => 'int(11) NOT NULL',
    ), 'state_id');
    create_or_update('estatik_manager_status', array(
        'status_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'status_title' => 'varchar(255) NOT NULL',
    ), 'status_id');
    create_or_update('estatik_manager_types', array(
        'type_id' => 'int(11) NOT NULL AUTO_INCREMENT',
        'type_title' => 'varchar(255) NOT NULL',
    ), 'type_id');
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
    $create_agents_meta = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'estatik_agents_meta (
                            `id` INT NOT NULL AUTO_INCREMENT,
                            `agent_id` INT NOT NULL,
                            `meta_key` VARCHAR(255) NOT NULL,
                            `meta_value` VARCHAR(255) NOT NULL,
                            PRIMARY KEY (`id`)
                            )
                            ENGINE=InnoDB
                            DEFAULT CHARSET=utf8';
    $wpdb->query($create_agents_meta);
}
