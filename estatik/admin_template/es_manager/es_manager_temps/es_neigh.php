<?php
	global $wpdb;
	
	$selected_neigh = array();
	$selected_neigh_distance = array();
 	
	if( isset($_POST['prop_neigh']) && $_POST['prop_neigh']==1){
		$prop_neigh=1;		 
	 }
	 
	 if(isset($_POST['prop_id']) && $_POST['prop_id']!=""){
		$prop_id = $_POST['prop_id'];
		$prop_neigh=1;		 
	 }
	 
	 if(!empty($prop_id)){
 
		 $es_neigh_listing = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.'estatik_properties_neighboarhood WHERE prop_id="'.$prop_id.'"');	
		 foreach($es_neigh_listing as $list) {	
			$selected_neigh[] = $list->neigh_id;
			$selected_neigh_distance[$list->neigh_id] = $list->neigh_distance;
		 }
		 //print_r($selected_neigh); 
		 //exit;
	 }
	
	$es_neigh_listing = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.'estatik_manager_neighboarhood' );		
	if(!empty($es_neigh_listing)) {	
		
?>            
	<?php 
 
	$i=0;
	
	foreach($es_neigh_listing as $list) {	
	
	?>
		<li id="neigh_<?php echo $list->neigh_id?>" class="<?php if(in_array($list->neigh_id,$selected_neigh)) { echo 'active'; } ?>">
			<label>
				<?php echo $list->neigh_title?>
                <?php if(isset($prop_neigh) && $prop_neigh==1) { ?>
            		<input type="checkbox" name="es_prop_neigh[<?php echo $i?>]" <?php if(in_array($list->neigh_id,$selected_neigh)) { echo 'checked="checked"'; } ?> value="<?php echo $list->neigh_id?>" />
            	<?php } ?>
            </label>
			<?php if(isset($prop_neigh) && $prop_neigh==1) { ?>
                <input type="text" name="neigh_distance[<?php echo $i?>]"  value="<?php if(in_array($list->neigh_id,$selected_neigh)) { echo $selected_neigh_distance[$list->neigh_id]; } else { echo"text/number"; }?>" onFocus="es_neigh_prop_text(this); if(this.value == 'text/number') { this.value = ''; }" onBlur="if(this.value == '') { this.value = 'text/number'; }" />
            <?php } ?>
			<small onclick="es_neigh_delete(this)"></small>
			<span class="es_field_loader es_neigh_loader"></span>
			<input type="hidden" value="<?php echo $list->neigh_id?>" name="es_neigh_id" class="es_neigh_id" />
		</li>
		
	<?php 
	
	$i++;
	
	} ?>
 
<?php } else { ?>
	<p><?php _e( "No record found. Please add new one.", "es-plugin" ); ?></p>
<?php } ?>