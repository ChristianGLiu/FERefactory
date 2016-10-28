jQuery(document).ready(function() {
	jQuery('.es.save_close').on('click', function() {
		jQuery('input[name=es_subscription_action]').val('saveclose');
		jQuery(this).closest('form').submit();
	});
	 jQuery('.es_wrapper select').each(function(index, element) {
		var selText = jQuery(this).find('option:selected').text(); 
		jQuery(this).wrap("<div class='es_select'></div>");
 		jQuery(this).parent('.es_select').append('<div class="es_select_arow"></div>');
		jQuery(this).parent('.es_select').find('.es_select_arow').text(selText);
    });
 
	jQuery('.es_wrapper select').change(function(){
		var selText = jQuery(this).find('option:selected').text();
		jQuery(this).parent('.es_select').find('.es_select_arow').text(selText);
	}); 
  
  
});
 