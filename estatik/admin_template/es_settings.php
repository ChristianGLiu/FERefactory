<?php
global $wpdb;
$default_gmap_api = get_option('gmap_api');
$taxonomies = array(
    'property_category',
);
$args = array(
    'orderby' => 'name',
    'order' => 'ASC',
    'hide_empty' => false,
    'exclude' => array(),
    'exclude_tree' => array(),
    'include' => array(),
    'number' => '',
    'fields' => 'all',
    'slug' => '',
    'parent' => '',
    'hierarchical' => true,
    'child_of' => 0,
    'childless' => false,
    'get' => '',
    'name__like' => '',
    'description__like' => '',
    'pad_counts' => false,
    'offset' => '',
    'search' => '',
    'cache_domain' => 'core'
);
$prop_categories = get_terms($taxonomies, $args);
if($default_gmap_api === false){
    if($default_gmap_api != 1) {
        update_option('gmap_api', '1');
    }
}
if(isset($_POST['es_settings_submit'])){
    $wpdb->update(
        $wpdb->prefix.'estatik_settings',
        array(
            'address'                               => sanitize_text_field($_POST['address']),
            'agent'                                 => sanitize_text_field($_POST['agent']),
            'agents_height'                         => sanitize_text_field($_POST['agents_height']),
            'agents_width'                          => sanitize_text_field($_POST['agents_width']),
            'currency_sign_place'                   => sanitize_text_field($_POST['currency_sign_place']),
            'date_format'                           => sanitize_text_field($_POST['date_format']),
            'default_currency'                      => sanitize_text_field($_POST['default_currency']),
            'labels'                                => sanitize_text_field($_POST['labels']),
            'date_added'                            => sanitize_text_field($_POST['date_added']),
            'listing_layout'                        => sanitize_text_field($_POST['listing_layout']),
            'powered_by_link'                       => sanitize_text_field($_POST['powered_by_link']),
            'pdf_player'                            => sanitize_text_field($_POST['pdf_player']),
            'price'                                 => sanitize_text_field($_POST['price']),
            'price_format'                          => sanitize_text_field($_POST['price_format']),
            'prop_listview_2column_height'          => sanitize_text_field($_POST['prop_listview_2column_height']),
            'prop_listview_2column_width'           => sanitize_text_field($_POST['prop_listview_2column_width']),
            'prop_listview_list_height'             => sanitize_text_field($_POST['prop_listview_list_height']),
            'prop_listview_list_width'              => sanitize_text_field($_POST['prop_listview_list_width']),
            'prop_listview_table_height'            => sanitize_text_field($_POST['prop_listview_table_height']),
            'prop_listview_table_width'             => sanitize_text_field($_POST['prop_listview_table_width']),
            'prop_singleview_photo_center_height'   => sanitize_text_field($_POST['prop_singleview_photo_center_height']),
            'prop_singleview_photo_center_width'    => sanitize_text_field($_POST['prop_singleview_photo_center_width']),
            'prop_singleview_photo_lr_height'       => sanitize_text_field($_POST['prop_singleview_photo_lr_height']),
            'prop_singleview_photo_lr_width'        => sanitize_text_field($_POST['prop_singleview_photo_lr_width']),
            'prop_singleview_photo_thumb_height'    => sanitize_text_field($_POST['prop_singleview_photo_thumb_height']),
            'prop_singleview_photo_thumb_width'     => sanitize_text_field($_POST['prop_singleview_photo_thumb_width']),
            // 'property_slug'                         => sanitize_text_field($_POST['property_slug']),
            'resize_method'                         => sanitize_text_field($_POST['resize_method']),
            'no_of_listing'                         => sanitize_text_field($_POST['no_of_listing']),
            'single_property_layout'                => sanitize_text_field($_POST['single_property_layout']),
            'theme_style'                           => sanitize_text_field($_POST['theme_style']),
            'title'                                 => sanitize_text_field($_POST['title']),
            'view_first_on_off'                     => sanitize_text_field($_POST['view_first_on_off']),
            'twitter_link'                          => sanitize_text_field($_POST['twitter_link']),
            'facebook_link'                         => sanitize_text_field($_POST['facebook_link']),
            'google_plus_link'                      => sanitize_text_field($_POST['google_plus_link']),
            'linkedin_link'                         => sanitize_text_field($_POST['linkedin_link']),
        ),
        array( 'setting_id' => 1 )
    );
    if(isset($_POST['gmap_settings_height']) && !empty($_POST['gmap_settings_height'])){
        update_option('gmap_height', $_POST['gmap_settings_height']);
    }
    $gmap_height = get_option('gmap_height');
    if(!$gmap_height || empty($gmap_height)){
        update_option('gmap_height', '500');
        $gmap_height = get_option('gmap_height');
    }
    $gmap_markers_limit = get_option('gmap_markers_limit');
    if(!$gmap_markers_limit || empty($gmap_markers_limit)){
        update_option('gmap_markers_limit', '20');
        $gmap_markers_limit = get_option('gmap_markers_limit');
    }
    if(isset($_POST['gmap_settings_count']) && !empty($_POST['gmap_settings_count'])){
        update_option('gmap_markers_limit', $_POST['gmap_settings_count']);
    }
    $gmap_markers_zoom = get_option('gmap_markers_zoom');
    if(!$gmap_markers_zoom || empty($gmap_markers_zoom)){
        update_option('gmap_markers_zoom', '12');
        $gmap_markers_zoom = get_option('gmap_markers_zoom');
    }
    if(isset($_POST['gmap_settings_zoom']) && !empty($_POST['gmap_settings_zoom'])){
        update_option('gmap_settings_zoom', $_POST['gmap_settings_zoom']);
    }
    $prop_categories = get_terms($taxonomies, $args);
    $table_name = $wpdb->prefix . 'estatik_category_meta';
    $agent_sql = "SELECT * FROM " . $wpdb->prefix . "estatik_agents WHERE agent_status = 1 order by agent_id desc";
    $es_agent_result = $wpdb->get_results($agent_sql);
    if (!empty($_POST['pink_categories_list'])) {
        $wpdb->query("DELETE FROM $table_name WHERE meta_key = 'gmap_icon_color' AND meta_value = 'pink'");
        foreach ($_POST['pink_categories_list'] as $category) {
            if (!empty($category)) {
                es_update_category_meta($category, 'gmap_icon_color', 'pink');
            } elseif ($category == 'none') {
                $wpdb->query("DELETE FROM $table_name WHERE meta_key = 'gmap_icon_color' AND meta_value = 'pink'");
            }  else {
                $wpdb->query("DELETE FROM $table_name WHERE meta_key = 'gmap_icon_color' AND meta_value = 'pink'");
            }
        }
    }
    if (!empty($_POST['blue_categories_list'])) {
        $wpdb->query("DELETE FROM $table_name WHERE meta_key = 'gmap_icon_color' AND meta_value = 'blue'");
        foreach ($_POST['blue_categories_list'] as $category) {
            if (!empty($category)) {
                es_update_category_meta($category, 'gmap_icon_color', 'blue');
            } elseif ($category == 'none') {
                $wpdb->query("DELETE FROM $table_name WHERE meta_key = 'gmap_icon_color' AND meta_value = 'blue'");
            }  else {
                $wpdb->query("DELETE FROM $table_name WHERE meta_key = 'gmap_icon_color' AND meta_value = 'blue'");
            }
        }
    }
    if (!empty($_POST['green_categories_list'])) {
        $wpdb->query("DELETE FROM $table_name WHERE meta_key = 'gmap_icon_color' AND meta_value = 'green'");
        foreach ($_POST['green_categories_list'] as $category) {
            if (!empty($category)) {
                es_update_category_meta($category, 'gmap_icon_color', 'green');
            } elseif ($category == 'none') {
                $wpdb->query("DELETE FROM $table_name WHERE meta_key = 'gmap_icon_color' AND meta_value = 'green'");
            } else {
                $wpdb->query("DELETE FROM $table_name WHERE meta_key = 'gmap_icon_color' AND meta_value = 'green'");
            }
        }
    }
    if (isset($_POST['house_type_categories_list']) && !empty($_POST['house_type_categories_list'])) {
        $wpdb->query("DELETE FROM $table_name WHERE meta_key = 'gmap_icon_type' AND meta_value = 'house'");
        foreach ($_POST['house_type_categories_list'] as $category) {
            if (!empty($category) && $category != 0) {
                es_update_category_meta($category, 'gmap_icon_type', 'house');
            } elseif ($category == 'none') {
                $wpdb->query("DELETE FROM $table_name WHERE meta_key = 'gmap_icon_type' AND meta_value = 'house'");
            } else {
                $wpdb->query("DELETE FROM $table_name WHERE meta_key = 'gmap_icon_type' AND meta_value = 'house'");
            }
        }
    }
    if (isset($_POST['flag_type_categories_list']) && !empty($_POST['flag_type_categories_list'])) {
        $wpdb->query("DELETE FROM $table_name WHERE meta_key = 'gmap_icon_type' AND meta_value = 'flag'");
        foreach ($_POST['flag_type_categories_list'] as $category) {
            if (!empty($category) && $category != 0) {
                es_update_category_meta($category, 'gmap_icon_type', 'flag');
            } elseif ($category == 'none') {
                $wpdb->query("DELETE FROM $table_name WHERE meta_key = 'gmap_icon_type' AND meta_value = 'flag'");
            }  else {
                $wpdb->query("DELETE FROM $table_name WHERE meta_key = 'gmap_icon_type' AND meta_value = 'flag'");
            }
        }
    }
    if (isset($_POST['point_type_categories_list']) && !empty($_POST['point_type_categories_list'])) {
        $wpdb->query("DELETE FROM $table_name WHERE meta_key = 'gmap_icon_type' AND meta_value = 'point'");
        foreach ($_POST['point_type_categories_list'] as $category) {
            if (!empty($category) && $category != 0) {
                es_update_category_meta($category, 'gmap_icon_type', 'point');
            } elseif ($category == 'none') {
                $wpdb->query("DELETE FROM $table_name WHERE meta_key = 'gmap_icon_type' AND meta_value = 'point'");
            } else {
                $wpdb->query("DELETE FROM $table_name WHERE meta_key = 'gmap_icon_type' AND meta_value = 'point'");
            }
        }
    }
    /**
     * save google maps api settings
     */
    if(isset($_POST['gmap_api'])){
        update_option('gmap_api', $_POST['gmap_api']);
    }
    wp_redirect('?page=es_settings', 301);
    exit;
}
$gmap_height = get_option('gmap_height');
if(!$gmap_height || empty($gmap_height)){
    update_option('gmap_height', '500');
    $gmap_height = get_option('gmap_height');
}
$gmap_markers_limit = get_option('gmap_markers_limit');
if(!$gmap_markers_limit || empty($gmap_markers_limit)){
    update_option('gmap_markers_limit', '20');
    $gmap_markers_limit = get_option('gmap_markers_limit');
}
$gmap_markers_zoom = get_option('gmap_settings_zoom');
if(!$gmap_markers_zoom || empty($gmap_markers_zoom)){
    update_option('gmap_settings_zoom', '12');
    $gmap_markers_zoom = get_option('gmap_settings_zoom');
}
/**
 * Get google maps API settings
 */
$map_settings = get_option('gmap_api');
if($map_settings != 0){
    $map_settings = 1;
}
?>
<div class="es_wrapper">
    <div class="es_header clearFix">
        <h2><?php _e( "Settings", "es-plugin" ); ?></h2>
        <h3><img src="<?php echo DIR_URL.'admin_template/';?>images/estatik_pro.png" alt="#" /><small>Ver. <?php echo es_plugin_version(); ?></small></h3>
    </div>
    <form method="post" id="es_settings_form" action="">
        <div class="esHead clearFix">
            <p><?php _e( "Please fill up your Settings detail and click save to finish.", "es-plugin" ); ?></p>
            <input type="submit" value="<?php _e( "Save", "es-plugin" ); ?>" name="es_settings_submit" />
        </div>
        <?php if(isset($_POST['es_settings_submit'])) { ?>
            <div class="es_success"><?php _e( "Settings has been updated.", "es-plugin" ); ?></div>
        <?php } ?>
        <div class="es_content_in">
            <div class="es_tabs clearFix">
                <ul>
                    <li><a href="#es_general_settings"><?php _e( "General Settings", "es-plugin" ); ?></a></li>
                    <li><a href="#es_layout"><?php _e( "Layout", "es-plugin" ); ?></a></li>
                    <li><a href="#es_images"><?php _e( "Images", "es-plugin" ); ?></a></li>
                    <li><a href="#es_currency"><?php _e( "Currency", "es-plugin" ); ?></a></li>
                    <li><a href="#es_sharing"><?php _e( "Sharing", "es-plugin" ); ?></a></li>
                    <li><a href="#es_map_view"><?php _e('Map View', 'es-plugin'); ?></a></li>
                    <li><a href="#es_subscription"><?php _e( "Subscription", "es-plugin" ); ?></a></li>
                </ul>
            </div>
            <?php
            $es_settings = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}estatik_settings WHERE `setting_id`=1");
            ?>
            <div id="es_settings" class="es_tabs_contents  clearFix">
                <div id="es_general_settings" class="es_tabs_content_in clearFix">
                    <div class="es_settings_field clearFix">
                        <span><?php _e( "Powered by link", "es-plugin" ); ?>:</span>
                        <label class="<?php if($es_settings->powered_by_link=='1'){ echo 'active'; } ?>"><input type="radio" value="1" <?php if($es_settings->powered_by_link=='1'){ echo 'checked="checked"'; } ?> name="powered_by_link" /><?php _e( "Yes", "es-plugin" ); ?></label>
                        <label class="<?php if($es_settings->powered_by_link=='0'){ echo 'active'; } ?>"><input type="radio" value="0" <?php if($es_settings->powered_by_link=='0'){ echo 'checked="checked"'; } ?> name="powered_by_link" /><?php _e( "No", "es-plugin" ); ?></label>
                    </div>
                    <div class="es_settings_field clearFix">
                        <span><?php _e( "Number of listings per page", "es-plugin" ); ?>:</span>
                        <input type="number" name="no_of_listing" value="<?php echo $es_settings->no_of_listing?>" />
                    </div>
                    <div class="es_settings_field clearFix">
                        <span><?php _e( "Price", "es-plugin" ); ?>:</span>
                        <label class="<?php if($es_settings->price=='1'){ echo 'active'; } ?>"><input type="radio" value="1" <?php if($es_settings->price=='1'){ echo 'checked="checked"'; } ?> name="price" /><?php _e( "Yes", "es-plugin" ); ?></label>
                        <label class="<?php if($es_settings->price=='0'){ echo 'active'; } ?>"><input type="radio" value="0" <?php if($es_settings->price=='0'){ echo 'checked="checked"'; } ?> name="price" /><?php _e( "No", "es-plugin" ); ?></label>
                    </div>
                    <div class="es_settings_field clearFix">
                        <span><?php _e( "Title/Address", "es-plugin" ); ?>:</span>
                        <label class="<?php if($es_settings->title=='1'){ echo 'active'; } ?>"><input type="radio" value="1" <?php if($es_settings->title=='1'){ echo 'checked="checked"'; } ?> name="title" /><?php _e( "Title", "es-plugin" ); ?></label>
                        <label class="<?php if($es_settings->title=='0'){ echo 'active'; } ?>"><input type="radio" value="0" <?php if($es_settings->title=='0'){ echo 'checked="checked"'; } ?> name="title" /><?php _e( "Address", "es-plugin" ); ?></label>
                    </div>
                    <div class="es_settings_field clearFix">
                        <span><?php _e( "Address", "es-plugin" ); ?>:</span>
                        <label class="<?php if($es_settings->address=='1'){ echo 'active'; } ?>"><input type="radio" value="1" <?php if($es_settings->address=='1'){ echo 'checked="checked"'; } ?> name="address" /><?php _e( "Yes", "es-plugin" ); ?></label>
                        <label class="<?php if($es_settings->address=='0'){ echo 'active'; } ?>"><input type="radio" value="0" <?php if($es_settings->address=='0'){ echo 'checked="checked"'; } ?> name="address" /><?php _e( "No", "es-plugin" ); ?></label>
                    </div>
                    <div class="es_settings_field clearFix">
                        <span><?php _e( "Agent", "es-plugin" ); ?>:</span>
                        <label class="<?php if($es_settings->agent=='1'){ echo 'active'; } ?>"><input type="radio" value="1" <?php if($es_settings->agent=='1'){ echo 'checked="checked"'; } ?> name="agent" /><?php _e( "Yes", "es-plugin" ); ?></label>
                        <label class="<?php if($es_settings->agent=='0'){ echo 'active'; } ?>"><input type="radio" value="0" <?php if($es_settings->agent=='0'){ echo 'checked="checked"'; } ?> name="agent" /><?php _e( "No", "es-plugin" ); ?></label>
                    </div>
                    <div class="es_settings_field clearFix">
                        <span><?php _e( "Labels", "es-plugin" ); ?>:</span>
                        <label class="<?php if($es_settings->labels=='1'){ echo 'active'; } ?>"><input type="radio" value="1" <?php if($es_settings->labels=='1'){ echo 'checked="checked"'; } ?> name="labels" /><?php _e( "Yes", "es-plugin" ); ?></label>
                        <label class="<?php if($es_settings->labels=='0'){ echo 'active'; } ?>"><input type="radio" value="0" <?php if($es_settings->labels=='0'){ echo 'checked="checked"'; } ?> name="labels" /><?php _e( "No", "es-plugin" ); ?></label>
                    </div>
                    <div class="es_settings_field clearFix">
                        <span><?php _e( "Enable Google Maps API", "es-plugin" ); ?>:</span>
                        <label class="<?php if($map_settings=='1'){ echo 'active'; } ?>"><input type="radio" value="1" <?php if($map_settings=='1'){ echo 'checked="checked"'; } ?> name="gmap_api" /><?php _e( "Yes", "es-plugin" ); ?></label>
                        <label class="<?php if($map_settings=='0'){ echo 'active'; } ?>"><input type="radio" value="0" <?php if($map_settings=='0'){ echo 'checked="checked"'; } ?> name="gmap_api" /><?php _e( "No", "es-plugin" ); ?></label>
                    </div>
                    <div class="es_settings_field clearFix">
                        <span><?php _e( "Date added", "es-plugin" ); ?>:</span>
                        <label class="<?php if($es_settings->date_added=='1'){ echo 'active'; } ?>"><input type="radio" value="1" <?php if($es_settings->date_added=='1'){ echo 'checked="checked"'; } ?> name="date_added" /><?php _e( "Yes", "es-plugin" ); ?></label>
                        <label class="<?php if($es_settings->date_added=='0'){ echo 'active'; } ?>"><input type="radio" value="0" <?php if($es_settings->date_added=='0'){ echo 'checked="checked"'; } ?> name="date_added" /><?php _e( "No", "es-plugin" ); ?></label>
                    </div>
                    <div class="es_settings_field clearFix">
                        <span><?php _e( "Date format", "es-plugin" ); ?>:</span>
                        <select name="date_format">
                            <option value=""><?php _e( "Date format", "es-plugin" ); ?></option>
                            <option <?php if($es_settings->date_format=='d/m/y'){ echo 'selected="selected"'; } ?> value="<?php echo "d/m/y"?>"><?php echo date("d/m/y");?></option>
                            <option <?php if($es_settings->date_format=='m/d/y'){ echo 'selected="selected"'; } ?> value="<?php echo "m/d/y"?>"><?php echo date("m/d/y");?></option>
                            <option <?php if($es_settings->date_format=='d.m.y'){ echo 'selected="selected"'; } ?> value="<?php echo "d.m.y"?>"><?php echo date("d.m.y");?></option>
                        </select>
                    </div>
                    <!-- <div class="es_settings_field clearFix">
                        <span><?php _e( "Property page slug", "es-plugin" ); ?>:</span>
                        <input type="text" name="property_slug" 
                               value="<?php echo $es_settings->property_slug?>" />
                    </div> -->
                    <div class="es_settings_field clearFix">
                        <span><?php _e( "Choose theme color style", "es-plugin" ); ?>:</span>
                        <select name="theme_style">
                            <option <?php if($es_settings->theme_style=='light'){ echo 'selected="selected"'; } ?> value="<?php echo "light"?>"><?php _e("Light", "es-plugin"); ?></option>
                            <option <?php if($es_settings->theme_style=='dark'){ echo 'selected="selected"'; } ?> value="<?php echo "dark"?>"><?php _e("Dark", "es-plugin"); ?></option>
                        </select>
                    </div>
                    <div class="es_settings_field clearFix">
                        <span><?php _e( "View first menu", "es-plugin" ); ?>:</span>
                        <label class="<?php if($es_settings->view_first_on_off=='1'){ echo 'active'; } ?>">
                            <input type="radio" value="1" name="view_first_on_off"
                                <?php if($es_settings->view_first_on_off=='1'){ echo 'checked="checked"'; } ?> />
                            <?php _e('Yes', 'es-plugin'); ?>
                        </label>
                        <label class="<?php if($es_settings->view_first_on_off=='0'){ echo 'active'; } ?>">
                            <input type="radio" value="0" name="view_first_on_off"
                                <?php if($es_settings->view_first_on_off=='0'){ echo 'checked="checked"'; } ?> />
                            <?php _e('No', 'es-plugin'); ?>
                        </label>
                    </div>
                </div>
                <div id="es_subscription" class="es_tabs_content_in clearFix">
                    <!-- START SUBSCRIPTION SETTINGS -->
                    <h3><?php _e( 'Subscription settings', 'es-plugin' ); ?></h3>
                    <div class="es_settings_field clearFix">
                        <span><?php _e( "Listing publishing", "es-plugin" ); ?>:</span>
                        <label class="<?php echo es_listing_publishing_type() ? 'active' : ''; ?>">
                            <input type="radio" value="1" name="listing_publishing" <?php checked(1, es_listing_publishing_type()); ?>/>
                            <?php _e('Automatic', 'es-plugin'); ?>
                        </label>
                        <label class="<?php echo ! es_listing_publishing_type() ? 'active' : ''; ?>">
                            <input type="radio" value="0" name="listing_publishing" <?php checked(0, es_listing_publishing_type()); ?>/>
                            <?php _e('Manual', 'es-plugin'); ?>
                        </label>
                    </div>
                    <div class="es_settings_field clearFix">
                        <span><?php _e( "Enable Subscriptions", "es-plugin" ); ?>:</span>
                        <label class="<?php echo es_is_enabled_subscription() ? 'active' : ''; ?>">
                            <input type="radio" value="1" name="enable_subscription" <?php checked(1, es_is_enabled_subscription()); ?>/>
                            <?php _e('Yes', 'es-plugin'); ?>
                        </label>
                        <label class="<?php echo ! es_is_enabled_subscription() ? 'active' : ''; ?>">
                            <input type="radio" value="0" name="enable_subscription" <?php checked(0, es_is_enabled_subscription()); ?>/>
                            <?php _e('No', 'es-plugin'); ?>
                        </label>
                    </div>
                    <div class="es_settings_field clearFix">
                        <span><?php _e( "Currency", "es-plugin" ); ?>:</span>
                        <select name="es_currency">
                            <option value=""><?php _e( '- Select Value -', 'es-plugin' ); ?></option>
                            <?php foreach( es_get_currencies() as $code => $cur ) : ?>
                                <option value="<?php echo $code; ?>" <?php selected( $code, es_get_currency() ); ?>>
                                    <?php echo $cur; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="es_settings_field clearFix">
                        <span><?php echo __( 'Currency sign place', 'es-plugin' ); ?>:</span>
                        <label class="<?php echo es_currency_position() == 'before' ? 'active' : ''; ?>"><input type="radio" value="before" name="es_currency_sign_place"><?php _e( 'Before price', 'es-plugin' ); ?></label>
                        <label class="<?php echo es_currency_position() == 'after' ? 'active' : ''; ?>"><input type="radio" value="after" name="es_currency_sign_place"><?php _e( 'After price', 'es-plugin' ); ?></label>
                    </div>
                    <?php $es_pages = es_get_pages_helper(); ?>
                    <?php if ( !empty( $es_pages ) ) : ?>
                        <div class="es_settings_field clearFix">
                            <span><?php _e( "Registration page", "es-plugin" ); ?>:</span>
                            <select name="es_register_page">
                                <option value=""><?php _e( '- Select Value -', 'es-plugin' ); ?></option>
                                <?php foreach( $es_pages as $page ) : ?>
                                    <option value="<?php echo $page->ID; ?>" <?php selected( $page->ID, es_get_register_page() ); ?>>
                                        <?php echo !empty($page->post_title) ? $page->post_title : __( 'No title', 'es-plugin' ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="es_settings_field clearFix">
                            <span><?php _e( "Manage properties page", "es-plugin" ); ?>:</span>
                            <select name="es_manage_page">
                                <option value=""><?php _e( '- Select Value -', 'es-plugin' ); ?></option>
                                <?php foreach( $es_pages as $page ) : ?>
                                    <option value="<?php echo $page->ID; ?>" <?php selected( $page->ID, es_get_manage_page() ); ?>>
                                        <?php echo !empty($page->post_title) ? $page->post_title : __( 'No title', 'es-plugin' ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="es_settings_field clearFix">
                            <span><?php _e( "Subscriptions table page", "es-plugin" ); ?>:</span>
                            <select name="es_subscription_table_page">
                                <option value=""><?php _e( '- Select Value -', 'es-plugin' ); ?></option>
                                <?php foreach( $es_pages as $page ) : ?>
                                    <option value="<?php echo $page->ID; ?>" <?php selected( $page->ID, es_get_subscription_table_page() ); ?>>
                                        <?php echo !empty($page->post_title) ? $page->post_title : __( 'No title', 'es-plugin' ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    <h3><?php _e( 'Subscription expired email settings', 'es-plugin' ); ?></h3>
                    <div class="es_settings_field clearFix">
                        <span><?php _e( "Subscription expired subject", "es-plugin" ); ?>:</span>
                        <input type="text" name="es_get_expired_subscription_subject" value="<?php echo es_get_expired_subscription_subject(); ?>"/>
                    </div>
                    <div class="es_settings_field clearFix">
                        <span><?php _e( "Subscription expired message", "es-plugin" ); ?>:</span></br></br>
                        <?php wp_editor( es_get_expired_subscription_body(), 'es_get_expired_subscription_body' ); ?>
                    </div>
                    <h3><?php _e( 'Subscription Paypal settings', 'es-plugin' ); ?></h3>
                    <div class="es_settings_field clearFix">
                        <span><?php _e( "Paypal Type", "es-plugin" ); ?>:</span>
                        <select name="es_paypal_type">
                            <option value="standard" <?php selected( 'standard', es_get_paypal_type() ); ?>><?php _e( 'Standard', 'es-plugin' ); ?></option>
                            <option value="express" <?php selected( 'express', es_get_paypal_type() ); ?>><?php _e( 'Express', 'es-plugin' ); ?></option>
                        </select>
                    </div>
                    <div class="es_paypal_block es_settings_paypal_standard">
                        <div class="es_settings_field clearFix">
                            <span><?php _e( "Paypal email", "es-plugin" ); ?>:</span>
                            <input type="email" name="paypal_email" value="<?php echo es_get_paypal_email(); ?>"/>
                        </div>
                    </div>
                    <div class="es_settings_field clearFix">
                        <span><?php _e( "Paypal API Mode", "es-plugin" ); ?>:</span>
                        <select name="es_paypal_mode">
                            <option value="sandbox" <?php selected( 'sandbox', es_get_paypal_mode() ); ?>><?php _e( 'Sandbox (test mode)', 'es-plugin' ); ?></option>
                            <option value="live" <?php selected( 'live', es_get_paypal_mode() ); ?>><?php _e( 'Live', 'es-plugin' ); ?></option>
                        </select>
                    </div>
                    <div class="es_paypal_block es_settings_paypal_express">
                        <div class="es-paypal-block live">
                            <?php $paypal_credentials = es_get_paypal_credentials( 'live' ); ?>
                            <div class="es_settings_field clearFix">
                                <span><?php _e( "Paypal API key", "es-plugin" ); ?>:</span>
                                <input type="text" name="es_paypal_key" value="<?php echo ! empty( $paypal_credentials['clientId'] ) ? $paypal_credentials['clientId'] : ''; ?>"/>
                            </div>
                            <div class="es_settings_field clearFix">
                                <span><?php _e( "Paypal API secret", "es-plugin" ); ?>:</span>
                                <input type="text" name="es_paypal_secret" value="<?php echo ! empty( $paypal_credentials['clientSecret'] ) ? $paypal_credentials['clientSecret'] : ''; ?>"/>
                            </div>
                        </div>
                        <div class="es-paypal-block sandbox">
                            <?php $paypal_credentials = es_get_paypal_credentials(); ?>
                            <div class="es_settings_field clearFix">
                                <span><?php _e( "Paypal Sandbox API key", "es-plugin" ); ?>:</span>
                                <input type="text" name="es_paypal_sandbox_key" value="<?php echo ! empty( $paypal_credentials['clientId'] ) ? $paypal_credentials['clientId'] : ''; ?>"/>
                            </div>
                            <div class="es_settings_field clearFix">
                                <span><?php _e( "Paypal Sandbox API secret", "es-plugin" ); ?>:</span>
                                <input type="text" name="es_paypal_sandbox_secret" value="<?php echo ! empty( $paypal_credentials['clientSecret'] ) ? $paypal_credentials['clientSecret'] : ''; ?>"/>
                            </div>
                        </div>
                    </div>
                    <!-- END OF SUBSCRIPTION SETTINGS -->
                </div>
                <div id="es_layout" class="es_tabs_content_in clearFix">
                    <div class="es_layout es_list_view clearFix">
                        <span><?php _e( "Listings layout", "es-plugin" ); ?>:</span>
                        <label
                            class="<?php if($es_settings->listing_layout=='table'){ echo 'active'; } ?>">
                            <small></small>
                            <input type="radio" name="listing_layout"
                                <?php if($es_settings->listing_layout=='table'){ echo 'checked="checked"'; } ?>
                                   value="table" />
                        </label>
                        <label
                            class="<?php if($es_settings->listing_layout=='2columns'){ echo 'active'; } ?>">
                            <small></small>
                            <input type="radio" name="listing_layout"
                                <?php if($es_settings->listing_layout=='2columns'){ echo 'checked="checked"'; } ?>
                                   value="2columns" />
                        </label>
                        <label
                            class="<?php if($es_settings->listing_layout=='list'){ echo 'active'; } ?>">
                            <small></small>
                            <input type="radio" name="listing_layout"
                                <?php if($es_settings->listing_layout=='list'){ echo 'checked="checked"'; } ?>
                                   value="list" />
                        </label>
                    </div>
                    <div class="es_layout es_single_view clearFix">
                        <span><?php _e( "Single property", "es-plugin" ); ?>:</span>
                        <label class="<?php if($es_settings->single_property_layout=='3'){ echo 'active'; } ?>"><small></small><input type="radio" name="single_property_layout" <?php if($es_settings->single_property_layout=='3'){ echo 'checked="checked"'; } ?> value="3" /></label>
                        <label class="<?php if($es_settings->single_property_layout=='2'){ echo 'active'; } ?>"><small></small><input type="radio" name="single_property_layout" <?php if($es_settings->single_property_layout=='2'){ echo 'checked="checked"'; } ?> value="2" /></label>
                        <label class="<?php if($es_settings->single_property_layout=='1'){ echo 'active'; } ?>"><small></small><input type="radio" name="single_property_layout" <?php if($es_settings->single_property_layout=='1'){ echo 'checked="checked"'; } ?> value="1" /></label>
                    </div>
                </div>
                <div id="es_images" class="es_tabs_content_in clearFix">
                    <div class="es_images_setting_head clearFix">
                        <h2><?php _e( "Properties", "es-plugin" ); ?></h2>
                        <div class="es_images_setting_resize">
                            <span><?php _e( "Resize method", "es-plugin" ); ?>:</span>
                            <label class="<?php if($es_settings->resize_method=='crop_shrink'){ echo 'active'; } ?>"><input type="radio" value="crop_shrink" <?php if($es_settings->resize_method=='crop_shrink'){ echo 'checked="checked"'; } ?> name="resize_method" /><?php _e( "Crop & shrink", "es-plugin" ); ?></label>
                            <label class="<?php if($es_settings->resize_method=='crop'){ echo 'active'; } ?>"><input type="radio" value="crop" <?php if($es_settings->resize_method=='crop'){ echo 'checked="checked"'; } ?> name="resize_method" /><?php _e( "Crop", "es-plugin" ); ?></label>
                        </div>
                    </div>
                    <div class="es_images_setting clearFix">
                        <ul>
                            <li class="clearFix">
                                <div><span><?php _e( "Listings view", "es-plugin" ); ?>:</span></div>
                                <div><label><?php _e( "Table", "es-plugin" ); ?></label></div>
                                <div><label><?php _e( "2 colums", "es-plugin" ); ?></label></div>
                                <div><label><?php _e( "list", "es-plugin" ); ?></label></div>
                            </li>
                            <li class="clearFix">
                                <div><span><?php _e( "Height", "es-plugin" ); ?>, px</span></div>
                                <div><input type="text" value="<?php echo $es_settings->prop_listview_table_height;?>" name="prop_listview_table_height" /></div>
                                <div><input type="text" value="<?php echo $es_settings->prop_listview_2column_height;?>" name="prop_listview_2column_height" /></div>
                                <div><input type="text" value="<?php echo $es_settings->prop_listview_list_height;?>" name="prop_listview_list_height" /></div>
                            </li>
                            <li class="clearFix">
                                <div><span><?php _e( "Widht", "es-plugin" ); ?>, px</span></div>
                                <div><input type="text" value="<?php echo $es_settings->prop_listview_table_width;?>" name="prop_listview_table_width" /></div>
                                <div><input type="text" value="<?php echo $es_settings->prop_listview_2column_width;?>" name="prop_listview_2column_width" /></div>
                                <div><input type="text" value="<?php echo $es_settings->prop_listview_list_width;?>" name="prop_listview_list_width" /></div>
                            </li>
                        </ul>
                    </div>
                    <div class="es_images_setting clearFix">
                        <ul>
                            <li class="clearFix">
                                <div><span><?php _e( "Single property", "es-plugin" ); ?>:</span></div>
                                <div><label><?php _e( "Photo on<br />the left/right", "es-plugin" ); ?></label></div>
                                <div><label><?php _e( "Photo <br />in center", "es-plugin" ); ?></label></div>
                                <div><label><?php _e( "Photo <br /> Thumb", "es-plugin" ); ?></label></div>
                            </li>
                            <li class="clearFix">
                                <div><span><?php _e( "Height", "es-plugin" ); ?>, px</span></div>
                                <div><input type="text" value="<?php echo $es_settings->prop_singleview_photo_lr_height;?>" name="prop_singleview_photo_lr_height" /></div>
                                <div><input type="text" value="<?php echo $es_settings->prop_singleview_photo_center_height;?>" name="prop_singleview_photo_center_height" /></div>
                                <div><input type="text" value="<?php echo $es_settings->prop_singleview_photo_thumb_height;?>" name="prop_singleview_photo_thumb_height" /></div>
                            </li>
                            <li class="clearFix">
                                <div><span><?php _e( "Widht", "es-plugin" ); ?>, px</span></div>
                                <div><input type="text" value="<?php echo $es_settings->prop_singleview_photo_lr_width;?>" name="prop_singleview_photo_lr_width" /></div>
                                <div><input type="text" value="<?php echo $es_settings->prop_singleview_photo_center_width;?>" name="prop_singleview_photo_center_width" /></div>
                                <div><input type="text" value="<?php echo $es_settings->prop_singleview_photo_thumb_width;?>" name="prop_singleview_photo_thumb_width" /></div>
                            </li>
                        </ul>
                    </div>
                    <div class="es_images_setting_head clearFix">
                        <h2><?php _e( "Agents", "es-plugin" ); ?></h2>
                    </div>
                    <div class="es_images_setting clearFix">
                        <ul>
                            <li class="clearFix">
                                <div><span><?php _e( "Height", "es-plugin" ); ?>, px</span></div>
                                <div><input type="text" value="<?php echo $es_settings->agents_height;?>" name="agents_height" /></div>
                            </li>
                            <li class="clearFix">
                                <div><span><?php _e( "Widht", "es-plugin" ); ?>, px</span></div>
                                <div><input type="text" value="<?php echo $es_settings->agents_width;?>" name="agents_width" /></div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="es_currency" class="es_tabs_content_in clearFix">
                    <div class="es_settings_field clearFix">
                        <span><?php _e( "Default currency", "es-plugin" ); ?>:</span>
                        <select name="default_currency">
                            <option value=""><?php _e( "Dollar, Euro, etc", "es-plugin" ); ?>.</option>
                            <?php global $wpdb;
                            $es_currency_listing = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.'estatik_manager_currency' );
                            if(!empty($es_currency_listing)) {
                                foreach($es_currency_listing as $list) {
                                    $selected = ($es_settings->default_currency==$list->currency_title) ? 'selected="selected"' : "";
                                    echo '<option '.$selected.' value="'.$list->currency_title.'">'.$list->currency_title.'</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="es_settings_field clearFix">
                        <span><?php _e( "Price format", "es-plugin" ); ?>:</span>
                        <select name="price_format">
                            <option <?php if($es_settings->price_format=='2|.|,'){ echo 'selected="selected"'; } ?> value="2|.|,">19,999.00</option>
                            <option <?php if($es_settings->price_format=='2|,|.'){ echo 'selected="selected"'; } ?> value="2|,|.">19.999,00</option>
                            <option <?php if($es_settings->price_format=='0||'){ echo 'selected="selected"'; } ?> value="0|| ">19 999</option>
                        </select>
                    </div>
                    <div class="es_settings_field clearFix">
                        <span><?php _e( "Currency sign place", "es-plugin" ); ?>:</span>
                        <label class="<?php if($es_settings->currency_sign_place=='before'){ echo 'active'; } ?>"><input type="radio" value="before" <?php if($es_settings->currency_sign_place=='before'){ echo 'checked="checked"'; } ?> name="currency_sign_place" /><?php _e( "Before price", "es-plugin" ); ?></label>
                        <label class="<?php if($es_settings->currency_sign_place=='after'){ echo 'active'; } ?>"><input type="radio" value="after" <?php if($es_settings->currency_sign_place=='after'){ echo 'checked="checked"'; } ?> name="currency_sign_place" /><?php _e( "After price", "es-plugin" ); ?></label>
                    </div>
                </div>
                <div id="es_sharing" class="es_tabs_content_in clearFix">
                    <div class="es_settings_field clearFix">
                        <small></small>
                        <span>Twitter</span>
                        <label class="<?php if($es_settings->twitter_link=='1'){ echo 'active'; } ?>"><input type="radio" value="1" <?php if($es_settings->twitter_link=='1'){ echo 'checked="checked"'; } ?> name="twitter_link" /><?php _e( "Yes", "es-plugin" ); ?></label>
                        <label class="<?php if($es_settings->twitter_link=='0'){ echo 'active'; } ?>"><input type="radio" value="0" <?php if($es_settings->twitter_link=='0'){ echo 'checked="checked"'; } ?> name="twitter_link" /><?php _e( "No", "es-plugin" ); ?></label>
                    </div>
                    <div class="es_settings_field clearFix">
                        <small></small>
                        <span>Facebook</span>
                        <label class="<?php if($es_settings->facebook_link=='1'){ echo 'active'; } ?>"><input type="radio" value="1" <?php if($es_settings->facebook_link=='1'){ echo 'checked="checked"'; } ?> name="facebook_link" /><?php _e( "Yes", "es-plugin" ); ?></label>
                        <label class="<?php if($es_settings->facebook_link=='0'){ echo 'active'; } ?>"><input type="radio" value="0" <?php if($es_settings->facebook_link=='0'){ echo 'checked="checked"'; } ?> name="facebook_link" /><?php _e( "No", "es-plugin" ); ?></label>
                    </div>
                    <div class="es_settings_field clearFix">
                        <small></small>
                        <span>Google+</span>
                        <label class="<?php if($es_settings->google_plus_link=='1'){ echo 'active'; } ?>"><input type="radio" value="1" <?php if($es_settings->google_plus_link=='1'){ echo 'checked="checked"'; } ?> name="google_plus_link" /><?php _e( "Yes", "es-plugin" ); ?></label>
                        <label class="<?php if($es_settings->google_plus_link=='0'){ echo 'active'; } ?>"><input type="radio" value="0" <?php if($es_settings->google_plus_link=='0'){ echo 'checked="checked"'; } ?> name="google_plus_link" /><?php _e( "No", "es-plugin" ); ?></label>
                    </div>
                    <div class="es_settings_field clearFix">
                        <small></small>
                        <span>LinkedIn</span>
                        <label class="<?php if($es_settings->linkedin_link=='1'){ echo 'active'; } ?>"><input type="radio" value="1" <?php if($es_settings->linkedin_link=='1'){ echo 'checked="checked"'; } ?> name="linkedin_link" /><?php _e( "Yes", "es-plugin" ); ?></label>
                        <label class="<?php if($es_settings->linkedin_link=='0'){ echo 'active'; } ?>"><input type="radio" value="0" <?php if($es_settings->linkedin_link=='0'){ echo 'checked="checked"'; } ?> name="linkedin_link" /><?php _e( "No", "es-plugin" ); ?></label>
                    </div>
                    <div class="es_settings_field clearFix">
                        <small></small>
                        <span><?php _e( "PDF flyer", "es-plugin" ); ?>:</span>
                        <label class="<?php if($es_settings->pdf_player=='1'){ echo 'active'; } ?>"><input type="radio" value="1" <?php if($es_settings->pdf_player=='1'){ echo 'checked="checked"'; } ?> name="pdf_player" /><?php _e( "Yes", "es-plugin" ); ?></label>
                        <label class="<?php if($es_settings->pdf_player=='0'){ echo 'active'; } ?>"><input type="radio" value="0" <?php if($es_settings->pdf_player=='0'){ echo 'checked="checked"'; } ?> name="pdf_player" /><?php _e( "No", "es-plugin" ); ?></label>
                    </div>
                </div>
                <div id="es_map_view" class="es_tabs_content_in clearFix" style="display: block;">
                    <div class="boxSizing es_gmap_lists">
                        <div class="es-icon-settings-fields">
                            <div class="es_gmap_settings_field clearFix">
                                <span class="label-span"><?php _e('Icons color', 'es-plugin'); ?>:</span>
                                <div class="select-wrapper pink-cat-select">
                                    <img src="<?php echo plugins_url('/images/ColorSelector-pink.png', __FILE__); ?>">
                                    <select name="pink_categories_list[]" multiple="multiple"
                                            class="mult-sel">
                                        <option value="none"><?php echo __('-- None --', 'es-plugin'); ?></option>
                                        <?php foreach ($prop_categories as $category) { ?>
                                            <?php $category_color = es_get_category_meta($category->term_id, 'gmap_icon_color'); ?>
                                            <option
                                                value="<?php echo $category->term_id; ?>" <?php selected('pink', $category_color); ?>><?php echo $category->name; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="select-wrapper blue-cat-select">
                                    <img src="<?php echo plugins_url('/images/ColorSelector-blue.png', __FILE__); ?>">
                                    <select name="blue_categories_list[]" multiple="true" class="mult-sel">
                                        <option value="none"><?php echo __('-- None --', 'es-plugin'); ?></option>
                                        <?php foreach ($prop_categories as $category) { ?>
                                            <?php $category_color = es_get_category_meta($category->term_id, 'gmap_icon_color'); ?>
                                            <option
                                                value="<?php echo $category->term_id; ?>" <?php selected('blue', $category_color); ?>><?php echo $category->name; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="select-wrapper green-cat-select">
                                    <img src="<?php echo plugins_url('/images/ColorSelector-green.png', __FILE__); ?>">
                                    <select name="green_categories_list[]" multiple="true" class="mult-sel">
                                        <option value="none"><?php echo __('-- None --', 'es-plugin'); ?></option>
                                        <?php foreach ($prop_categories as $category) { ?>
                                            <?php $category_color = es_get_category_meta($category->term_id, 'gmap_icon_color'); ?>
                                            <option
                                                value="<?php echo $category->term_id; ?>" <?php selected('green', $category_color); ?>><?php echo $category->name; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="es_gmap_settings_field clearFix">
                            <span class="label-span"><?php _e('Icons type', 'es-plugin'); ?>:</span>
                            <div class="select-wrapper house-wrapper house-cat-select">
                                <img id='img-house' src="<?php echo plugins_url('/images/marker-pink.png', __FILE__); ?>">
                                <select name="house_type_categories_list[]" multiple="true"
                                        class=" mult-sel">
                                    <option value="none"><?php echo __('-- None --', 'es-plugin'); ?></option>
                                    <?php foreach ($prop_categories as $category) { ?>
                                        <?php $category_type = es_get_category_meta($category->term_id, 'gmap_icon_type'); ?>
                                        <option
                                            value="<?php echo $category->term_id; ?>" <?php selected('house', $category_type); ?>><?php echo $category->name; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="select-wrapper flag-wrapper flag-cat-select">
                                <img id='img-flag' src="<?php echo plugins_url('/images/flag-pink.png', __FILE__); ?>">
                                <select name="flag_type_categories_list[]" multiple="true"
                                        class="mult-sel">
                                    <option value="none"><?php echo __('-- None --', 'es-plugin'); ?></option>
                                    <?php foreach ($prop_categories as $category) { ?>
                                        <?php $category_type = es_get_category_meta($category->term_id, 'gmap_icon_type'); ?>
                                        <option
                                            value="<?php echo $category->term_id; ?>" <?php selected('flag', $category_type); ?>><?php echo $category->name; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="select-wrapper point-wrapper point-cat-select">
                                <img id='img-point' src="<?php echo plugins_url('/images/circle-pink.png', __FILE__); ?>">
                                <select name="point_type_categories_list[]" multiple="true"
                                        class="mult-sel">
                                    <option value="none"><?php echo __('-- None --', 'es-plugin'); ?></option>
                                    <?php foreach ($prop_categories as $category) { ?>
                                        <?php $category_type = es_get_category_meta($category->term_id, 'gmap_icon_type'); ?>
                                        <option
                                            value="<?php echo $category->term_id; ?>" <?php selected('point', $category_type); ?>><?php echo $category->name; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="gmap-extra-set-wrapper">
                            <h3><?php echo __('Extra settings', 'es-plugin'); ?></h3>
                            <div class="gmap-ext-set"><span><?php echo __('Height', 'es-plugin'); ?></span><input type="text" name="gmap_settings_height" class="gmap-extra-set-field" size="5" value="<?php echo $gmap_height; ?>"><span class="px-wrapper">px</span></div>
                            <div class="gmap-ext-set"><span><?php echo __('Number of listings to show', 'es-plugin'); ?></span><input type="text" name="gmap_settings_count" class="gmap-extra-set-field" size="5" value="<?php echo $gmap_markers_limit; ?>"></div>
                            <div class="gmap-ext-set"><span><?php echo __('Map zoom level (default value is 12)', 'es-plugin'); ?></span><input type="text" name="gmap_settings_zoom" class="gmap-extra-set-field" size="5" value="<?php echo $gmap_markers_zoom; ?>"></div>
                        </div>
                    </div>
                    <!-- Information of Shortcodes going here-->
                    <div class="boxSizing es_gmap_lists_info">
                        <h2><?php echo __('Shortcodes', 'es-plugin'); ?></h2>
                        <h4><?php echo __('Please create pages with shortcodes below to display map on your website', 'es-plugin'); ?></h4>
                        <ul class="shortcode-info-list">
                            <li>
                                <div class="shortcode-name"><?php echo '[es_property_map]'; ?></div>
                                <div class="shortcode-description">- <?php echo __('All properties', 'es-plugin'); ?></div>
                            </li>
                            <li>
                                <div class="shortcode-name"><?php echo '[es_property_map type="for-rent"]'; ?></div>
                                <div class="shortcode-description">- <?php echo __('Properties for rent', 'es-plugin'); ?></div>
                            </li>
                            <li>
                                <div class="shortcode-name"><?php echo '[es_property_map type="for-sale"]'; ?></div>
                                <div class="shortcode-description">- <?php echo __('Properties for sale', 'es-plugin'); ?></div>
                            </li>
                            <li>
                                <div class="shortcode-name"><?php echo '[es_property_map prop_id="12, 24, 26"]'; ?></div>
                                <div class="shortcode-description">- <?php echo __('Specific properties', 'es-plugin'); ?></div>
                            </li>
                        </ul>
                    </div>
                    <!-- Information of Shortcodes ending here-->
                </div>
            </div>
        </div>
</div>
</form>
</div>
