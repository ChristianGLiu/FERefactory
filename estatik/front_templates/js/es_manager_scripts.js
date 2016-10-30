 
jQuery(document).ready(function(e) { 
 
	
	jQuery('.es_message').click(function(){
		jQuery(this).removeClass('es_error es_success').text('');
	})
 
});
 
// function for manager Neigh Insertion
function es_neigh_insertion(){
	
		var es_neigh_title = jQuery("#es_neigh_title").val();
			if((es_neigh_title=="") || (document.getElementById("es_neigh_title").defaultValue == es_neigh_title)){
				jQuery("#es_neigh_message").addClass('es_error').text('pleae fill your field.');
				return false;
			}
			
			jQuery("#es_neigh_add_loader").show();
			 
			jQuery.ajax({
			type: 'POST',   // Adding Post method
			url: estatik_ajax.ajaxurl, // Including ajax file
			data: {"action": "es_neigh_insertion", "es_neigh_title":es_neigh_title, "prop_id":prop_id}, 
			success: function(data){ // Show returned data using the function.
				
				jQuery("#es_neigh_add_loader").hide();
				
				jQuery("#es_neigh_message").removeClass('es_error').addClass('es_success').text('field has been added.');
				
				setTimeout(function(){
					
					jQuery("#es_neigh_message").removeClass('es_success').text('');
						
				},2000)
		 
				jQuery("#es_neigh_title").val(document.getElementById("es_neigh_title").defaultValue);
				
				jQuery("#es_neigh_listing").html(data);
 
			}
		});
		
}
// function for manager neigh delete
function es_neigh_delete(obj){
	
	var es_neigh_id = jQuery(obj).siblings('#es_neigh_id').val();
 	 
	
		var result = confirm("Are you sure you want to delete it?");
		if (result==false) {
			
			return false
			
		} 
		jQuery(obj).siblings("#es_neigh_loader").show();
		 
		jQuery.ajax({
		type: 'POST',   // Adding Post method
		url: estatik_ajax.ajaxurl, // Including ajax file
		data: {"action": "es_neigh_delete", "es_neigh_id":es_neigh_id, "prop_id":prop_id}, 
		success: function(data){ // Show returned data using the function.
			
			jQuery("#es_neigh_loader").hide();
			
			jQuery("#es_neigh_message").addClass('es_success').text('field has been deleted.');
			
			setTimeout(function(){
				
				jQuery("#es_neigh_message").removeClass('es_success').text('');
					
			},2000)
			jQuery("#es_neigh_listing").html(data);
		}
	});
		
}
 
 
 
// function for manager feature Insertion
function es_feature_insertion(){
	
		var es_feature_title = jQuery("#es_feature_title").val();
		var prop_feature = jQuery("#prop_feature").val();
	 
			if((es_feature_title=="") || (document.getElementById("es_feature_title").defaultValue == es_feature_title)){
				jQuery("#es_feature_message").addClass('es_error').text('pleae fill your field.');
				return false;
			}
			
			jQuery("#es_feature_add_loader").show();
			 
			jQuery.ajax({
			type: 'POST',   // Adding Post method
			url: estatik_ajax.ajaxurl, // Including ajax file
			data: {"action": "es_feature_insertion", "es_feature_title":es_feature_title, "prop_feature":prop_feature, "prop_id":prop_id}, 
			success: function(data){ // Show returned data using the function.
				
				jQuery("#es_feature_add_loader").hide();
				
				jQuery("#es_feature_message").removeClass('es_error').addClass('es_success').text('field has been added.');
				
				setTimeout(function(){
					
					jQuery("#es_feature_message").removeClass('es_success').text('');
						
				},2000)
		 
				jQuery("#es_feature_title").val(document.getElementById("es_feature_title").defaultValue);
				
				jQuery("#es_feature_listing").html(data);
 
			}
		});
		
}
// function for manager feature delete
function es_feature_delete(obj){
	
	var es_feature_id = jQuery(obj).siblings('#es_feature_id').val();
	var prop_feature = jQuery("#prop_feature").val();
 
		var result = confirm("Are you sure you want to delete it?");
		if (result==false) {
			
			return false
			
		} 
		jQuery(obj).siblings("#es_feature_loader").show();
		 
		jQuery.ajax({
		type: 'POST',   // Adding Post method
		url: estatik_ajax.ajaxurl, // Including ajax file
		data: {"action": "es_feature_delete", "es_feature_id":es_feature_id, "prop_id":prop_id}, 
		success: function(data){ // Show returned data using the function.
			
			jQuery("#es_feature_loader").hide();
			
			jQuery("#es_feature_message").addClass('es_success').text('field has been deleted.');
			
			setTimeout(function(){
				
				jQuery("#es_feature_message").removeClass('es_success').text('');
					
			},2000)
			jQuery("#es_feature_listing").html(data);
		}
	});
		
}
// function for manager appliance Insertion
function es_appliance_insertion(){
	
		var es_appliance_title = jQuery("#es_appliance_title").val();
			if((es_appliance_title=="") || (document.getElementById("es_appliance_title").defaultValue == es_appliance_title)){
				jQuery("#es_appliance_message").addClass('es_error').text('pleae fill your field.');
				return false;
			}
			
			jQuery("#es_appliance_add_loader").show();
			 
			jQuery.ajax({
			type: 'POST',   // Adding Post method
			url: estatik_ajax.ajaxurl, // Including ajax file
			data: {"action": "es_appliance_insertion", "es_appliance_title":es_appliance_title, "prop_id":prop_id}, 
			success: function(data){ // Show returned data using the function.
				
				jQuery("#es_appliance_add_loader").hide();
				
				jQuery("#es_appliance_message").removeClass('es_error').addClass('es_success').text('field has been added.');
				
				setTimeout(function(){
					
					jQuery("#es_appliance_message").removeClass('es_success').text('');
						
				},2000)
		 
				jQuery("#es_appliance_title").val(document.getElementById("es_appliance_title").defaultValue);
				
				jQuery("#es_appliance_listing").html(data);
 
			}
		});
		
}
// function for manager appliance delete
function es_appliance_delete(obj){
	
	var es_appliance_id = jQuery(obj).siblings('#es_appliance_id').val();
	var prop_appliance = jQuery("#prop_appliance").val();
 
		var result = confirm("Are you sure you want to delete it?");
		if (result==false) {
			
			return false
			
		} 
		jQuery(obj).siblings("#es_appliance_loader").show();
		 
		jQuery.ajax({
		type: 'POST',   // Adding Post method
		url: estatik_ajax.ajaxurl, // Including ajax file
		data: {"action": "es_appliance_delete", "es_appliance_id":es_appliance_id, "prop_id":prop_id}, 
		success: function(data){ // Show returned data using the function.
			
			jQuery("#es_appliance_loader").hide();
			
			jQuery("#es_appliance_message").addClass('es_success').text('field has been deleted.');
			
			setTimeout(function(){
				
				jQuery("#es_appliance_message").removeClass('es_success').text('');
					
			},2000)
			jQuery("#es_appliance_listing").html(data);
		}
	});
		
}
  