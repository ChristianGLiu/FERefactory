<?php 
global $wpdb;
 
if(isset($_GET['del'])){
	$wpdb->delete( $wpdb->prefix.'estatik_agents', array( 'agent_id' => $_GET['del'] ) );
	wp_delete_user( $_GET['del'] );
}
if(isset($_POST['agent_id'])){
 
	$agent_ids = array_reverse($_POST['agent_id']);
	
	if(!empty($agent_ids)){
	
		for($i=0; $i<count($agent_ids); $i++){
			
			
			if(isset($_POST['es_selcted_copy']) && $_POST['es_selcted_copy']=='yes'){
				
				$select_result = new stdClass;
				
				$select_result = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'estatik_agents WHERE agent_id = '.$agent_ids[$i] );	
 
				$user_data = array(
					'ID' => '',
					'user_pass' => wp_generate_password(),
					'user_login' => 'copy-'.$select_result->agent_user_name,
					'user_email' => 'copy-'.$select_result->agent_email,
					'role' => 'agent_role' // Use default role or another role, e.g. 'editor'
				);
				
				$user_id = wp_insert_user( $user_data );
			 
				$wpdb->insert(
				$wpdb->prefix.'estatik_agents',
				array(
						'agent_id' 				=> $user_id,
						'agent_name' 			=> $select_result->agent_name,
						'agent_user_name' 		=> 'copy-'.$select_result->agent_user_name, 
						'agent_email' 			=> 'copy-'.$select_result->agent_email, 
						'agent_company' 	  	=> $select_result->agent_company, 
						'agent_sold_prop' 		=> $select_result->agent_sold_prop, 
						'agent_tel' 			=> $select_result->agent_tel, 
						'agent_web' 			=> $select_result->agent_web, 
						'agent_rating' 			=> $select_result->agent_rating, 
						'agent_about' 			=> $select_result->agent_about, 
						'agent_pic' 			=> $select_result->agent_pic, 
						'agent_meta' 			=> $select_result->agent_meta,
						'agent_status' 			=> '1'
					)
				);
				
			}
			
			
			if(isset($_POST['es_selcted_del']) && $_POST['es_selcted_del']=='yes'){
				$wpdb->delete( $wpdb->prefix.'estatik_agents', array( 'agent_id' => $agent_ids[$i] ) );	
				wp_delete_user( $agent_ids[$i] );	
			}
			
			if(isset($_POST['es_selcted_publish']) && $_POST['es_selcted_publish']=='yes'){
				$wpdb->update( $wpdb->prefix.'estatik_agents', array( 'agent_status' => 1 ), array( 'agent_id' => $agent_ids[$i] ) );	
			}
			
			if(isset($_POST['es_selcted_unpublish']) && $_POST['es_selcted_unpublish']=='yes'){
				$wpdb->update( $wpdb->prefix.'estatik_agents', array( 'agent_status' => 0 ), array( 'agent_id' => $agent_ids[$i] ) );	
			}
			
			
		}
	
	}
}
 
 
?>
<div class="es_wrapper"> 
 	
    <input type="hidden" value="<?php _e( "Please select agents you want to copy.", "es-plugin" );  ?>" id="selAgentsToCopy"  />
    <input type="hidden" value="<?php _e( "Please select agents you want to delete.", "es-plugin" );  ?>" id="selAgentsToDelete"  />
    <input type="hidden" value="<?php _e( "Please select agents you want to publish.", "es-plugin" );  ?>" id="selAgentsToPublish"  />
    <input type="hidden" value="<?php _e( "Please select agents you want to unpublish.", "es-plugin" ); ?>" id="selAgentsToUnPublish"  />
    
    <input type="hidden" value="<?php _e( "Are you sure you want to Copy it?", "es-plugin" );  ?>" id="sureToCopy"  />
    <input type="hidden" value="<?php _e( "Are you sure you want to delete it?", "es-plugin" );  ?>" id="sureToDelete"  />
    <input type="hidden" value="<?php _e( "Are you sure you want to publish it?", "es-plugin" ); ?>" id="sureToPublish"  />
    <input type="hidden" value="<?php _e( "Are you sure you want to unpublish it?", "es-plugin" ); ?>" id="sureToUnPublish"  />
    
    <div class="es_alert_popup" id="select_popup">
    	<div class="es_alert_popup_overlay"></div>
        <div class="es_alert_popup_in boxSizing">
        	<p></p>
            <ul>
            	<li><a class="es_ok" href="javascript:void(0)"><?php _e( "OK", "es-plugin" ); ?></a></li>
            </ul>
            <a href="javascript:void(0)" class="es_close_popup"></a>
        </div>
    </div>
    
    <div class="es_alert_popup" id="sure_popup">
    	<div class="es_alert_popup_overlay"></div>
        <div class="es_alert_popup_in boxSizing">
        	<p></p>
            <ul>
            	<li><a class="es_ok" href="javascript:void(0)"><?php _e( "OK", "es-plugin" ); ?></a></li>
                <li><a class="es_cancel" href="javascript:void(0)"><?php _e( "Cancel", "es-plugin" ); ?></a></li>
            </ul>
            <a href="javascript:void(0)" class="es_close_popup"></a>
        </div>
    </div>
    
    <div class="es_header clearFix">
        <h2><?php _e( "Agents", "es-plugin" ); ?></h2>
        <h3><img src="<?php echo DIR_URL.'admin_template/';?>images/estatik_pro.png" alt="#" /><small>Ver. <?php echo es_plugin_version(); ?></small></h3>
    </div>
    
    <div class="es_all_listing_search clearFix" id="es_agent_listing_search">
 
        <div class="es_all_listing_search_upper">
        	<form method="post" id="agent_action" action="<?php echo admin_url()?>admin.php?page=es_agents">
                <div class="es_search_filter clearFix">
                    <label><?php _e( "Filter by", "es-plugin" ); ?>:</label>
                    <select name="agent_company">
                            <option value=""><?php _e( "Company", "es-plugin" ); ?></option>
							<?php $sql = 'SELECT distinct(agent_company) as agent_company FROM '.$wpdb->prefix.'estatik_agents';
								$es_compnay_listing = $wpdb->get_results($sql);
								if(!empty($es_compnay_listing)) {	
									foreach($es_compnay_listing as $list) {
										$selected = (isset($_POST['agent_company']) && $_POST['agent_company']==$list->agent_company) ? 'selected="selected"' : "";
										echo '<option '.$selected.' value="'.$list->agent_company.'">'.$list->agent_company.'</option>';		
									}
								}
							 ?>
                    </select>
                    <select name="agent_rating">
                        <option value=""><?php _e( "Rating", "es-plugin" ); ?></option>
                        <option <?php if (isset($_POST['agent_rating']) && $_POST['agent_rating']=='bad') { echo 'selected="selected"'; } ?> value="bad"><?php _e( "Bad", "es-plugin" ); ?></option>
                        <option <?php if (isset($_POST['agent_rating']) && $_POST['agent_rating']=='poor') { echo 'selected="selected"'; } ?> value="poor"><?php _e( "Poor", "es-plugin" ); ?></option>
                        <option <?php if (isset($_POST['agent_rating']) && $_POST['agent_rating']=='regular') { echo 'selected="selected"'; } ?> value="regular"><?php _e( "Regular", "es-plugin" ); ?></option>
                        <option <?php if (isset($_POST['agent_rating']) && $_POST['agent_rating']=='good') { echo 'selected="selected"'; } ?> value="good"><?php _e( "Good", "es-plugin" ); ?></option>
                        <option <?php if (isset($_POST['agent_rating']) && $_POST['agent_rating']=='excellent') { echo 'selected="selected"'; } ?> value="excellent"><?php _e( "Excellent", "es-plugin" ); ?></option>
                    </select>
                </div>
                <div class="es_search_prop clearFix">
                    <label><?php _e( "By Name", "es-plugin" ); ?>:</label>
                    <input name="agent_name" value="<?php if (isset($_POST['agent_name'])) { echo $_POST["agent_name"]; } ?>" type="text" />
                    <input type="submit" name="search_list" value="<?php _e( "Search", "es-plugin" ); ?>" />
                    <input type="button" value="<?php _e( "Reset", "es-plugin" ); ?>" onclick="window.location='admin.php?page=es_agents'" />
                </div>
                 
            </form>
        </div>
        
        <div class="es_manage_listing clearFix">
        	<label><?php _e( "Manage", "es-plugin" ); ?>:</label>
            <ul>
                <li><a href="javascript:void(0)" id="es_listing_select_all"><?php _e( "Select all", "es-plugin" ); ?></a></li>
                <li><a href="javascript:void(0)" id="es_listing_undo_selection"><?php _e( "Undo selection", "es-plugin" ); ?></a></li>
                <li><a href="javascript:void(0)" id="es_listing_copy"><?php _e( "Copy", "es-plugin" ); ?></a></li>
                <li><a href="javascript:void(0)" id="es_listing_del"><?php _e( "Delete", "es-plugin" ); ?></a></li>
                <li><a href="javascript:void(0)" id="es_listing_publish"><?php _e( "Publish", "es-plugin" ); ?></a></li>
                <li><a href="javascript:void(0)" id="es_listing_unpublish"><?php _e( "Unpublish", "es-plugin" ); ?></a></li>
            </ul>
        </div>
        
    </div>
    
    <?php if(isset($_GET['del']) && !isset($_POST['es_selcted_copy']) && !isset($_POST['es_selcted_del']) && !isset($_POST['es_selcted_publish']) && !isset($_POST['es_selcted_unpublish'])){ ?>
        <div class="es_success"><?php _e( "Agent has been deleted.", "es-plugin" ); ?></div>	 
	<?php } ?>
    
    <?php if(isset($_POST['es_selcted_copy']) && $_POST['es_selcted_copy']=='yes'){ ?>
        <div class="es_success"><?php _e( "Selected Agents have been copied.", "es-plugin" ); ?></div>	 
	<?php } ?>
	
	<?php if(isset($_POST['es_selcted_del']) && $_POST['es_selcted_del']=='yes'){ ?>
        <div class="es_success"><?php _e( "Selected Agents have been deleted.", "es-plugin" ); ?></div>	 
	<?php } ?>
    
    <?php if(isset($_POST['es_selcted_publish']) && $_POST['es_selcted_publish']=='yes'){ ?>
        <div class="es_success"><?php _e( "Selected Agents have been published.", "es-plugin" ); ?></div>	 
	<?php } ?>
    
    <?php if(isset($_POST['es_selcted_unpublish']) && $_POST['es_selcted_unpublish']=='yes'){ ?>
        <div class="es_success"><?php _e( "Selected Agents have been unpublished.", "es-plugin" ); ?></div>	 
	<?php } ?>
    
    <?php if(isset($_POST['search_list'])){ ?>
        <div class="es_success"><?php _e( "Your Search results.", "es-plugin" ); ?></div>	 
	<?php } ?>
    
    
    
    
    <div class="es_content_in clearFix" id="es_agent_listing">
 		
        <div class="es_all_listing_head clearFix">
        	<div>
            	<input type="checkbox" value=""  />
            </div>
            <div class="hide-phone">
            	<?php _e( "Agent ID", "es-plugin" ); ?>
            </div>
            <div>
            	<?php _e( "Username", "es-plugin" ); ?>
            </div>
            <div class="hide-phone">
            	<?php _e( "Name", "es-plugin" ); ?>
            </div>
            <div>
            	<?php _e( "Email", "es-plugin" ); ?>
            </div>
            <div class="hide-phone">
            	<?php _e( "Company", "es-plugin" ); ?>
            </div>
            <div class="hide-ipad hide-phone">
            	<?php _e( "Properties Q-ty", "es-plugin" ); ?>
            </div>
            <div class="hide-ipad hide-phone">
            	<?php _e( "Rating", "es-plugin" ); ?>
            </div>
            <div class="hide-phone"> 
            	<?php _e( "Status", "es-plugin" ); ?>
            </div>
        </div>
        
        <div class="es_all_listing clearFix">
        	<form id="listing_actions" action="" method="post">
 
                <input type="hidden" id="es_selcted_copy" name="es_selcted_copy" value="no" />
                <input type="hidden" id="es_selcted_del" name="es_selcted_del" value="no" />
                <input type="hidden" id="es_selcted_publish" name="es_selcted_publish" value="no" />
                <input type="hidden" id="es_selcted_unpublish" name="es_selcted_unpublish" value="no" />
            
                <ul>
                    <?php  
						$agent_company 		= (isset($_POST['agent_company'])) ? sanitize_text_field($_POST['agent_company']) : "";
						$agent_rating 		= (isset($_POST['agent_rating'])) ? sanitize_text_field($_POST['agent_rating']) : "";
						$agent_name 		= (isset($_POST['agent_name'])) ? sanitize_text_field($_POST['agent_name']) : "";
   
                        $where = "";
                        $where_array = array();
                        if($agent_company != ''){
                            $where_array[]  =	'agent_company 	=  "'.$agent_company.'"';
                        }
                        if($agent_rating != ''){
                            $where_array[]  =	'agent_rating 	=  "'.$agent_rating.'"';
                        }
						if($agent_name != ''){
                            $where_array[]  =	'agent_name like "%'.$agent_name.'%"';
                        }
                 
						
                        $where =  implode(" AND ",$where_array);
    			 
                        if(!empty($where_array)){
                        
                            $sql = 'SELECT * FROM '.$wpdb->prefix.'estatik_agents WHERE '.$where.' order by agent_id desc';
                   
                        }
                        else
                        {
                            $sql = 'SELECT * FROM '.$wpdb->prefix.'estatik_agents order by agent_id desc'; 		
                        }
                        
						$es_my_listing = $wpdb->get_results($sql); 
			 
                        if(!empty($es_my_listing)) {
                            foreach($es_my_listing as $list) {
                    ?>
     
                                <li class="clearFix">
                                    <div>
                                        <p><input type="checkbox" name="agent_id[]" value="<?php echo $list->agent_id?>"  /></p>
                                    </div>
                                    <div class="hide-phone">
                                        <p><?php echo $list->agent_id?></p>
                                    </div>
                                    <div>
                                        <p><a href="<?php echo admin_url();?>admin.php?page=es_add_new_agent&agent_id=<?php echo $list->agent_id?>"><?php echo $list->agent_user_name?></a></p>
                                    </div>
                                    <div class="hide-phone">
                                        <p><a href="<?php echo admin_url();?>admin.php?page=es_add_new_agent&agent_id=<?php echo $list->agent_id?>"><?php echo $list->agent_name?></a></p>
                                    </div>
                                    <div>
                                        <p><?php echo $list->agent_email?></p>
                                    </div>
                                    <div class="hide-phone">
                                        <p><?php echo $list->agent_company?></p>
                                    </div>
                                    <div class="hide-ipad hide-phone">
                                        <?php $agent_prop_quantity = $wpdb->get_var( "SELECT COUNT(agent_id) as total FROM ".$wpdb->prefix."estatik_properties WHERE agent_id = ".$list->agent_id." AND prop_pub_unpub = 1");?>
                                        <p><?php echo $agent_prop_quantity?></p>
                                    </div>
                                    <div class="hide-ipad hide-phone">
                                        <div class="es_agent_rating es_rating_<?php echo $list->agent_rating?>">
                                            <a href="javascript:void(0)"></a>
                                            <a href="javascript:void(0)"></a>
                                            <a href="javascript:void(0)"></a>
                                            <a href="javascript:void(0)"></a>
                                            <a href="javascript:void(0)"></a>
                                        </div>
                                    </div>
                                    <div class="hide-phone">
                                        <p><?php if($list->agent_status==0) { _e( "Unactive", "es-plugin" ); } else { _e( "Active", "es-plugin" );  } ?></p>
                                    </div>
                                    <span class="es_list_edit_del">
                                        <a href="<?php echo admin_url();?>admin.php?page=es_add_new_agent&agent_id=<?php echo $list->agent_id?>"><?php _e( "Edit", "es-plugin" ); ?></a>
                                        <a href="<?php echo admin_url();?>admin.php?page=es_agents&del=<?php echo $list->agent_id?>"><?php _e( "Delete", "es-plugin" ); ?></a>
                                    </span>
                                    
                                </li>
                    
                        <?php 
                            }
                            
                        } else {
                    
                        echo '<li class="es_no_record">'.__( "No record Found.", "es-plugin" ).'</li>';	
                    
                        } 
                        ?> 
                </ul>
            
            </form>
        </div>
        
    </div>
 
</div>
  
 
 