<?php
function es_user_name_error(){
	$agent_user_name = sanitize_text_field($_POST['agent_user_name']);
	$agent_id = sanitize_text_field($_POST['agent_id']);
	global $wpdb;
	$where = '';
	if($agent_id != ""){
		$where = ' and ID != '.$agent_id;
	}
	$sql = 'SELECT * from '.$wpdb->prefix.'users WHERE user_login = "'.$agent_user_name.'"'.$where;
 
	$user_name_exist = $wpdb->get_results($sql);
 
	if(!empty($user_name_exist)){
		
		echo 'This user name is already registered. Please choose another one.';		
	} 
die();
}
add_action('wp_ajax_es_user_name_error', 'es_user_name_error'); 
add_action('wp_ajax_nopriv_es_user_name_error', 'es_user_name_error');
function es_email_error(){
	$es_email = sanitize_email($_POST['es_email']);
	$agent_id = sanitize_text_field($_POST['agent_id']);
	global $wpdb;
	$where = '';
	if($agent_id != ""){
		$where = ' and ID != '.$agent_id;
	}
	$sql = 'SELECT * from '.$wpdb->prefix.'users WHERE user_email = "'.$es_email.'"'.$where;
 
	$email_exist = $wpdb->get_results($sql);
 
	if(!empty($email_exist)){
		
		echo 'This email is already exist. Please choose another one.';		
	} 
die();
}
add_action('wp_ajax_es_email_error', 'es_email_error'); 
add_action('wp_ajax_nopriv_es_email_error', 'es_email_error');