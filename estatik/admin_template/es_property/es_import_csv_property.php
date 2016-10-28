<?php
global $wpdb;
 
 
	if(isset($_FILES['prop_csv']) && $_FILES['prop_csv']!=""){
		
		$field_array = array("prop_title","prop_price","prop_bedrooms","prop_bathrooms","prop_floors","prop_area","prop_lotsize","prop_builtin","prop_description");
		
		$agent_id 			= sanitize_text_field($_POST['agent_id']);
		
		$prop_csv = $_FILES['prop_csv'];
		
		$allowed =  array('csv');
		$filename = $_FILES['prop_csv']['name'];
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		if(in_array($ext,$allowed) ) {
		
			$row = 1;
			if (($handle = fopen($prop_csv['tmp_name'], "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					$num = count($data);
					//echo "<p> $num fields in line $row: <br /></p>\n";
					$row++;
					if($row>2){
					$insert	=	array();
					$prop_type	=	'';
					$prop_category	=	'';
					
					for ($c=0; $c < $num; $c++) {
						//echo $data[$c] . "<br />\n";
						
						if($c < count($field_array)){
							$insert[$field_array[$c]]	=	$data[$c];	
						}
						 
					}
						
						$prop_type			=$data[count($data)-8]; 
						
						$prop_category		=$data[count($data)-7]; 
						
						$prop_status		=$data[count($data)-6]; 
						
						$prop_featured		=$data[count($data)-5]; 
						$prop_hot			=$data[count($data)-4]; 
						
						$prop_open_house	=$data[count($data)-3]; 
						
						$prop_foreclosure	=$data[count($data)-2]; 
						
						$prop_period		=$data[count($data)-1];
						 
						
						$insert['agent_id']			=	$agent_id;
						$insert['prop_date_added']	=	time();
						$insert['prop_pub_unpub']	=	1;
						
						$insert['prop_type']		=	es_common('type_id',$wpdb->prefix.'estatik_manager_types','type_title',$prop_type);
						
						$insert['prop_category']	=	es_common('cat_id',$wpdb->prefix.'estatik_manager_categories','cat_title',$prop_category);
						
						$insert['prop_status']		=	es_common('status_id',$wpdb->prefix.'estatik_manager_status','status_title',$prop_status);
						
						if($prop_featured=="yes"){
							$insert['prop_featured']	=	1;
						}else{
							$insert['prop_featured']	=	0;
						}
						
						if($prop_hot=="yes"){
							$insert['prop_hot']	=	1;
						}else{
							$insert['prop_hot']	=	0;
						}
						
						if($prop_open_house=="yes"){
							$insert['prop_open_house']	=	1;
						}else{
							$insert['prop_open_house']	=	0;
						}
						
						if($prop_foreclosure=="yes"){
							$insert['prop_foreclosure']	=	1;
						}else{
							$insert['prop_foreclosure']	=	0;
						}
						
						$insert['prop_period']		=	es_common('period_id',$wpdb->prefix.'estatik_manager_rent_period','period_title',$prop_period);
		 				
						//print_r($insert);
						
						
						$my_post = array(
						  'post_title'    => $insert['prop_title'],
						  'post_status'   => 'publish',
						  'post_content'  =>  '[es_single_property]',
						  'post_author'   => $agent_id,
						  'post_type'     => 'properties',
						);
				 
						// Insert the post into the database
						$post_id = wp_insert_post( $my_post );
						
						$insert['prop_id']	=	$post_id;
						
						wp_set_object_terms( $post_id, (int)$insert['prop_category'], 'property_category');
						wp_set_object_terms( $post_id, (int)$insert['prop_status'], 'property_status');
						wp_set_object_terms( $post_id, (int)$insert['prop_type'], 'property_type');
						
						
						$wpdb->insert(
							$wpdb->prefix.'estatik_properties',$insert
						);
						
					}
				}
				fclose($handle);
			}
			
			if(isset($_POST['es_prop_save_close'])){
		
				wp_redirect('?page=es_my_listings',301); exit;	
		
			}
		
		}
 
	} 
 
	
	function es_common($colum_name,$table_name,$where_field,$where_val)
	{		
		global $wpdb;
		$sql = 'SELECT '.$colum_name.' as common FROM '.$table_name.' WHERE '.$where_field.' = "'.$where_val.'"';
		$es_type = $wpdb->get_row($sql);		
		if(isset($es_type)){
			return $es_type->common;
		}
	}
 
?>
 
<div class="es_wrapper"> 
 	
     
    
    <div class="es_header clearFix">	
        <h2><?php _e( "New Property", "es-plugin" ); ?></h2>
        <h3><img src="<?php echo DIR_URL.'admin_template/';?>images/estatik_pro.png" alt="#" /><small>Ver. <?php echo es_plugin_version(); ?></small></h3>
    </div>
    
    <form method="post" enctype="multipart/form-data" id="es_prop_insertion" action="">
     
    
        <div class="esHead clearFix">
            <p class="floatLeft"><?php _e( "Please Import your property detail with CSV sample and click save to finish.", "es-plugin" ); ?></p>
            <input type="submit" value="<?php _e( "Save & Close", "es-plugin" ); ?>" class="save_close" name="es_prop_save_close" />
            <input type="submit" value="<?php _e( "Save", "es-plugin" ); ?>" name="es_prop_save" />
        </div>
 
    
    <?php  if(isset($_FILES['prop_csv']) && $_FILES['prop_csv']!="" && in_array($ext,$allowed) ){  ?>
        
        <div class="es_success"><?php _e( "Property has been Added.", "es-plugin" ); ?></div>	
    
    <?php } ?>
    
    <?php  if(isset($_FILES['prop_csv']) && $_FILES['prop_csv']!="" && !in_array($ext,$allowed) ){  ?>
        
       <div class="es_error"><?php _e( "Please choose CSV file. or download sample file for refrence.", "es-plugin" ); ?></div>
    
    <?php } ?>
 
    
    <div class="es_content_in addNewProp">
   
        
        <div class="es_tabs_contents clearFix">
            <input type="hidden" name="agent_id" value="<?php echo get_current_user_id();?>" />
			 
            <div class="new_prop_csv clearFix">
                <span><?php _e( "Download Sample File", "es-plugin" ); ?>:</span>
                <span><a download="es_property_csv_sample.csv" href="<?php echo DIR_URL.'admin_template/images/es_property_csv_sample.csv'?>"><?php _e( "Download Sample", "es-plugin" ); ?></a></span>
            </div>
            
            <div class="new_prop_csv clearFix">
                <span><?php _e( "Import CSV", "es-plugin" ); ?>:</span>
                <input type="file" name="prop_csv" value="" />
            </div>
 
        </div>
 
    </div>
    
    </form>
</div>
 