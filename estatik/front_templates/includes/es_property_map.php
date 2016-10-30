<?php
global $wpdb;
if ( !wp_script_is( 'es-property-map', 'enqueued' ) ) {
    wp_enqueue_script('es-property-map', DIR_URL . 'front_templates/js/es_property_map.js');
}
extract(shortcode_atts(array(
    'type' => 'all',
    'prop_id' => false
), $atts));
$limit = get_option('gmap_markers_limit');
if(empty($limit)){
    $limit = 20;
}
if (!empty($type) && $type != 'all') {
    $args = array(
        'post_type' => 'properties',
        'post_status' => 'publish',
        'tax_query' => array(
            array(
                'taxonomy' => 'property_category',
                'field' => 'slug',
                'terms' => $type
            )
        ),
        'posts_per_page' => $limit
    );
    $term_object = get_term_by('slug', $type, 'property_category');
    $term_id = $term_object->term_id;
    $icon_color = es_get_category_meta($term_id, 'gmap_icon_color');
    $icon_type = es_get_category_meta($term_id, 'gmap_icon_type');
    if (empty($icon_type)) {
        $icon_type = 'house';
    }
    if (empty($icon_color)) {
        $icon_color = 'pink';
    }
    if ( !wp_style_is( 'estatik_gmap_style', 'enqueued' ) ) {
        wp_enqueue_style('estatik_gmap_style', DIR_URL . 'front_templates/css/es_property_gmap.css');
    }
    $query = new WP_Query($args);
    if ($query->have_posts()):
        $i = 0;
        $prop_list = array();
        while ($query->have_posts()): $query->the_post();
            $prop_id = get_the_ID();
            $properties_table = $wpdb->prefix . 'estatik_properties';
            $prop_sql = $wpdb->prepare("SELECT * FROM $properties_table WHERE prop_id = %d", $prop_id);
            $prop_array = $wpdb->get_row($prop_sql);
            $dimension_sql = "SELECT dimension_title FROM " . $wpdb->prefix . "estatik_manager_dimension WHERE dimension_status = 1";
            $dimension = $wpdb->get_row($dimension_sql);
            if (isset($prop_array) && !empty($prop_array)) {
                $prop_meta_table = $wpdb->prefix . 'estatik_properties_meta';
                $prop_meta_sql = $wpdb->prepare("SELECT * FROM $prop_meta_table WHERE prop_id = %d AND prop_meta_key = %s", $prop_id, 'images');
                $prop_meta_array = $wpdb->get_row($prop_meta_sql);
                $prop_longitude = $prop_array->prop_longitude;
                $prop_latitude = $prop_array->prop_latitude;
                $prop_title = get_the_title($prop_id) . ' | ' . strtoupper($term_object->name);
                $prop_address = $prop_array->prop_address;
                $prop_price = $prop_array->prop_price;
                $prop_area = $prop_array->prop_area . ' ' . $dimension->dimension_title;
                $prop_bedrooms = $prop_array->prop_bedrooms;
                $prop_bathrooms = $prop_array->prop_bathrooms;
                $prop_link = get_permalink($prop_id);
                $prop_image_unserialize = maybe_unserialize($prop_meta_array->prop_meta_value);
                $es_settings = es_front_settings();
                $currency_sign_ex = explode(",", $es_settings->default_currency);
                if (count($currency_sign_ex) == 1) {
                    $currency_sign = $currency_sign_ex[0];
                } else {
                    $currency_sign = $currency_sign_ex[1];
                }
                $image_sql = "SELECT prop_meta_value FROM " . $wpdb->prefix . "estatik_properties_meta WHERE prop_id = " . $prop_id . " AND prop_meta_key = 'images'";
                $uploaded_images = $wpdb->get_row($image_sql);
                $uploaded_images_count = "0";
                if (!empty($uploaded_images)) {
                    $upload_image_data = unserialize($uploaded_images->prop_meta_value);
                    $uploaded_images_count = count($upload_image_data);
                }
                if (isset($upload_image_data[0]) && !empty($upload_image_data[0])) {
                    $upload_dir = '';
                                                    if(strpos($upload_image_data[0], "http://")>-1 || strpos($upload_image_data[0], "https://")>-1) {
                                                    } else {
                                                        $upload_dir = wp_upload_dir();
                                                        }
                    $list_image_name = explode("/", $upload_image_data[0]);
                    $list_image_name = end($list_image_name);
                    $list_image_path = str_replace($list_image_name, "", $upload_image_data[0]);
                    $image_url = $upload_dir['baseurl'] . $list_image_path . $list_image_name;
                }
                if ($prop_bedrooms == 1) {
                    $prop_bedrooms = $prop_bedrooms . __(' bed');
                } else {
                    $prop_bedrooms = $prop_bedrooms . __(' beds');
                }
                if ($prop_bathrooms == 1) {
                    $prop_bathrooms = $prop_bathrooms . __(' bath');
                } else {
                    $prop_bathrooms = $prop_bathrooms . __(' baths');
                }
                $prop_list[$i] = array(
                    'id' => $prop_id,
                    'title' => $prop_title,
                    'longitude' => $prop_longitude,
                    'latitude' => $prop_latitude,
                    'address' => $prop_address,
                    'currency' => $currency_sign,
                    'price' => $prop_price,
                    'image' => $image_url,
                    'area' => $prop_area,
                    'bedrooms' => $prop_bedrooms,
                    'bathrooms' => $prop_bathrooms,
                    'link' => $prop_link,
                    'color' => $icon_color,
                    'img' => plugins_url('/gmap_img/'. $icon_color . '/' . $icon_type . '.png', __FILE__)
                );
                $i++;
            }
        endwhile;
    endif;
    if (empty($icon_color)) {
        $icon_color = 'pink';
    }
    $gmag_zoom = get_option('gmap_settings_zoom');
    if ($prop_list && !empty($prop_list)) {
        $prop_info = json_encode($prop_list); ?>
        <script>var data = <?php echo $prop_info; ?></script>
        <script>var icon = '<?php echo plugins_url('/gmap_img/'. $icon_color . '/' . $icon_type . '.png', __FILE__); ?>'</script>
        <script>var zoom = <?php echo $gmag_zoom ?></script>
        <?php
    }
}
elseif(isset($prop_id) && !empty($prop_id)){
    $prop_id = preg_replace('/\s+/', '', $prop_id);
    $props = array();
    $props = explode(',', $prop_id);
    $args = array(
        'post_type' => 'properties',
        'post_status' => 'publish',
        'post__in' => $props,
        'posts_per_page' => $limit
    );
    $icon_type = get_option('default_gmap_type');
    if (!$icon_type || empty($icon_type)) {
        $icon_type = 'house';
    }
    $icon_color = get_option('default_gmap_color');
    if (empty($icon_color)) {
        $icon_color = 'pink';
    }
    if ( !wp_style_is( 'estatik_gmap_style', 'enqueued' ) ) {
        wp_enqueue_style('estatik_gmap_style', DIR_URL . 'front_templates/css/es_property_gmap.css');
    }
    $query = new WP_Query($args);
    if ($query->have_posts()):
        $i = 0;
        $prop_list = array();
        while ($query->have_posts()): $query->the_post();
            $prop_id = get_the_ID();
            $properties_table = $wpdb->prefix . 'estatik_properties';
            $prop_sql = $wpdb->prepare("SELECT * FROM $properties_table WHERE prop_id = %d", $prop_id);
            $prop_array = $wpdb->get_row($prop_sql);
            $term_id = $prop_array->prop_category;
            $term_object = get_term_by('id', $term_id, 'property_category');
            $icon_color = es_get_category_meta($term_id, 'gmap_icon_color');
            $icon_type = es_get_category_meta($term_id, 'gmap_icon_type');
            if (empty($icon_color)) {
                $icon_color = 'pink';
            }
            $dimension_sql = "SELECT dimension_title FROM " . $wpdb->prefix . "estatik_manager_dimension WHERE dimension_status = 1";
            $dimension = $wpdb->get_row($dimension_sql);
            if (isset($prop_array) && !empty($prop_array)) {
                $prop_meta_table = $wpdb->prefix . 'estatik_properties_meta';
                $prop_meta_sql = $wpdb->prepare("SELECT * FROM $prop_meta_table WHERE prop_id = %d AND prop_meta_key = %s", $prop_id, 'images');
                $prop_meta_array = $wpdb->get_row($prop_meta_sql);
                $prop_longitude = $prop_array->prop_longitude;
                $prop_latitude = $prop_array->prop_latitude;
                $prop_title = get_the_title($prop_id) . ' | ' . strtoupper($term_object->name);
                $prop_address = $prop_array->prop_address;
                $prop_price = $prop_array->prop_price;
                $prop_area = $prop_array->prop_area . ' ' . $dimension->dimension_title;
                $prop_bedrooms = $prop_array->prop_bedrooms;
                $prop_bathrooms = $prop_array->prop_bathrooms;
                $prop_link = get_permalink($prop_id);
                $prop_image_unserialize = maybe_unserialize($prop_meta_array->prop_meta_value);
                $es_settings = es_front_settings();
                $currency_sign_ex = explode(",", $es_settings->default_currency);
                if (count($currency_sign_ex) == 1) {
                    $currency_sign = $currency_sign_ex[0];
                } else {
                    $currency_sign = $currency_sign_ex[1];
                }
                $image_sql = "SELECT prop_meta_value FROM " . $wpdb->prefix . "estatik_properties_meta WHERE prop_id = " . $prop_id . " AND prop_meta_key = 'images'";
                $uploaded_images = $wpdb->get_row($image_sql);
                $uploaded_images_count = "0";
                if (!empty($uploaded_images)) {
                    $upload_image_data = unserialize($uploaded_images->prop_meta_value);
                    $uploaded_images_count = count($upload_image_data);
                }
                if (isset($upload_image_data[0]) && !empty($upload_image_data[0])) {
                     $upload_dir = '';
                                if(strpos($upload_image_data[0], "http://")>-1 || strpos($upload_image_data[0], "https://")>-1) {
                                } else {
                                    $upload_dir = wp_upload_dir();
                                    }
                    $list_image_name = explode("/", $upload_image_data[0]);
                    $list_image_name = end($list_image_name);
                    $list_image_path = str_replace($list_image_name, "", $upload_image_data[0]);

                    $image_url = $upload_dir['baseurl'] . $list_image_path . $list_image_name;
                }
                if ($prop_bedrooms == 1) {
                    $prop_bedrooms = $prop_bedrooms . __(' bed');
                } else {
                    $prop_bedrooms = $prop_bedrooms . __(' beds');
                }
                if ($prop_bathrooms == 1) {
                    $prop_bathrooms = $prop_bathrooms . __(' bath');
                } else {
                    $prop_bathrooms = $prop_bathrooms . __(' baths');
                }
                $prop_list[$i] = array(
                    'id' => $prop_id,
                    'title' => $prop_title,
                    'longitude' => $prop_longitude,
                    'latitude' => $prop_latitude,
                    'address' => $prop_address,
                    'currency' => $currency_sign,
                    'price' => $prop_price,
                    'image' => $image_url,
                    'area' => $prop_area,
                    'bedrooms' => $prop_bedrooms,
                    'bathrooms' => $prop_bathrooms,
                    'link' => $prop_link,
                    'color' => $icon_color,
                    'img' => plugins_url('/gmap_img/'. $icon_color . '/' . $icon_type . '.png', __FILE__)
                );
                $i++;
            }
        endwhile;
    endif;
    $gmag_zoom = get_option('gmap_settings_zoom');
    if ($prop_list && !empty($prop_list)) {
        $prop_info = json_encode($prop_list); ?>
        <script>var data = <?php echo $prop_info; ?>;</script>
        <script>var icon = '<?php echo plugins_url('/gmap_img/'. $icon_color . '/' . $icon_type . '.png', __FILE__); ?>'</script>
        <script>var zoom = '<?php echo $gmag_zoom ?>';</script>
        <?php
    }
}
else {
    $args = array(
        'post_type' => 'properties',
        'post_status' => 'publish',
        'posts_per_page' => $limit
    );
    $icon_type = get_option('default_gmap_type');
    if(empty($icon_type)){
        $icon_type = 'house';
    }
    $icon_color = get_option('default_gmap_color');
    if(empty($icon_color)){
        $icon_color = 'pink';
    }
    if ( !wp_style_is( 'estatik_gmap_style', 'enqueued' ) ) {
        wp_enqueue_style('estatik_gmap_style', DIR_URL . 'front_templates/css/es_property_gmap.css');
    }
    $query = new WP_Query($args);
    if ($query->have_posts()):
        $i = 0;
        $prop_list = array();
        while ($query->have_posts()): $query->the_post();
            $prop_id = get_the_ID();
            $prop_sql = $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}estatik_properties WHERE prop_id = %d", $prop_id);
            $prop_array = $wpdb->get_row($prop_sql);
            if ( !is_object($prop_array) ) continue;
            $term_id = $prop_array->prop_category;
            $term_object = get_term_by('id', $term_id, 'property_category');
            $icon_color = es_get_category_meta($term_id, 'gmap_icon_color');
            $icon_type = es_get_category_meta($term_id, 'gmap_icon_type');
            if (empty($icon_type)) {
                $icon_type = 'house';
            }
            if (empty($icon_color)) {
                $icon_color = 'pink';
            }
            $dimension = $wpdb->get_row(
                "SELECT dimension_title FROM {$wpdb->prefix}estatik_manager_dimension WHERE dimension_status = 1");
            if ( !empty($prop_array) ) {
                $prop_meta_sql = $wpdb->prepare(
                    "SELECT * FROM {$wpdb->prefix}estatik_properties_meta WHERE prop_id = %d", $prop_id);
                $prop_meta_array = $wpdb->get_row($prop_meta_sql);
                $prop_longitude = $prop_array->prop_longitude;
                $prop_latitude = $prop_array->prop_latitude;
                $name = is_object($term_object) ? $term_object->name : '';
                $prop_title = get_the_title($prop_id) . ' | ' . $name;
                $prop_address = $prop_array->prop_address;
                $prop_price = $prop_array->prop_price;
                $prop_area = $prop_array->prop_area;
                $prop_bedrooms = $prop_array->prop_bedrooms;
                $prop_bathrooms = $prop_array->prop_bathrooms;
                $prop_link = get_permalink($prop_id);
                if(isset($prop_meta_array) && is_object($prop_meta_array)) {
                    $prop_image_unserialize = maybe_unserialize($prop_meta_array->prop_meta_value);
                }
                $es_settings = es_front_settings();
                $currency_sign_ex = explode(",", $es_settings->default_currency);
                if(count($currency_sign_ex)==1){
                    $currency_sign = $currency_sign_ex[0];
                }else {
                    $currency_sign = $currency_sign_ex[1];
                }
                $image_sql = "SELECT prop_meta_value FROM ".$wpdb->prefix."estatik_properties_meta WHERE prop_id = ".$prop_id." AND prop_meta_key = 'images'";
                $uploaded_images = $wpdb->get_row($image_sql);
                $uploaded_images_count ="0";
                if(!empty($uploaded_images)){
                    $upload_image_data = unserialize($uploaded_images->prop_meta_value);
                    $uploaded_images_count = count($upload_image_data);
                }
                if(isset($upload_image_data[0]) && !empty($upload_image_data[0])) {
                    $upload_dir = '';
                                                    if(strpos($upload_image_data[0], "http://")>-1 || strpos($upload_image_data[0], "https://")>-1) {
                                                    } else {
                                                        $upload_dir = wp_upload_dir();
                                                        }
                    $list_image_name = explode("/", $upload_image_data[0]);
                    $list_image_name = end($list_image_name);
                    $list_image_path = str_replace($list_image_name, "", $upload_image_data[0]);
                    $image_url = $upload_dir['baseurl'] . $list_image_path . $list_image_name;
                } else {
                    $image_url = get_template_directory_uri() . '/images/placeholder.png';
                }
                if($prop_bedrooms == 1){
                    $prop_bedrooms = $prop_bedrooms . __(' bed');
                }
                else{
                    $prop_bedrooms = $prop_bedrooms . __(' beds');
                }
                if($prop_bathrooms == 1){
                    $prop_bathrooms = $prop_bathrooms . __(' bath');
                }
                else{
                    $prop_bathrooms = $prop_bathrooms . __(' baths');
                }
                $prop_list[$i] = array(
                    'id' => $prop_id,
                    'title' => $prop_title,
                    'longitude' => $prop_longitude,
                    'latitude' => $prop_latitude,
                    'address' => $prop_address,
                    'currency' => $currency_sign,
                    'price' => $prop_price,
                    'image' => $image_url,
                    'area' => $prop_area,
                    'bedrooms' => $prop_bedrooms,
                    'bathrooms' => $prop_bathrooms,
                    'link' => $prop_link,
                    'color' => $icon_color,
                    'img' => plugins_url('/gmap_img/'. $icon_color . '/' . $icon_type . '.png', __FILE__)
                );
                $i++;
            }
        endwhile;
    endif;
    $gmag_zoom = get_option('gmap_settings_zoom');
    if ($prop_list && !empty($prop_list)) {
        $prop_info = json_encode($prop_list); ?>
        <script>var data = <?php echo $prop_info; ?></script>
        <script>var icon = '<?php echo plugins_url('/gmap_img/'. $icon_color . '/' . $icon_type . '.png', __FILE__); ?>'</script>
        <script>var zoom = '<?php echo $gmag_zoom ?>';</script>
        <?php
    }
}
$gmap_height = get_option('gmap_height');
?>
<script>var height = '<?php echo $gmap_height; ?>'</script>
<div class="es-gmap-wrapper">
    <div id="es-map-canvas"></div>
</div>