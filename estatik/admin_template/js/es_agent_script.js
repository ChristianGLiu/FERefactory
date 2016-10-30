jQuery(document).ready(function() {
    jQuery('.es_all_listing input[type="checkbox"], .es_all_listing_head input[type="checkbox"]').prop("checked",false)
    jQuery('.es_all_listing_head input[type="checkbox"]').click(function(){
        if(jQuery(this).prop("checked")==true){
            jQuery('.es_all_listing input[type="checkbox"]').prop("checked",true)
            jQuery('.es_all_listing li').addClass('active');
        } else {
            jQuery('.es_all_listing input[type="checkbox"]').prop("checked",false)
            jQuery('.es_all_listing li').removeClass('active');
        }
    });
    jQuery('.es_all_listing input[type="checkbox"]').click(function(){
        if(jQuery(this).prop("checked")==true){
            jQuery(this).parents('.es_all_listing li').addClass('active');
        } else {
            jQuery(this).parents('.es_all_listing li').removeClass('active');
        }
    });
    jQuery('#es_listing_select_all').click(function(){
        jQuery('.es_all_listing_head input[type="checkbox"], .es_all_listing input[type="checkbox"]').prop("checked",true)
        jQuery('.es_all_listing li').addClass('active');
    });
    jQuery('#es_listing_undo_selection').click(function(){
        jQuery('.es_all_listing_head input[type="checkbox"], .es_all_listing input[type="checkbox"]').prop("checked",false)
        jQuery('.es_all_listing li').removeClass('active');
    });
    jQuery('.es_cancel, .es_close_popup, .es_ok').click(function(){
        jQuery('.es_alert_popup').fadeOut(500)
    });
    jQuery('#es_listing_copy').click(function(){
        var es_select_list = "";
        jQuery('.es_all_listing li input[type="checkbox"]').each(function(){
            if(jQuery(this).prop('checked')==true){
                es_select_list = "1";
            }
        })
        if(es_select_list==''){
            jQuery('#select_popup').find('p').text(jQuery("#selAgentsToCopy").val());
            jQuery('#select_popup').fadeIn(500);
            return false;
        }
        jQuery('#sure_popup').find('p').text(jQuery("#sureToCopy").val());
        jQuery('#sure_popup').fadeIn(500);
        jQuery('.es_ok').click(function(){
            jQuery("#es_selcted_copy").val('yes');
            jQuery("#listing_actions").submit();
        });
    });
    jQuery('.es_list_edit_del a:last-child').click(function(){
        jQuery('#sure_popup').find('p').text(jQuery("#sureToDelete").val());
        jQuery('#sure_popup').find('a.es_ok').attr('href',jQuery(this).attr('href'));
        jQuery('#sure_popup').fadeIn(500);
        jQuery('.es_ok').click(function(){
            jQuery("#listing_actions").submit();
            return true;
        });
        return false;
    });
    jQuery('#es_listing_del').click(function(){
        var es_select_list = "";
        jQuery('.es_all_listing li input[type="checkbox"]').each(function(){
            if(jQuery(this).prop('checked')==true){
                es_select_list = "1";
            }
        })
        if(es_select_list==''){
            jQuery('#select_popup').find('p').text(jQuery("#selAgentsToDelete").val());
            jQuery('#select_popup').fadeIn(500);
            return false;
        }
        jQuery('#sure_popup').find('p').text(jQuery("#sureToDelete").val());
        jQuery('#sure_popup').fadeIn(500);
        jQuery('.es_ok').click(function(){
            jQuery("#es_selcted_del").val('yes');
            jQuery("#listing_actions").submit();
        });
    });
    jQuery('#es_listing_publish').click(function(){
        var es_select_list = "";
        jQuery('.es_all_listing li input[type="checkbox"]').each(function(){
            if(jQuery(this).prop('checked')==true){
                es_select_list = "1";
            }
        });
        if(es_select_list==''){
            jQuery('#select_popup').find('p').text(jQuery("#selAgentsToPublish").val());
            jQuery('#select_popup').fadeIn(500);
            return false;
        }
        jQuery('#sure_popup').find('p').text(jQuery("#sureToPublish").val());
        jQuery('#sure_popup').fadeIn(500);
        jQuery('.es_ok').click(function(){
            jQuery("#es_selcted_publish").val('yes');
            jQuery("#listing_actions").submit();
        });
    });
    jQuery('#es_listing_unpublish').click(function(){
        var es_select_list = "";
        jQuery('.es_all_listing li input[type="checkbox"]').each(function(){
            if(jQuery(this).prop('checked')==true){
                es_select_list = "1";
            }
        })
        if(es_select_list==''){
            jQuery('#select_popup').find('p').text(jQuery("#selAgentsToUnPublish").val());
            jQuery('#select_popup').fadeIn(500);
            return false;
        }
        jQuery('#sure_popup').find('p').text(jQuery("#sureToUnPublish").val());
        jQuery('#sure_popup').fadeIn(500);
        jQuery('.es_ok').click(function(){
            jQuery("#es_selcted_unpublish").val('yes');
            jQuery("#listing_actions").submit();
        });
    });
    jQuery('#es_agent_rating a').click(function(){
        var hrefVal =  jQuery(this).attr('href');
        jQuery("#agent_rating").val(hrefVal);
        jQuery('#es_agent_rating').removeAttr('class').addClass('es_agent_rating es_rating_'+hrefVal);
        return false;
    });
});
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
