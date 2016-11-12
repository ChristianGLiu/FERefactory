<?php
/**
* Plugin Name: generate-random-user-activity
* Plugin URI: https://www.eclink.ca/
* Description: generate-random-user-activity plugin built
* Version: 1.0
* Author: Gang Liu
* Author URI: https://www.eclink.ca/
**/


defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

global $wpdb;

function get_user_roles_by_user_id( $user_id ) {
    $user_data = get_userdata( $user_id );
    return empty( $user_data ) ? array() : $user_data->roles;
}

function is_user_in_role( $user_id, $role  ) {
    return in_array( $role, get_user_roles_by_user_id( $user_id ) );
}

function generate_random_user_activity() {
	$rand_ids= $wpdb->get_results("SELECT ID FROM $wpdb->XQiviLizusers ORDER BY RAND() LIMIT 20");

$rand_ids_final = array();
	foreach ($rand_ids as $one_id) {
	    if(is_user_in_role($one_id, 'Contributor')){
           array_push($rand_ids_final,$one_id);
	    }
	}

	foreach ($rand_ids_final as $one_user){

	$activity_params = array (
     'user_id' => $one_user,
     'component' => "members",
     'type'  => 'last_activity',
     'action' => '',
     'content' => '',
     'primary_link' => '',
     'item_id' => 0,
     'date_recorded' => time() - rand(1,60),

	);
	  $wpdb->insert(
      					$wpdb->prefix.'bp_activity',
      					$activity_params
      					);

	}

	}

add_action( 'generate_random_user_activity', 'generate_random_user_activity', 25 );
