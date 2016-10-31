<?php
/**
 *
 * @param type $atts
 * @return type
 */
function es_my_listing( $atts ) {
    $attsArray = shortcode_atts( array(
        'layout' => ''
    ), $atts );
    $esLayout = $attsArray['layout'];
    ob_start();
    include('includes/es_my_listing.php');
    return ob_get_clean();
}
add_shortcode( 'es_my_listing', 'es_my_listing' );

/**
 *
 * @param type $atts
 * @return type
 */
function es_my_listing_special( $atts ) {
    $attsArray = shortcode_atts( array(
        'layout' => ''
    ), $atts );
    $esLayout = $attsArray['layout'];
    ob_start();
    get_list_special('WHERE prop_pub_unpub=1 AND prop_price >=220000', 'ORDER BY prop_price,prop_id DESC', 5,  $esLayout);
    return ob_get_clean();
}
add_shortcode( 'es_my_listing_special', 'es_my_listing_special' );

/**
 *
 * @param type $atts
 * @return type
 */
function es_agent_property_listing( $atts ) {
    $attsArray = shortcode_atts( array(
        'layout' => ''
    ), $atts );
    $esLayout = $attsArray['layout'];
    ob_start();
    $author_page = 1;
    include('includes/es_my_listing.php');
    return ob_get_clean();
}
add_shortcode( 'es_agent_property_listing', 'es_agent_property_listing' );
/**
 *
 * @param type $atts
 * @return type
 */
function es_category_property_listing( $atts ) {
    $attsArray = shortcode_atts( array(
        'layout' => ''
    ), $atts );
    $esLayout = $attsArray['layout'];
    ob_start();
    $category_page = 1;
    include('includes/es_my_listing.php');
    return ob_get_clean();
}
add_shortcode( 'es_category_property_listing', 'es_category_property_listing' );
/**
 *
 * @param type $atts
 * @return type
 */
function es_search( $atts ) {
    $attsArray = shortcode_atts( array(
        'layout' => ''
    ), $atts );
    $esLayout = $attsArray['layout'];
    ob_start();
    $search_page = 1;
    include('includes/es_my_listing.php');
    return ob_get_clean();
}
add_shortcode( 'es_search', 'es_search' );
function es_login() {
    ob_start();
    include('includes/es_login.php');
    return ob_get_clean();
}
add_shortcode( 'es_login', 'es_login' );
function es_agents_listing() {
    ob_start();
    $theme = wp_get_theme();
    if ( $theme['Name'] == "Estatik Trendy Theme" ) {
        include('includes/es_agents_trendy.php');
        return;
    }
    include('includes/es_agents.php');
    return ob_get_clean();
}
add_shortcode( 'es_agents', 'es_agents_listing' );
function es_latest_props( $atts ) {
    $attsArray = shortcode_atts( array(
        'layout' => ''
    ), $atts );
    $esLayout = $attsArray['layout'];
    ob_start();
    get_list('WHERE prop_pub_unpub=1', 'ORDER BY prop_id DESC', $esLayout);
    // include('includes/es_latest_props.php');
    return ob_get_clean();
}
add_shortcode( 'es_latest_props', 'es_latest_props' );
function es_featured_props( $atts ) {
    $attsArray = shortcode_atts( array(
        'layout' => ''
    ), $atts );
    $esLayout = $attsArray['layout'];
    ob_start();
    get_list('WHERE prop_pub_unpub=1 AND prop_featured=1', 'ORDER BY prop_id DESC', $esLayout);
    // include('includes/es_featured_props.php');
    return ob_get_clean();
}
add_shortcode( 'es_featured_props', 'es_featured_props' );
function es_cheapest_props( $atts ) {
    $attsArray = shortcode_atts( array(
        'layout' => ''
    ), $atts );
    $esLayout = $attsArray['layout'];
    ob_start();
    get_list('WHERE prop_pub_unpub = 1', 'ORDER BY prop_price ASC', $esLayout);
    // include('includes/es_cheapest_props.php');
    return ob_get_clean();
}
add_shortcode( 'es_cheapest_props', 'es_cheapest_props' );
function es_profile() {
    ob_start();
    include('includes/es_profile.php');
    return ob_get_clean();
}
add_shortcode( 'es_profile', 'es_profile' );
function es_prop_management() {
    ob_start();
    include('includes/es_prop_management.php');
    return ob_get_clean();
}
add_shortcode( 'es_prop_management', 'es_prop_management' );
function es_register() {
    ob_start();
    include('includes/es_register.php');
    return ob_get_clean();
}
add_shortcode( 'es_register', 'es_register' );
function es_single_property() {
    $theme = wp_get_theme();
    
    if ( $theme['Name'] == "Estatik Trendy Theme" ) {
    
        include('includes/es_prop_single_trendy.php');
        
    } else if ( isset($_GET['pdf']) ) {
    
        include(PATH_DIR.'front_templates/tcpdf/examples/es_prop_pdf.php');
        
    } else {
    
        include('includes/es_prop_single.php');
        
    }
    
    return ob_get_clean();
    
}
add_shortcode( 'es_single_property', 'es_single_property' );
// function es_single_property_trendy() {
//    if(isset($_GET['pdf'])){
//        include(PATH_DIR.'front_templates/tcpdf/examples/es_prop_pdf.php');
//    }else{
//        include('includes/es_prop_single_trendy.php');
//    }
//    return ob_get_clean();
//}
//add_shortcode( 'es_single_property_trendy', 'es_single_property_trendy' );
function es_property_map($atts){
    ob_start();
    include('includes/es_property_map.php');
    return ob_get_clean();
}
add_shortcode( 'es_property_map', 'es_property_map' );
function es_new_category_shortcode($atts){
    global $wpdb;
    $attsArray = shortcode_atts( array(
        'layout' => ''
    ), $atts );
    $esLayout = $attsArray['layout'];
    ob_start();
    if ( !empty($atts['category']) ) {
        $category_title = strtolower($atts['category']);
        $sql = $wpdb->prepare(
            "SELECT `cat_id` FROM {$wpdb->prefix}estatik_manager_categories WHERE `cat_title`='%s'",
            $category_title);
        $category_id = $wpdb->get_row($sql);
        $where = "WHERE `prop_category` LIKE '%{$category_id->cat_id}%' AND `prop_pub_unpub`=1";
        // $properties = $wpdb->get_results("SELECT prop_id, prop_category
        // 	FROM {$wpdb->prefix}estatik_properties WHERE `prop_category`
        // 	LIKE '%$category_id%' AND `prop_pub_unpub`=1");
        // if ($properties) {
        // 	foreach ($properties as $prop) {
        // 		if ($prop_cat = unserialize($prop->prop_category)) {
        // 			if (in_array($category_id, $prop_cat)) {
        // 				$ids[] = $prop->prop_id;
        // 			}
        // 		}
        // 	}
        // 	if ($ids) {
        // 		$where = "WHERE `prop_id` IN (".implode(',', $ids).") AND `prop_pub_unpub`=1";
        // 	}
        // }
    } else if (!empty($atts['type'])){
        $type = strtolower($atts['type']);
        $sql = $wpdb->prepare(
            "SELECT `type_id` FROM {$wpdb->prefix}estatik_manager_types WHERE `type_title`='%s'",
            $type);
        $type_id = $wpdb->get_row($sql);
        if ( !empty($type_id) ) {
            $where = "WHERE `prop_type`='{$type_id->type_id}' AND `prop_pub_unpub`=1";
        } else{
            $where = '';
        }
    } else if (!empty($atts['status'])){
        $category_title = strtolower($atts['status']);
        $sql = $wpdb->prepare(
            "SELECT `status_id` FROM {$wpdb->prefix}estatik_manager_status WHERE `status_title`='%s'",
            $category_title);
        $category_id = $wpdb->get_row($sql)->status_id;
        $where = "WHERE `prop_status` LIKE '%$category_id%' AND `prop_pub_unpub`=1";
    }
    // else if (!empty($atts['type'])){
    // 	$category_title = strtolower($atts['type']);
    // 	$sql = $wpdb->prepare(
    // 		"SELECT `type_id` FROM {$wpdb->prefix}estatik_manager_type WHERE `type_title`='%s'",
    // 		$category_title);
    // 	$category_id = $wpdb->get_row($sql)->type_id;
    // 	$where = "WHERE `prop_type` LIKE '%$category_id%' AND `prop_pub_unpub`=1";
    // }
    get_list($where, 'ORDER BY prop_id DESC', $esLayout);
    // include('includes/es_category.php');
    return ob_get_clean();
}
add_shortcode('es_category', 'es_new_category_shortcode');
function es_my_listing_city( $atts ) {
    global $wpdb;
    $attsArray = shortcode_atts( array(
        'layout' => ''
    ), $atts );
    $esLayout = $attsArray['layout'];
    ob_start();
    if ( !empty($atts['city']) ) {
        $sql = $wpdb->prepare(
            "SELECT city_id FROM {$wpdb->prefix}estatik_manager_cities WHERE city_title='%s'",
            $atts['city']);
        $city_id = $wpdb->get_row($sql);
        if ( isset($city_id->city_id) ) {
            get_list("WHERE `city_id`='{$city_id->city_id}' AND `prop_pub_unpub` = 1",
                'ORDER BY prop_id DESC', $esLayout);
        }
    }
    return ob_get_clean();
}
add_shortcode( 'es_city', 'es_my_listing_city' );
function es_my_listing_trendy( $atts ) {
    global $wpdb;
    $attsArray = shortcode_atts( array(
        'layout' => ''
    ), $atts );
    $esLayout = $attsArray['layout'];
    ob_start();
    $where = isset($atts['where']) ? $atts['where'] : '';
    $order = isset($atts['order']) ? $atts['order'] : '';
    $layout = isset($atts['layout']) ? $atts['layout'] : '';
    get_list_trendy($where, $order, $layout);
    return ob_get_clean();
}
add_shortcode( 'es_my_listing_trendy', 'es_my_listing_trendy' );
/**
 * Shortcode for Listing by Agent name.
 *
 * @global type $wpdb
 * @param type $atts
 * @return string
 */
function es_listings_agent_name( $atts ) {
    global $wpdb;
    $attsArray = shortcode_atts( array(
        'layout' => ''
    ), $atts );
    $esLayout = $attsArray['layout'];
    if (!empty($atts['name'])) {
        $agent_id = $wpdb->get_var( $wpdb->prepare(
            "
            SELECT agent_id
            FROM " . $wpdb->prefix . "estatik_agents
            WHERE agent_name LIKE %s LIMIT 1
        ",
            '%' . $atts['name'] . '%')
        );
        if (!empty($agent_id)) {
            ob_start();
            get_list('WHERE agent_id=' . $agent_id, 'ORDER BY prop_id DESC', $esLayout);
            return ob_get_clean();
        }
    }
    return '';
}
add_shortcode('es_listings_agent_name', 'es_listings_agent_name');
