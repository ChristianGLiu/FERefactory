
 
var user_validate = ""; 
var email_validate = ""; 
 
function emailValidation(email) { 
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
} 
 
function emailvalidate(obj){
	
	email_validate = "";
	
	jQuery("#email_error").text('');
	
	var es_email = jQuery('#agent_email').val();
	
	var agent_id = jQuery("#agent_id").val();
	
	var agent_user_name = jQuery("#agent_user_name").val();
	
	if (es_email=='') {
	
		jQuery('#email_error').text(jQuery("#agentEmailRequired").val());
		
		return false;
		
	}
	
	if (!emailValidation(es_email)) {
	
		jQuery('#email_error').text(jQuery("#agentEmailNotValid").val());
	
	}else{
	
		jQuery("#es_email_loader").show();
  
		jQuery.ajax({
		type: 'POST',   // Adding Post method
		url: estatik_ajax.ajaxurl, // Including ajax file
		data: {"action": "es_email_error", "es_email":es_email,"agent_id":agent_id,"agent_user_name":agent_user_name}, 
		success: function(data){ // Show returned data using the function.
			
			jQuery("#es_email_loader").hide();
			
			jQuery("#email_error").text(data);
			
			if(data != jQuery("#agentEmailExist").val()) {
				email_validate = "ok";
			}
			
		}
		});
	  
	} 
	
} 
function uservalidate(obj){
	
	user_validate = "";
	
	jQuery("#user_name_error").text('');
 
	var agent_id = jQuery("#agent_id").val();
	
	var agent_user_name = jQuery("#agent_user_name").val();
 	
	if (agent_user_name=="") {
	
		jQuery('#user_name_error').text(jQuery("#agentUserNameRequired").val());
	
	}else{
	
		jQuery("#es_user_name_loader").show();
		jQuery.ajax({
		type: 'POST',   // Adding Post method
		url: estatik_ajax.ajaxurl, // Including ajax file
		data: {"action": "es_user_name_error","agent_id":agent_id,"agent_user_name":agent_user_name}, 
		success: function(data){ // Show returned data using the function.
			
			jQuery("#es_user_name_loader").hide();
			
			jQuery("#user_name_error").text(data);
			
			if(data != jQuery("#agentUserNameExist").val()) {
				user_validate = "ok";
			}
			
		}
		});
	  
	} 
 
} 
function es_agent_submit(){
	
	if (email_validate=="ok" && user_validate=="ok") {
 
		 return true;
		 
	}else{
 
		uservalidate('#agent_user_name');
		emailvalidate('#agent_email');
			
	} 
	
	return false;
}
function es_agent_pic_url(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
      jQuery('.es_agent_photo img').attr("src", e.target.result);
    };
    reader.readAsDataURL(input.files[0]);
  }
}
// function for AGENTS New field Insertion
function es_prop_detail_add_new(){
	
		var es_agent_add_new_title = jQuery("#es_agent_add_new_title").val();
		if((es_agent_add_new_title=="") || (document.getElementById("es_agent_add_new_title").defaultValue == es_agent_add_new_title)){
			jQuery("#es_agent_add_new_error").addClass('error').text(jQuery("#fillYourField").val());
			return false;
		} 
		
		jQuery('.es_message').removeClass('es_error').text('');
		
		jQuery("#es_agent_info_in").append('<div class="es_agent_field clearFix"><label>'+es_agent_add_new_title+':</label><input type="text" name="agent_meta[\''+es_agent_add_new_title+'\']" value=""><a href="javascript:void(0)" onclick="es_field_del(this)" class="field_del"></a></div>')
		
		jQuery("#es_agent_add_new_title").val(document.getElementById("es_agent_add_new_title").defaultValue);
		
}
function es_field_del(obj){
	
	jQuery(obj).parents('.es_agent_field').remove();
	
}
 
