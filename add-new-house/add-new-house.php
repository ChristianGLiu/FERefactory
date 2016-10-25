<?php
/**
* Plugin Name: add-new-house
* Plugin URI: https://www.eclink.ca/
* Description: add-new-house plugin built
* Version: 1.0
* Author: Gang Liu
* Author URI: https://www.eclink.ca/
**/


defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

global $wp_rest_server;

function es_sanitize_array($item) {
	return sanitize_text_field($item);
}

// Register REST API endpoints
class AddNewHouse {

	/**
	* Register the routes for the objects of the controller.
	*/
	public static function register_endpoints() {
		// endpoints will be registered here
		register_rest_route( 'house/v1', '/get', array(
		'methods' => 'GET',
		'callback' => array( 'AddNewHouse', 'get_house' ),
		) );
		register_rest_route( 'house/v1', '/create', array(
		'methods' => 'POST',
		'callback' => array( 'AddNewHouse', 'create_house' ),
		) );
	}

	/**
	* Get all the candies
	*
	* @param WP_REST_Request $request Full data about the request.
	* @return WP_Error|WP_REST_Request
	*/
	public static function get_house( $request ) {
		$data = get_posts( array(
		'post_type'      => 'properties',
		'post_status'    => 'publish',
		'posts_per_page' => 20,
		) );
		global $wpdb;

		// @TODO do your magic here
		$house_obj = $data[0];

		$queryString = "SELECT * FROM ".$wpdb->prefix."estatik_properties WHERE prop_id = ".$house_obj->ID." ORDER BY prop_id desc";
		//echo "query string:".$queryString."<br />";
		$sql = $wpdb->prepare($queryString);
		$es_prop_single = $wpdb->get_row($sql);
		$house_obj->post_content = $es_prop_single;
		return new WP_REST_Response( $house_obj , 200 );
	}


	/**
	* Add a new candy
	*
	* @param WP_REST_Request $request Full data about the request.
	* @return WP_Error|WP_REST_Request
	*/
	public static function create_house( $request ) {
		// $json = $request->get_body_params();
		// $data = json_decode($json, true);
		$debugInfo = '';

		global $wpdb;
		$duplicated = false;
		$check_id = 0;
		$params = $request->get_json_params();

		$debugInfo .= "get json request body:".var_dump($params)."\r\n";
		if(!empty($params['MlsNumber'])){
			$guid = "https://eclink.ca/properties/".$params['MlsNumber']."/";

			if(!empty($guid)){
				$check_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM ".$wpdb->prefix."posts WHERE guid=%s", $guid ) );
				if(!empty($check_id)){
					$duplicated = true;
				}
			}

			$post_id=0;

			$the_user = get_users(array( 'number' => 1, 'offset'=>rand(1,200) ) );
			$debugInfo .= "user id is ".$the_user->ID."\r\n";
			$post_post_title ="[最新房源]".mb_strimwidth($params['Property']['Address']['AddressText'], 0, 10, "...");
			$debugInfo .= "post_post_title ".$post_post_title."\r\n";
			$post_post_name=preg_replace("/[\s_]/", "-", mb_strimwidth($params['Property']['Address']['AddressText'], 0, 10, ""));
			$debugInfo .= "post_post_name ".$post_post_name."\r\n";

			if(!$duplicated){
				$post_id = wp_insert_post( array(
				'post_author' =>$the_user->ID,
				'post_title' =>$post_post_title,
				'post_excerpt'=>'加东华联网分享的又一则中文房产信息',
				'post_name' =>$post_post_name,
				'post_parent' =>0,
				'post_type'=>"properties",
				'comment_status'=>'open',
				'ping_status'=>'open',
				'post_status'=>'publish',
				'filter'=>"raw",
				'guid' => $guid,
				'post_content'=>"[es_single_property]"
				) );

			}else{

				$my_post = array(
				'ID'           => $check_id,
				'post_title'    => $post_post_title,
				'post_type'     => 'properties'
				);
				// Update the post into the database
				$post_id = wp_update_post( $my_post );
			}


			if(!empty($post_id)) {

				$post_house_type =$params['Property']['Type'];
				$post_house_price =$params['Property']['Price'];
				$post_house_bedrooms = $params['Building']['Bedrooms'];
				$prop_bedrooms = intval($post_house_bedrooms);


				if(strpos($post_house_bedrooms,'+')>-1 || strpos($post_house_bedrooms,' ')>-1) {
					$bedrooms = preg_split("/[\s\+]/", $post_house_bedrooms);
					$prop_bedrooms= 0;
					foreach($bedrooms as $onebed) {
						$prop_bedrooms += intval($onebed);
					}
				}


				$prop_price = preg_replace("/([^0-9\\.])/i", "", $post_house_price);
				$debugInfo .= "get type price bedromms: ".$post_house_type. ' '.$post_house_price.' '.$post_house_bedrooms."\r\n";
				if(!empty($post_house_type)){
					if(strpos($post_house_type,'apartment')>-1 ||strpos($post_house_type,'condo')>-1){
						$prop_type = 126;
					} else if (strpos($post_house_type,'house')>-1 ||strpos($post_house_type,'family')>-1){
						$prop_type = 127;
					} else {
						$prop_type = 128;

					}
				} else {
					$prop_type = 127;
				}

				$debugInfo .= "get prop_type : ".$prop_type."\r\n";
				$agent_id 			= 272;
				$prop_title 		= $post_post_title;
				//	$prop_type 			= sanitize_text_field($_POST['prop_type']);
				$prop_category 		= 125;
				$prop_status 		= 122;

				wp_set_object_terms( $post_id, $prop_category, 'property_category');
				wp_set_object_terms( $post_id, $prop_status, 'property_status');
				wp_set_object_terms( $post_id, $prop_type, 'property_type');
				wp_set_object_terms( $post_id, 129, 'category');

				$debugInfo .= "set term relation : \r\n";

				$prop_featured 		= 0;
				$prop_hot 			= 0;
				$prop_open_house 	= 0;
				$prop_foreclosure 	= 0;
				//	$prop_price 		= sanitize_text_field(floatval($_POST['prop_price']));
				$prop_period 		= 1;
				//	$prop_bedrooms 		= sanitize_text_field(intval($_POST['prop_bedrooms']));
				$prop_bathrooms 	= intval($params['Building']['BathroomTotal']);
				$prop_floors 		= intval($params['Building']['StoriesTotal']);
				$prop_lotsize 		= intval($params['Land']['SizeTotal']);
				if($prop_lotsize<10) {
					$prop_lotsize = $prop_lotsize * 43560 ;
				}

				$prop_area 			= $prop_lotsize;
				$debugInfo .= "get house other attribute: ".$prop_bathrooms. ' '.$prop_floors.' '.$prop_lotsize."\r\n";
				$prop_builtin 		= '';
				$prop_description 	= wp_kses_post($params['PublicRemarks']);
				$prop_description 	= stripcslashes($prop_description);

				$apiKey = 'AIzaSyAsP0s0WE8zAqB9-C0FVWTPag_ATP-boXA';
				$translate_url = 'https://www.googleapis.com/language/translate/v2?key=' . $apiKey . '&q=' . rawurlencode($prop_description) . '&source=en&target=zh-CN';

				$translate_handle = curl_init($translate_url);
				curl_setopt($translate_handle, CURLOPT_RETURNTRANSFER, true);
				$translate_response = curl_exec($translate_handle);
				$translate_responseDecoded = json_decode($translate_response, true);
				$responseCode = curl_getinfo($translate_handle, CURLINFO_HTTP_CODE);      //Here we fetch the HTTP response code
				curl_close($translate_handle);

				if($responseCode != 200) {
					$debugInfo .= 'Fetching translation failed! Server response code:' . $responseCode . '\r\n';
					$debugInfo .= 'Error description: ' . $translate_responseDecoded['error']['errors'][0]['message'];
				}
				else {
					$prop_description = $translate_responseDecoded['data']['translations'][0]['translatedText'];
					$debugInfo .= 'prop_description translate: ' . $prop_description. '\r\n';
				}


				$country_id 		= 1;
				$state_id 			= 1;
				$city_id 			= 1;
				$prop_zip_postcode 	= sanitize_text_field($params['PostalCode']);
				$prop_street 		= sanitize_text_field('');
				if($country_id!="" || $state_id!="" || $city_id!=""){
					$prop_address		= sanitize_text_field($post_post_title);
				}else{
					$prop_address		= '';
				}
				$prop_longitude 	= sanitize_text_field(floatval($params['Property']['Address']['Longitude']));
				$prop_latitude 		= sanitize_text_field(floatval($params['Property']['Address']['Latitude']));
				$prop_id			= intval($post_id);



				if(!$duplicated){

$debugInfo .= "ready to insert property table:".$wpdb->prefix.'estatik_properties'."\r\n";
					$wpdb->insert(
					$wpdb->prefix.'estatik_properties',
					array(
					'prop_id' 			=> $post_id,
					'agent_id' 			=> $agent_id,
					'prop_date_added' 	=> time(),
					'prop_pub_unpub' 	=> 1,
					'prop_number'       => intval($params['MlsNumber']),
					'prop_title' 		=> $prop_title,
					'prop_type' 		=> $prop_type,
					'prop_category' 	=> $prop_category,
					'prop_status' 		=> $prop_status,
					'prop_featured' 	=> $prop_featured,
					'prop_hot' 			=> $prop_hot,
					'prop_open_house' 	=> $prop_open_house,
					'prop_foreclosure' 	=> $prop_foreclosure,
					'prop_price' 		=> $prop_price,
					'prop_period' 		=> $prop_period,
					'prop_bedrooms' 	=> $prop_bedrooms,
					'prop_bathrooms' 	=> $prop_bathrooms,
					'prop_floors' 		=> $prop_floors,
					'prop_area' 		=> $prop_area,
					'prop_lotsize' 		=> $prop_lotsize,
					'prop_builtin' 		=> $prop_builtin,
					'prop_description' 	=> $prop_description,
					'country_id' 		=> $country_id,
					'state_id' 			=> $state_id,
					'city_id' 			=> $city_id,
					'prop_zip_postcode' => $prop_zip_postcode,
					'prop_street' 		=> $prop_street,
					'prop_address' 		=> $prop_address,
					'prop_longitude' 	=> $prop_longitude,
					'prop_latitude' 	=> $prop_latitude,
					)
					);
				}else{

				$debugInfo .= "ready to update property table:".$wpdb->prefix.'estatik_properties'."\r\n";

					$wpdb->update(
					$wpdb->prefix.'estatik_properties',
					array(
					'prop_title' 		=> $prop_title,
					'prop_type' 		=> $prop_type,
					'prop_category' 	=> $prop_category,
					'prop_status' 		=> $prop_status,
					'prop_featured' 	=> $prop_featured,
					'prop_hot' 			=> $prop_hot,
					'prop_open_house' 	=> $prop_open_house,
					'prop_foreclosure' 	=> $prop_foreclosure,
					'prop_price' 		=> $prop_price,
					'prop_period' 		=> $prop_period,
					'prop_bedrooms' 	=> $prop_bedrooms,
					'prop_bathrooms' 	=> $prop_bathrooms,
					'prop_floors' 		=> $prop_floors,
					'prop_area' 		=> $prop_area,
					'prop_lotsize' 		=> $prop_lotsize,
					'prop_builtin' 		=> $prop_builtin,
					'prop_description' 	=> $prop_description,
					'country_id' 		=> $country_id,
					'state_id' 			=> $state_id,
					'city_id' 			=> $city_id,
					'prop_zip_postcode' => $prop_zip_postcode,
					'prop_street' 		=> $prop_street,
					'prop_address' 		=> $prop_address,
					'prop_longitude' 	=> $prop_longitude,
					'prop_latitude' 	=> $prop_latitude,
					),
					array( 'prop_id' => $prop_id )
					);


				}

			}

			if($duplicated) {
				$post_id = $guid." duplicated\r\n";
			}

			$prop_images = $params['Property']['Photo'];
			$prop_images_arr = array();

			if(!empty($prop_images)) {
				foreach($prop_images as $oneImage){

					if(!empty($oneImage['HighResPath'])){
						array_push($prop_images_arr, $oneImage['HighResPath']);
					}
					if(!empty($oneImage['LowResPath'])){
						array_push($prop_images_arr, $oneImage['LowResPath']);
					}
					if(!empty($oneImage['MedResPath'])){
						array_push($prop_images_arr, $oneImage['MedResPath']);
					}
				}

			}
			if ( !empty($prop_images_arr) ) {
				$uploaded_images = array_map('es_sanitize_array', $prop_images_arr);
				$wpdb->delete( $wpdb->prefix.'estatik_properties_meta', array( 'prop_id' => $prop_id,'prop_meta_key'=>'images') );
				if(!empty($uploaded_images)){
					$prop_meta_data = serialize($uploaded_images);
					$wpdb->insert(
					$wpdb->prefix.'estatik_properties_meta',
					array(
					'prop_id' 		=> $prop_id,
					'prop_meta_key' => 'images',
					'prop_meta_value' 	=>$prop_meta_data
					)
					);
				}
			}

		} else {

			$debugInfo .= "failed, MLSNUMBER can not be null";
		}

		return new WP_REST_Response( "debug info ".$debugInfo, 200 );
		//return new WP_REST_Response( $post_id, 200 );
	}
}

/**
* Add REST API support to an already registered post type.
*/
add_action( 'init', 'my_custom_post_type_rest_support', 25 );
function my_custom_post_type_rest_support() {
	global $wp_post_types;

	//be sure to set this to the name of your post type!
	$post_type_name = 'properties';
	if( isset( $wp_post_types[ $post_type_name ] ) ) {
		$wp_post_types[$post_type_name]->show_in_rest = true;
		$wp_post_types[$post_type_name]->rest_base = $post_type_name;
		$wp_post_types[$post_type_name]->rest_controller_class = 'WP_REST_Posts_Controller';
	}

}

add_action( 'init', 'my_custom_taxonomy_rest_support', 25 );
function my_custom_taxonomy_rest_support() {
	global $wp_taxonomies;

	//be sure to set this to the name of your taxonomy!
	$taxonomy_name = 'property_type';

	if ( isset( $wp_taxonomies[ $taxonomy_name ] ) ) {
		$wp_taxonomies[ $taxonomy_name ]->show_in_rest = true;
		$wp_taxonomies[ $taxonomy_name ]->rest_base = $taxonomy_name;
		$wp_taxonomies[ $taxonomy_name ]->rest_controller_class = 'WP_REST_Terms_Controller';
	}

	$taxonomy_name = 'property_category';

	if ( isset( $wp_taxonomies[ $taxonomy_name ] ) ) {
		$wp_taxonomies[ $taxonomy_name ]->show_in_rest = true;
		$wp_taxonomies[ $taxonomy_name ]->rest_base = $taxonomy_name;
		$wp_taxonomies[ $taxonomy_name ]->rest_controller_class = 'WP_REST_Terms_Controller';
	}

	$taxonomy_name = 'property_status';

	if ( isset( $wp_taxonomies[ $taxonomy_name ] ) ) {
		$wp_taxonomies[ $taxonomy_name ]->show_in_rest = true;
		$wp_taxonomies[ $taxonomy_name ]->rest_base = $taxonomy_name;
		$wp_taxonomies[ $taxonomy_name ]->rest_controller_class = 'WP_REST_Terms_Controller';
	}


}


add_action( 'rest_api_init', array( 'AddNewHouse', 'register_endpoints' ) );