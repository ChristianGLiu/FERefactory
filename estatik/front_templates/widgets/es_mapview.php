<?php
wp_reset_query();
wp_reset_postdata();
global $post;
$current_id = get_post( $post );
if(!empty($current_id)){
    $current_id = $current_id->ID;
}
$queried_object = get_queried_object();
$current_page = "";
$single_page  = "";
$author_page  = "";
$archive_page = "";
$search_page  = "";
$category_page= "";
$current_page = "";
if(is_single() && $show_on_pages=="show_on_checked_pages"){
    $single_page = in_array('single_page',$choosed_pages);
}else if( isset($queried_object->user_login) && $queried_object->user_login!="" && $show_on_pages=="show_on_checked_pages") {
    $author_page = in_array('author_page',$choosed_pages);
}else if(is_tax() && $show_on_pages=="show_on_checked_pages") {
    $archive_page = in_array('archive_page',$choosed_pages);
}else if(is_category() && $show_on_pages=="show_on_checked_pages") {
    $category_page = in_array('category_page',$choosed_pages);
}else if(is_search() && $show_on_pages=="show_on_checked_pages") {
    $search_page = in_array('search_page',$choosed_pages);
} else if($show_on_pages=="show_on_checked_pages") {
    $current_page = in_array($current_id,$choosed_pages);
}
if(is_single() && $show_on_pages=="hide_on_checked_pages"){
    $single_page = in_array('single_page',$choosed_pages);
    $author_page=true;
    $archive_page=true;
    $search_page=true;
    $category_page=true;
    $current_page = true;
}else if( isset($queried_object->user_login) && $queried_object->user_login!="" && $show_on_pages=="hide_on_checked_pages") {
    $author_page = in_array('author_page',$choosed_pages);
    $single_page=true;
    $archive_page=true;
    $search_page=true;
    $category_page=true;
    $current_page = true;
}else if(is_tax() && $show_on_pages=="hide_on_checked_pages") {
    $archive_page = in_array('archive_page',$choosed_pages);
    $$author_page=true;
    $single_page=true;
    $search_page=true;
    $category_page=true;
    $current_page = true;
}else if(is_category() && $show_on_pages=="hide_on_checked_pages") {
    $category_page = in_array('category_page',$choosed_pages);
    $$author_page=true;
    $single_page=true;
    $archive_page=true;
    $search_page=true;
    $current_page = true;
}else if(is_search() && $show_on_pages=="hide_on_checked_pages") {
    $search_page = in_array('search_page',$choosed_pages);
    $author_page=true;
    $single_page=true;
    $archive_page=true;
    $category_page=true;
    $current_page = true;
} else if($show_on_pages=="hide_on_checked_pages") {
    $current_page = in_array($current_id,$choosed_pages);
    $author_page=true;
    $single_page=true;
    $archive_page=true;
    $search_page=true;
    $category_page=true;
}
if( $show_on_pages=="all_pages" || $show_on_pages=="show_on_checked_pages"
    && ($current_page==true || $author_page==true || $single_page==true || $archive_page==true || $search_page==true || $category_page==true )
    || $show_on_pages=="hide_on_checked_pages"
    && ( $current_page==false || $author_page==false || $single_page==false || $archive_page==false || $search_page==false || $category_page==false )
    && in_array($widget_id,$choosed_pages)) {
    global $wp_session;
    $wp_session['es_map_widget_id'] = (isset($widget_id))? "esMapView_".$widget_id : "esMapView_786";
    $widget_id = $wp_session['es_map_widget_id'];
    $wp_session['es_map_category'] = (isset($category))? $category : "";
    $category = $wp_session['es_map_category'];
    $wp_session['map_icon_style'] = (isset($map_icon_style))? $map_icon_style : "";
    $map_icon_style = $wp_session['map_icon_style'];
    $wp_session['number_of_props'] = (isset($number_of_props))? $number_of_props : "";
    $number_of_props = $wp_session['number_of_props'];
    $wp_session['map_zoom_level'] = (isset($map_zoom_level))? $map_zoom_level : "";
    $map_zoom_level = $wp_session['map_zoom_level'];
    ?>
    <div class="esMapViewOuter <?php echo $mapview_layout; ?>">
        <div class="esMapView" id="<?php echo $widget_id; ?>"
             icon-style="<?php echo $wp_session['map_icon_style']?>"
             zoom="<?php echo $map_zoom_level ?>"></div>
    </div>
    <?php
    // function es_mapview_script(){
    // 	global $wp_session, $es_settings, $wpdb;
    // 	// if ( empty($wp_session['es_map_widget_id']) ) {
    // 	// 	return;
    // 	// }
    // 	$widget_id 		 = $wp_session['es_map_widget_id'];
    // 	$category  		 = $wp_session['es_map_category'];
    // 	$map_icon_style  = $wp_session['map_icon_style'];
    // 	$number_of_props = $wp_session['number_of_props'];
    // 	$map_zoom_level  = $wp_session['map_zoom_level'];
    // 	if(isset($category) && $category != 'all'){
    // 		$where_array[]  =	'prop_category 	=  "'.$category.'"';
    // 	}
    // 	$where_array[]  =	'prop_pub_unpub	= 1';
    // 	$where =  implode(" AND ",$where_array);
    // 	$es_map_limit = ($number_of_props!="") ? "limit ".$number_of_props : "";
    // 	$sql = "SELECT * FROM {$wpdb->prefix}estatik_properties WHERE $where order by prop_id desc $es_map_limit";
    // 	$es_my_listing 	  = $wpdb->get_results($sql);
    // 	$firstLoc = 0;
    // 	$propCount= 0;
    // 	$first_latitude  = "";
    // 	$first_longitude = "";
    // 	$mapinfos = array();
    // 	if(!empty($es_my_listing)) {
    // 		foreach($es_my_listing as $list) {
    // 			if($firstLoc==0 && $list->prop_latitude!="" && $list->prop_longitude!=""){
    // 				$first_latitude  = $list->prop_latitude;
    // 				$first_longitude = $list->prop_longitude;
    // 				$firstLoc = 1;
    // 			}
    // 			if($list->prop_latitude!="" && $list->prop_longitude!="") {
    // 				$propCount++;
    // 				$sql = $wpdb->prepare("SELECT cat_title FROM {$wpdb->prefix}estatik_manager_categories
    // 					WHERE cat_id = '%d'", $list->prop_category);
    // 				$prop_cat = $wpdb->get_row($sql);
    // 				$prop_cat = (!empty($prop_cat) && $prop_cat->cat_title!="")? $prop_cat->cat_title : "";
    // 				$image_sql = $wpdb->prepare("SELECT prop_meta_value FROM {$wpdb->prefix}estatik_properties_meta
    // 										WHERE prop_id = '%d' AND prop_meta_key = 'images'", $list->prop_id);
    // 				$uploaded_images = $wpdb->get_row($image_sql);
    // 				$uploaded_images_count ="0";
    // 				if(!empty($uploaded_images)){
    // 					$upload_image_data = unserialize($uploaded_images->prop_meta_value);
    // 					$uploaded_images_count = count($upload_image_data);
    // 				}
    // 				$img = "";
    // 				if(!empty($upload_image_data)) {
    // 					$upload_dir = wp_upload_dir();
    // 					$list_image_name = explode("/",$upload_image_data[0]);
    // 					$list_image_name = end($list_image_name);
    // 					$list_image_path = str_replace($list_image_name,"",$upload_image_data[0]);
    // 					$image_url = $list_image_path.'list_'.$list_image_name;
    // 					$img = "<img src='{$upload_dir['baseurl']}$image_url' alt='' />";
    // 				}
    // 				$title = ($es_settings->title==1)? '<h5>'.substr($list->prop_title,0,30).'</h5>' : '<h5>'.substr($list->prop_address,0,30).'</h5>';
    // 				$price 				  = "";
    // 				$currency_sign_before = "";
    // 				$currency_sign_after  = "";
    // 				if($es_settings->price==1) {
    // 					$currency_sign_ex = explode(",", $es_settings->default_currency);
    // 					if(count($currency_sign_ex)==1){
    // 						$currency_sign = $currency_sign_ex[0];
    // 					}else {
    // 						$currency_sign = $currency_sign_ex[1];
    // 					}
    // 					($es_settings->currency_sign_place=='before')? $currency_sign_before = $currency_sign : $currency_sign_after = $currency_sign;
    // 					$price_format = explode("|",$es_settings->price_format);
    // 					$price = '<h4>'.$currency_sign_before.number_format($list->prop_price,$price_format[0],$price_format[1],$price_format[2]).$currency_sign_after.'</h4>';
    // 				}
    // 				$es_dimension = $wpdb->get_row("SELECT dimension_title FROM {$wpdb->prefix}estatik_manager_dimension WHERE dimension_status=1" );
    // 				$es_dimension_title = (!empty($es_dimension))? $es_dimension->dimension_title : '';
    // 				$prop_area = ($list->prop_area!=0) ? "<small>{$list->prop_area} $es_dimension_title</small>" : '';
    // 				$prop_beds = ($list->prop_bedrooms!=0) ? '<small>'.$list->prop_bedrooms.' '.__('beds', 'es-plugin').'</small>' : '';
    // 				$prop_baths    = ($list->prop_bathrooms!=0)? '<small>'.$list->prop_bathrooms.' '.__('bath', 'es-plugin').'</small>' : '';
    // 				$prop_link = get_permalink($list->prop_id);
    // 				$rentStyle = strpos($prop_cat,"rent");
    // 				$rentStyle = (!empty($rentStyle))? "rentInfo" : "";
    // 				$infoHtml = "<div class='esInfoBox ".$rentStyle."'><h2>Property #".$propCount." <span>".$prop_cat."</span></h2><div class='esInfoBoxIn clearfix'><div class='esInfoBoxInPic'>".$img."</div><div class='esInfoBoxInDetail'>".$title."".$price."<p>".$prop_area.$prop_beds.$prop_baths."</p><a href='".$prop_link."' >More Detail</a></div></div></div>";
    // 				//$infoHtml = "Ali";
    // 				$mapinfos[] = "['".preg_replace('/[^A-Za-z0-9\-\(\) ]/', '', strip_tags($title))."','".$list->prop_latitude."','".$list->prop_longitude."','".addslashes($infoHtml)."', '".$prop_cat."']";
    // 			}
    // 		}
    // 	}
    // 	$mapinfos = implode(', ', $mapinfos);
    // 	$latitude = ($first_latitude != "") ? $first_latitude : 55;
    // 	$longitude = ($first_longitude != "") ? $first_longitude : 103;
    // 	// $lat_long = ($first_latitude!="" && $first_latitude!="") ? $first_latitude.", ".$first_longitude : "55,103";
    // 	$map_zoom_level = (isset($map_zoom_level) && $map_zoom_level!="")? $map_zoom_level : "10";
    // 	//$map_zomm = ($first_latitude!="" && $first_latitude!="")? "14" : "3";
    // 	if($map_icon_style==3){
    // 		$map_icon_style = "3";
    // 	} else if($map_icon_style==2){
    // 		$map_icon_style = "2";
    // 	}else{
    // 		$map_icon_style = "1";
    // 	}
    // 	//echo "<script type='text/javascript' src='".DIR_URL . "front_templates/js/infobox.js'></script>";
    // 	echo "<script type='text/javascript'>
    // 	EstatikApp.widgetMapview.mapinfos = [$mapinfos];
    //        EstatikApp.widgetMapview.latitude = $latitude;
    //        EstatikApp.widgetMapview.longitude = $longitude;
    //        EstatikApp.widgetMapview.map_icon_style = '$map_icon_style';
    //        EstatikApp.widgetMapview.map_zoom_level = $map_zoom_level;
    //        EstatikApp.widgetMapview.widget_id = '$widget_id';
    // //var map;
    // //var marker;
    // 	// function esMapViewInit() {
    // 	// 	var myLatlng = new google.maps.LatLng(EstatikApp.widgetMapview.latitude,
    // 	// 		EstatikApp.widgetMapview.longitude);
    // 	// 	var isDraggable = jQuery(document).width() > 768 ? true : false;
    // 	// 	var mapOptions = {
    // 	// 	  zoom: EstatikApp.widgetMapview.map_zoom_level,
    // 	// 	  draggable: isDraggable,
    // 	// 	  scrollwheel: false,
    // 	// 	  center: myLatlng,
    // 	// 	};
    // 	// 	var map = new google.maps.Map(document.getElementById(EstatikApp.widgetMapview.widget_id), mapOptions);
    // 	// 	setMarkers(map, EstatikApp.widgetMapview.mapinfos);
    // 	// }
    // 	// //var markersArray = new Array();
    // 	// function setMarkers(map, locations) {
    // 	//   for (var i = 0; i < locations.length; i++) {
    // 	// 	var mapinfo = locations[i];
    // 	// 	var myLatLng = new google.maps.LatLng(mapinfo[1], mapinfo[2]);
    // 	// 	var mapIcon   = '';
    // 	// 	var closeIcon = '';
    // 	// 	var marker = '';
    // 	// 	if(mapinfo[4].indexOf('rent')!=-1){
    // 	// 		mapIcon = 'mapIconRed';
    // 	// 	}else{
    // 	// 		mapIcon = 'mapIconBlue';
    // 	// 	}
    // 	// 	marker = new google.maps.Marker({
    // 	// 		position: myLatLng,
    // 	// 		map: map,
    // 	// 		icon: EstatikApp.dir_url + 'front_templates/images/' + mapIcon
    // 	// 			+ EstatikApp.widgetMapview.map_icon_style + '.png',
    // 	// 		title: mapinfo[0],
    // 	// 	});
    // 	// 	var infobox = new InfoBox();
    // 	// 	google.maps.event.addListener(marker, 'click', (function(marker, i) {
    // 	// 	return function() {
    // 	// 		 map.setCenter(marker.getPosition());
    // 	// 		 infobox.close();
    // 	// 		 if(locations[i][4].indexOf('rent')!=-1){
    // 	// 				closeIcon = 'mapRedClose';
    // 	// 			}else{
    // 	// 				closeIcon = 'mapBlueClose';
    // 	// 			}
    // 	// 		 infobox = new InfoBox({
    // 	// 			 content: '',
    // 	// 			 disableAutoPan: false,
    // 	// 			 //maxWidth: 400,
    // 	// 			 //pixelOffset: new google.maps.Size(-140, 0),
    // 	// 			 zIndex: null,
    // 	// 			 boxStyle: {
    // 	// 				background: 'url(\'http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobox/examples/tipbox.gif\') no-repeat',
    // 	// 			},
    // 	// 			closeBoxURL: EstatikApp.dir_url + 'front_templates/images/' + closeIcon + '.png',
    // 	// 			infoBoxClearance: new google.maps.Size(1, 1)
    // 	// 		});
    // 	// 		infobox.setContent(locations[i][3]);
    // 	// 		//infobox.setContent(".stripcslashes('locations[i][3]').");
    // 	// 		infobox.open(map, marker);
    // 	// 	}
    // 	// 	})(marker, i));
    // 	//   }
    // 	// }
    // 	// google.maps.event.addDomListener(window, 'load', esMapViewInit);
    // </script>";
    // }
    // add_action( 'wp_header', 'es_mapview_script' );
}
