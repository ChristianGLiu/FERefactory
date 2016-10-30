<?php
global $es_settings;
function es_head()
{
    global $wpdb, $es_settings;
    $upload_dir = wp_upload_dir();
    $head_html='';
    if(is_singular('properties')){
        $sql = 'SELECT * FROM '.$wpdb->prefix.'estatik_properties WHERE prop_id = '.get_the_id().' order by prop_id desc';
        $es_prop_single_result = $wpdb->get_results($sql);
        $es_prop_single = $es_prop_single_result[0];
        $currency_sign="";
        if($es_settings->default_currency!="") {
            $prop_currency = $wpdb->get_row( 'SELECT currency_title FROM '.$wpdb->prefix.'estatik_manager_currency WHERE currency_id = "'.$es_settings->default_currency.'"');
            if(!empty($prop_currency)){
                $currency_sign_ex = explode(",", $prop_currency->currency_title);
                if(count($currency_sign_ex)==1){
                    $currency_sign = $currency_sign_ex[0];
                }else {
                    $currency_sign = $currency_sign_ex[1];
                }
            }
        }
        $prop_address = ($es_prop_single->prop_address!="") ? __('Address', 'es-plugin') .':'.$es_prop_single->prop_address.", " : "";
        $currency_sign_place_before = ($es_settings->currency_sign_place=='before') ? $currency_sign : "";
        $currency_sign_place_after = ($es_settings->currency_sign_place=='after') ? $currency_sign : "";
        $prop_bedrooms = ($es_prop_single->prop_bedrooms!=0) ? " " . __('Beds', 'es-plugin') . ":".$es_prop_single->prop_bedrooms.", " : "";
        $prop_bathrooms = ($es_prop_single->prop_bathrooms!=0) ? __("Bathrooms", 'es-plugin').':'.$es_prop_single->prop_bathrooms.", " : "";
        $prop_area = ($es_prop_single->prop_area!=0) ? __("Area", 'es-plugin').':'.$es_prop_single->prop_area."sq ft, " : "";
        $prop_meta_description = ($es_prop_single->prop_meta_description!="") ? __("Description", 'es-plugin').':'.$es_prop_single->prop_meta_description : "";
        $price_format = explode("|",$es_settings->price_format);
        $head_html.='<meta property="og:title" content="'.$es_prop_single->prop_title.'" />';
        $head_html.='<meta property="og:description" content="'.$prop_address. __('Price', 'es-plugin').': '.$currency_sign_place_before.number_format($es_prop_single->prop_price,$price_format[0],$price_format[1],$price_format[2]).$currency_sign_place_after.$prop_bedrooms.$prop_bathrooms.$prop_area.$prop_meta_description.'" />';
        $image_sql = "SELECT prop_meta_value FROM ".$wpdb->prefix."estatik_properties_meta WHERE prop_id = ".$es_prop_single->prop_id." AND prop_meta_key = 'images'";
        $uploaded_images = $wpdb->get_row($image_sql);
        if(!empty($uploaded_images)){
            $upload_image_data = unserialize($uploaded_images->prop_meta_value);
            if(!empty($upload_image_data)) {
                $single_list_image_name = explode("/",$upload_image_data[0]);
                $single_list_image_name = end($single_list_image_name);
                $single_list_image_path = str_replace($single_list_image_name,"",$upload_image_data[0]);
                $image_url = $single_list_image_path.'list_'.$single_list_image_name;
                $head_html.='<meta property="og:image" content="'.$upload_dir['baseurl'].$image_url.'" />';
            }
        }
    }
    echo $head_html;
    $prop_id = isset($_GET['add_new_prop']) && isset($_GET['prop_id']) ? $_GET['prop_id'] : '';
    $prop_title =  isset($_GET['add_new_prop']) && isset($_GET['type']) ? $_GET['type'] : '';
    $es_addresses = $wpdb->get_results("SELECT prop_address FROM {$wpdb->prefix}estatik_properties
				WHERE prop_pub_unpub = 1 ORDER BY prop_id DESC");
    array_walk($es_addresses, function(&$item) { $item = "'{$item->prop_address}'"; });
    $es_addresses = implode(', ', $es_addresses);
    echo "<script type='text/javascript'>\n
		var prop_id = '$prop_id';\n
        var EstatikApp = { ";
    if(is_singular('properties')){
        $sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}estatik_properties
		        WHERE prop_id = '%d' order by prop_id desc", get_the_id());
        $es_prop_single = $wpdb->get_row($sql);
        $title = str_replace('\'','',$es_prop_single->prop_title);
        echo "prop_latitude: {$es_prop_single->prop_latitude},\n
                  prop_longitude: {$es_prop_single->prop_longitude},\n
                  single_prop_title: '$title',\n";
    }
    echo "availableTags: [$es_addresses],\n
	              dir_url: '".DIR_URL."',\n
	              widgetMapview: {
	              	latitude: 0,
	              	longitude: 0,
	              	mapinfos: []
	              },\n
	              prop_title: '$prop_title',\n
	              prop_singleview_photo_thumb_width: '{$es_settings->prop_singleview_photo_thumb_width}', \n
	        	  speed: 400, \n
	              windowWidth: 0\n
            };
            </script>";
    es_mapview_script();
}
add_action('wp_head','es_head');
function estatik_front_scripts() {
    global $es_settings;
    $map_settings = get_option('gmap_api');
    if($map_settings != 0){
        $map_settings = 1;
    }
    wp_enqueue_style( 'estatik-es_admin_style', DIR_URL . 'front_templates/css/es_admin_style.css');
    wp_enqueue_style( 'estatik-es_admin_responsive', DIR_URL . 'front_templates/css/es_admin_responsive.css');
    wp_enqueue_script('es-es_my_listings_scripts', DIR_URL . 'front_templates/js/es_my_listings_scripts.js' , array( 'jquery' ));
    wp_enqueue_script('es-es_agent_script', DIR_URL . 'front_templates/js/es_agent_script.js' , array( 'jquery' ));
    wp_enqueue_script('es-es_subscription_table', DIR_URL . 'front_templates/js/es_subscription_table.js' , array( 'jquery' ));
    wp_enqueue_style( 'estatik-front-style', DIR_URL . 'front_templates/css/es_front_style.css');
    wp_enqueue_style( 'estatik-theme-style', DIR_URL . 'front_templates/css/es_'.$es_settings->theme_style.'.css');
    wp_enqueue_style( 'estatik-front-responsive-style', DIR_URL . 'front_templates/css/es_front_responsive.css');
    wp_enqueue_script( 'jquery-ui-autocomplete');
    wp_enqueue_script( 'jquery-ui-datepicker');
    wp_enqueue_script( 'jquery-ui-sortable');
    wp_enqueue_style( 'es-jquery-ui-style', DIR_URL . 'admin_template/css/jquery-ui.css');
    //init es_property_map_script
    // if($map_settings == 1) {
    wp_enqueue_script('gmap-api-script', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyAvz0XMOkYmXx2ZdqRTVhx34nMP4AFQfU8');
    // }
    wp_enqueue_script('estatik-front-scripts', DIR_URL . 'front_templates/js/es_front_scripts.js',
        array( 'jquery', 'jquery-ui-autocomplete', 'jquery-ui-datepicker', 'gmap-api-script' ));
    wp_localize_script( 'estatik-front-scripts', 'estatik_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php')));
    if(isset($_GET['add_new_prop'])){
        wp_enqueue_script('es_manager_scripts', DIR_URL . 'front_templates/js/es_manager_scripts.js' , array( 'jquery' ));
        wp_enqueue_script('es_property_scripts', DIR_URL . 'front_templates/js/es_property_scripts.js' ,
            array( 'jquery', 'jquery-ui-sortable' ));
    }
    wp_enqueue_script('es-jquery-bxslider', DIR_URL . 'front_templates/js/jquery.bxslider.min.js' , array( 'jquery' ));
    if(is_singular('properties')){
        wp_enqueue_script('es-sharethis', 'http://w.sharethis.com/button/buttons.js' , array( 'jquery' ));
        wp_enqueue_script('es-magnific', DIR_URL . 'front_templates/js/jquery.magnific-popup.min.js' , array( 'jquery' ));
        wp_enqueue_style( 'es-magnific-style', DIR_URL . 'front_templates/css/all.min.css');
    }
    wp_enqueue_script('gmap-infobox-map', plugins_url('/js/infobox.js', __FILE__));
//    wp_enqueue_script('gmap-infobubble-map', plugins_url('/js/infobubble.js', __FILE__));
    //wp_enqueue_style( 'bootstrap-style', 'http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css');
    //wp_enqueue_script('jquery-min', 'https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js');
    //wp_enqueue_script( 'bootstrap', 'http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js');
    wp_enqueue_script('es-property-map', DIR_URL . 'front_templates/js/es_property_map.js');
    wp_enqueue_style('estatik_gmap_style', DIR_URL . 'front_templates/css/es_property_gmap.css');
}
add_action( 'wp_enqueue_scripts', 'estatik_front_scripts' );
function es_print_scripts() {
    // global $wpdb, $es_settings;
    // $es_addresses = $wpdb->get_results("SELECT prop_address FROM {$wpdb->prefix}estatik_properties
    // 			WHERE prop_pub_unpub = 1 order by prop_id desc");
    echo "<script type='text/javascript'>\n";
    // echo "jQuery(window).load(function(e) { ";
    // if(isset($_GET['add_new_prop'])){
    // 	if(isset($_GET['type'])){
    // 		$href =  $_GET['type'];
    // 		echo "var type ='';
    // 			var media ='';
    // 			jQuery('.es_tabs ul li a').each(function(index, element) {
    // 				var href = jQuery(this).attr('href');
    // 				type = href.replace('#', '');
    // 				if(type=='".$href."'){
    // 					jQuery(this).trigger('click');
    // 				}
    // 			});";
    // 	 } else {
    // 		echo "jQuery('.es_tabs ul li:first-child a').click();";
    // 	 }
    // 	 echo "jQuery('.es_tabs ul li a').click(function(){
    // 			myGeocodeFirst();
    // 		});";
    // }
    // echo "});";
    // echo "jQuery(document).ready(function(e) {
    // 			var pagerWidth = jQuery('#es_prop_single_pager_outer').width()/".$es_settings->prop_singleview_photo_thumb_width.";
    // 			jQuery('.es_prop_single_pics').bxSlider({
    // 			  slideMargin: 0,
    // 			  controls: false,
    // 			  infiniteLoop: false,
    // 			  maxSlides: 1,
    // 			  pagerCustom: '.es_prop_single_pager'
    // 			});
    // 			jQuery('.es_prop_single_pager').bxSlider({
    // 			  slideWidth: ".$es_settings->prop_singleview_photo_thumb_width.",
    // 			  slideMargin: 10,
    // 			  pager: false,
    // 			  infiniteLoop: false,
    // 			  minSlides: parseInt(pagerWidth),
    // 			  maxSlides: parseInt(pagerWidth),
    // 			});
    // 			jQuery('.es_prop_single_pager li a').each(function(index, element) {
    // 				jQuery(this).attr('data-slide-index',index);
    // 			});
    // 		});";
    // if(is_singular('properties')){
    // 	echo "jQuery(window).load(function(e) {
    // 				var navPos = parseInt(jQuery('.es_prop_single_tabs').offset().top);
    // 				var navPosLeft = parseInt(jQuery('.es_prop_single_tabs').offset().left);
    // 				var navWidth = parseInt(jQuery('.es_prop_single_tabs').width());
    // 				jQuery(window).scroll(function(e) {
    // 					if(jQuery(this).scrollTop()>=navPos){
    // 						jQuery('.es_prop_single_tabs').addClass('fixed');
    // 						jQuery('.es_prop_single_tabs').css({'left':navPosLeft+'px','width':navWidth+'px'});
    // 					} else {
    // 						jQuery('.es_prop_single_tabs').removeClass('fixed');
    // 						jQuery('.es_prop_single_tabs').css({'left':'0px','width':'auto'});
    // 					}
    // 				});
    // 			  });
    // 			  jQuery('.es_prop_single_pics').magnificPopup({
    // 				  delegate: 'a',
    // 				  type: 'image',
    // 				  tLoading: 'Loading image #%curr%...',
    // 				  mainClass: 'mfp-img-mobile',
    // 				  gallery: {
    // 					enabled: true,
    // 					navigateByImgClick: true,
    // 					preload: [0,5] // Will preload 0 - before current, and 1 after the current image
    // 				  },
    // 				  image: {
    // 					//tError: '<a href='%url%'>The image #%curr%</a> could not be loaded.',
    // 					titleSrc: function(item) {
    // 					  //return item.el.attr('title') + '<small>by Marsel Van Oosten</small>';
    // 					}
    // 				  }
    // 				});
    // 		  stLight.options({publisher: 'e8d11332-5e62-42d4-a5e2-94bd0f0e0018', doNotHash: false, doNotCopy: false, hashAddressBar: false}); ";
    // 	}
    // echo "jQuery(function() {
    // 		var availableTags = [";
    // 		if(!empty($es_addresses)) {
    // 			foreach($es_addresses as $es_addres) {
    // 				echo "'".$es_addres->prop_address."',";
    // 			}
    // 		 }
    // 		echo "];
    // 		jQuery( '.es_address_auto' ).autocomplete({
    // 			source: availableTags
    // 		});
    // 	});
    // 	";
    // echo "
    // 	jQuery(function() {
    // 		jQuery('#es_date_added').datepicker({
    // 			showOn: 'button',
    // 			buttonImage: '".DIR_URL."front_templates/images/es_calender_icon.jpg',
    // 			buttonImageOnly: true,
    // 		});
    // 	});
    // ";
    global $current_user;
    $current_user = wp_get_current_user();
    if(isset($current_user->roles[0]) && $current_user->roles[0] =='agent_role'){
        echo "user_validate = 'ok';
			email_validate = 'ok';";
    }
    if(isset($_GET['add_new_prop'])){
        echo "
				  var map;
				  var geocoder;
				  var markers = new Array();
				  var firstLoc;
				  function myGeocodeFirst() {
					geocoder = new google.maps.Geocoder();
					var es_adress = jQuery('#prop_street').val()+', '+ jQuery('#es_cities option:selected').text()+', '+ jQuery('#es_states option:selected').text()+', '+ jQuery('#es_country option:selected').text();
					if(jQuery('#es_cities option:selected').val()!=''){
						jQuery('#prop_address').val(es_adress);
					}
					//console.log(es_adress);
					geocoder.geocode( {'address': es_adress },
					  function(results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
						  firstLoc = results[0].geometry.location;
						  //console.log(firstLoc);
						  document.getElementById('prop_longitude').value = results[0].geometry.location.lng();
						  document.getElementById('prop_latitude').value = results[0].geometry.location.lat();
						  map = new google.maps.Map(document.getElementById('es_address_map'),
						  {
							center: firstLoc,
							zoom: 16,
							mapTypeId: google.maps.MapTypeId.ROADMAP
						  });
						} 
						else {
						  document.getElementById('es_address_map').value = status;
						}
					  }
					);
				  }
				window.onload=myGeocodeFirst;";
    }
    echo "\n</script>";
}
add_action( 'wp_footer', 'es_print_scripts' );
function es_google_map() {
    if(is_singular('properties')){
        global $wpdb, $post;
        $sql = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}estatik_properties 
            WHERE prop_id = '%d' order by prop_id desc", get_the_id());
        $es_prop_single = $wpdb->get_row($sql);
        if($es_prop_single->prop_latitude!='' && $es_prop_single->prop_longitude!='') {
            echo "<script type='text/javascript'>
				function initialize() {
				  var myLatlng = new google.maps.LatLng(".$es_prop_single->prop_latitude.",".$es_prop_single->prop_longitude.");
				  var mapOptions = {
					zoom: 16,
					scrollwheel: false,
					center: myLatlng
				  }
				  var map = new google.maps.Map(document.getElementById('es_prop_single_view_map'), mapOptions);
				  var marker = new google.maps.Marker({
					  position: myLatlng,	  
					  map: map,
					  title: '".str_replace('\'','',$es_prop_single->prop_title)."'
				  });
				}
				google.maps.event.addDomListener(window, 'load', initialize);
			</script>";
        }
    }
}
add_action( 'wp_footer', 'es_google_map' );
/*if( is_active_widget( false, false, 'es_mapview' ) ){
	add_action('wp_footer', 'custom_widget_init');
}*/
function es_join($fields,$table1,$table2,$where=""){
    global $wpdb;
    $sql = 'SELECT '.$fields.' FROM '.$wpdb->prefix.$table1.','.$wpdb->prefix.$table2.' WHERE '.$where;
    return $wpdb->get_results($sql);
}
/**
 * Filter the link query arguments to change the post types.
 *
 * @param array $query An array of WP_Query arguments.
 * @return array $query
 */
function my_custom_link_query( $query ){
    global $current_user;
    if($current_user->roles[0]=="agent_role"){
        $query['post_type'] = array( 'asasasassas' );
    }
    return $query;
}
add_filter( 'wp_link_query_args', 'my_custom_link_query' );
function es_get_locations() {
    global $wpdb;
    // The $_REQUEST contains all the data sent via ajax
    if ( !isset($_REQUEST) ) {
        die();
    }
    $prefix = $wpdb->prefix;
    $id = $_REQUEST['id'];
    $type = $_REQUEST['type'];
    switch ( $type ) {
        case 'country_id':
            $table = 'estatik_manager_states';
            $field_id = 'state_id';
            $field_title = 'state_title';
            break;
        case 'state_id':
            $table = 'estatik_manager_cities';
            $field_id = 'city_id';
            $field_title = 'city_title';
            break;
        default:
            break;
    }
    $result = $wpdb->get_results("SELECT $field_id AS id, $field_title as title FROM $prefix$table WHERE $type='$id'");
    echo json_encode($result);
    // Always die in functions echoing ajax content
    die();
}
add_action( 'wp_ajax_es_get_locations', 'es_get_locations' );
add_action( 'wp_ajax_nopriv_es_get_locations', 'es_get_locations' );
require_once('includes/es_listing_functions.php');
function es_search_input_min_max($title, $name) {
    $min = $name . '_min';
    $max = $name . '_max';
    ?>
    <div class="es_my_listing_search_field">
        <label><?php echo $title ?></label>
        <input type="text" name="<?php echo $min?>"
               value="<?php echo isset($_GET[$min]) ? $_GET[$min] : '' ?>"
               placeholder="<?php _e("min", 'es-plugin'); ?>" />
        <i>-</i>
        <input type="text" name="<?php echo $max?>"
               value="<?php echo isset($_GET[$max]) ? $_GET[$max] : '' ?>"
               placeholder="<?php _e("max", 'es-plugin'); ?>"  />
    </div>
    <?php
}
function input_select($title, $name, $table) {
    global $wpdb;
    $value = isset($_GET[$name]) ? $_GET[$name] : '';
    ?>
    <div class="es_my_listing_search_field search_<?php echo $name ?>">
        <label><?php echo $title; ?></label>
        <div class="es_search_select">
            <span><?php echo $title; ?></span>
            <small></small>
            <ul>
                <?php
                if ( !empty($table) ) {
                    $items = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}$table" );
                    $keys = array_keys((array)$items[0]);
                    foreach ( $items as $item ) {
                        $item = (array) $item;
                        $id = $item[$keys[0]];
                        $title = $item[$keys[1]];
                        $selected = ($value == $id) ? 'selected' : "";
                        ?>
                        <li class="<?php echo $selected?>" value="<?php echo $id ?>">
                            <?php echo $title ?>
                        </li>
                        <?php
                    }
                }
                ?>
            </ul>
            <input type="hidden" name="<?php echo $name?>" value="<?php echo $value ?>" />
        </div>
    </div>
    <?php
}
if ( !function_exists('es_mapview_script') ) {
    function es_mapview_script(){
        global $wp_session, $es_settings, $wpdb;
        // $widget_id 		 = $wp_session['es_map_widget_id'];
        $category  		 = $wp_session['es_map_category'];
        $map_icon_style  = $wp_session['map_icon_style'];
        $number_of_props = $wp_session['number_of_props'];
        // $map_zoom_level  = $wp_session['map_zoom_level'];
        if(isset($category) && $category != 'all'){
            $where_array[]  =	'prop_category 	=  "'.$category.'"';
        }
        $where_array[]  =	'prop_pub_unpub	= 1';
        $where =  implode(" AND ",$where_array);
        $es_map_limit = ($number_of_props!="") ? "limit ".$number_of_props : "";
        $sql = "SELECT * FROM {$wpdb->prefix}estatik_properties WHERE $where order by prop_id desc $es_map_limit";
        $es_my_listing 	  = $wpdb->get_results($sql);
        $firstLoc = 0;
        $propCount= 0;
        $first_latitude  = "";
        $first_longitude = "";
        $mapinfos = array();
        if(!empty($es_my_listing)) {
            foreach($es_my_listing as $list) {
                if($firstLoc==0 && $list->prop_latitude!="" && $list->prop_longitude!=""){
                    $first_latitude  = $list->prop_latitude;
                    $first_longitude = $list->prop_longitude;
                    $firstLoc = 1;
                }
                if($list->prop_latitude!="" && $list->prop_longitude!="") {
                    $propCount++;
                    $sql = $wpdb->prepare("SELECT cat_title FROM {$wpdb->prefix}estatik_manager_categories
						WHERE cat_id = '%d'", $list->prop_category);
                    $prop_cat = $wpdb->get_row($sql);
                    $prop_cat = (!empty($prop_cat) && $prop_cat->cat_title!="")? $prop_cat->cat_title : "";
                    $image_sql = $wpdb->prepare("SELECT prop_meta_value FROM {$wpdb->prefix}estatik_properties_meta
											WHERE prop_id = '%d' AND prop_meta_key = 'images'", $list->prop_id);
                    $uploaded_images = $wpdb->get_row($image_sql);
                    $uploaded_images_count ="0";
                    if(!empty($uploaded_images)){
                        $upload_image_data = unserialize($uploaded_images->prop_meta_value);
                        $uploaded_images_count = count($upload_image_data);
                    }
                    $img = "";
                    if(!empty($upload_image_data)) {
                        $upload_dir = wp_upload_dir();
                        $list_image_name = explode("/",$upload_image_data[0]);
                        $list_image_name = end($list_image_name);
                        $list_image_path = str_replace($list_image_name,"",$upload_image_data[0]);
                        $image_url = $list_image_path.'list_'.$list_image_name;
                        $img = "<img src='{$upload_dir['baseurl']}$image_url' alt='' />";
                    }
                    $title = ($es_settings->title==1)? '<h5>'.substr($list->prop_title,0,30).'</h5>' : '<h5>'.substr($list->prop_address,0,30).'</h5>';
                    $price 				  = "";
                    $currency_sign_before = "";
                    $currency_sign_after  = "";
                    if($es_settings->price==1) {
                        $currency_sign_ex = explode(",", $es_settings->default_currency);
                        if(count($currency_sign_ex)==1){
                            $currency_sign = $currency_sign_ex[0];
                        }else {
                            $currency_sign = $currency_sign_ex[1];
                        }
                        ($es_settings->currency_sign_place=='before')? $currency_sign_before = $currency_sign : $currency_sign_after = $currency_sign;
                        $price_format = explode("|",$es_settings->price_format);
                        $price = '<h4>'.$currency_sign_before.number_format($list->prop_price,$price_format[0],$price_format[1],$price_format[2]).$currency_sign_after.'</h4>';
                    }
                    $es_dimension = $wpdb->get_row("SELECT dimension_title FROM {$wpdb->prefix}estatik_manager_dimension WHERE dimension_status=1" );
                    $es_dimension_title = (!empty($es_dimension))? $es_dimension->dimension_title : '';
                    $prop_area = ($list->prop_area!=0) ? "<small>{$list->prop_area} $es_dimension_title</small>" : '';
                    $prop_beds = ($list->prop_bedrooms!=0) ? '<small>'.$list->prop_bedrooms.' '.__('beds', 'es-plugin').'</small>' : '';
                    $prop_baths    = ($list->prop_bathrooms!=0)? '<small>'.$list->prop_bathrooms.' '.__('bath', 'es-plugin').'</small>' : '';
                    $prop_link = get_permalink($list->prop_id);
                    $rentStyle = strpos($prop_cat,"rent");
                    $rentStyle = (!empty($rentStyle))? "rentInfo" : "";
                    $infoHtml = "<div class='esInfoBox ".$rentStyle."'><h2>Property #".$propCount." <span>".$prop_cat."</span></h2><div class='esInfoBoxIn clearfix'><div class='esInfoBoxInPic'>".$img."</div><div class='esInfoBoxInDetail'>".$title."".$price."<p>".$prop_area.$prop_beds.$prop_baths."</p><a href='".$prop_link."' >More Detail</a></div></div></div>";
                    //$infoHtml = "Ali";
                    $mapinfos[] = "['".preg_replace('/[^A-Za-z0-9\-\(\) ]/', '', strip_tags($title))."','".$list->prop_latitude."','".$list->prop_longitude."','".addslashes($infoHtml)."', '".$prop_cat."']";
                }
            }
        }
        $mapinfos = implode(', ', $mapinfos);
        $latitude = ($first_latitude != "") ? $first_latitude : 55;
        $longitude = ($first_longitude != "") ? $first_longitude : 103;
        // $lat_long = ($first_latitude!="" && $first_latitude!="") ? $first_latitude.", ".$first_longitude : "55,103";
        // $map_zoom_level = (isset($map_zoom_level) && $map_zoom_level!="")? $map_zoom_level : "10";
        //$map_zomm = ($first_latitude!="" && $first_latitude!="")? "14" : "3";
        // if($map_icon_style==3){
        // 	$map_icon_style = "3";
        // } else if($map_icon_style==2){
        // 	$map_icon_style = "2";
        // }else{
        // 	$map_icon_style = "1";
        // }
        //echo "<script type='text/javascript' src='".DIR_URL . "front_templates/js/infobox.js'></script>";
        echo "<script type='text/javascript'>
        if (typeof EstatikApp != 'undefined') {
		EstatikApp.widgetMapview.mapinfos = [$mapinfos];
	    EstatikApp.widgetMapview.latitude = $latitude;
	    EstatikApp.widgetMapview.longitude = $longitude;
	    } else {
	        var EstatikApp = {
	        widgetMapview: {
	        mapinfos: [$mapinfos],
	        latitude:$latitude,
	        longitude:$longitude
	        }
	    }
	    </script>";
    }
}
function es_get_page_id_by_shortcode($shortcode) {
    global $wpdb;
    $sql = $wpdb->prepare("SELECT ID
            FROM {$wpdb->posts}
            WHERE
                post_type = 'page'
                AND post_status='publish'
                AND post_content LIKE '%s'", "%$shortcode%");
    return $wpdb->get_var($sql);
}
function es_get_url_by_shortcode($shortcode) {
    $id = es_get_page_id_by_shortcode($shortcode);
    if ( !empty($id) ) {
        return get_permalink($id);
    }
    return get_option('home');
}