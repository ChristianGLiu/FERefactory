jQuery(document).ready(function() {
    var type ="";
    var id_url ="";
    jQuery('.mult-sel').SumoSelect({floatWidth: 200});
    jQuery('.SumoSelect').each(function(){
        if(jQuery(this).parent().hasClass('es_select')){
            jQuery(this).unwrap();
        }
        jQuery(this).next().remove();
    });
    jQuery('.es_layout input').click(function(){
        jQuery(this).parents('.es_layout').find('label').removeClass('active');
        jQuery(this).parent('label').addClass('active');
    });
    jQuery('.es_gmap_settings_field input').click(function(){
        jQuery(this).parents('.es_gmap_settings_field').find('label').removeClass('active');
        jQuery(this).parent('label').addClass('active');
    });
    jQuery('.es_images_setting_resize input').click(function(){
        jQuery(this).parents('.es_images_setting_resize').find('label').removeClass('active');
        jQuery(this).parent('label').addClass('active');
    });
    jQuery('.pink-cat-select .optWrapper ul.options li').on('click', function(){
        var pink_value = jQuery(this).data('val');
        jQuery('.blue-cat-select .optWrapper ul.options li').each(function(index){
            if(jQuery(this).data('val') == pink_value){
                jQuery('.blue-cat-select .SumoSelect select')[0].sumo.unSelectItem(index);
            }
        });
        jQuery('.green-cat-select .optWrapper ul.options li').each(function(index){
            if(jQuery(this).data('val') == pink_value){
                jQuery('.green-cat-select .SumoSelect select')[0].sumo.unSelectItem(index);
            }
        });
    });
    jQuery('.blue-cat-select .optWrapper ul.options li').on('click', function(){
        var blue_value = jQuery(this).data('val');
        jQuery('.pink-cat-select .optWrapper ul.options li').each(function(index){
            if(jQuery(this).data('val') == blue_value){
                jQuery('.pink-cat-select .SumoSelect select')[0].sumo.unSelectItem(index);
            }
        });
        jQuery('.green-cat-select .optWrapper ul.options li').each(function(index){
            if(jQuery(this).data('val') == blue_value){
                jQuery('.green-cat-select .SumoSelect select')[0].sumo.unSelectItem(index);
            }
        });
    });
    jQuery('.green-cat-select .optWrapper ul.options li').on('click', function(){
        var green_value = jQuery(this).data('val');
        jQuery('.pink-cat-select .optWrapper ul.options li').each(function(index){
            if(jQuery(this).data('val') == green_value){
                jQuery('.pink-cat-select .SumoSelect select')[0].sumo.unSelectItem(index);
            }
        });
        jQuery('.blue-cat-select .optWrapper ul.options li').each(function(index){
            if(jQuery(this).data('val') == green_value){
                jQuery('.blue-cat-select .SumoSelect select')[0].sumo.unSelectItem(index);
            }
        });
    });
    jQuery('.house-cat-select .optWrapper ul.options li').on('click', function(){
        var house_value = jQuery(this).data('val');
        jQuery('.flag-cat-select .optWrapper ul.options li').each(function(index){
            if(jQuery(this).data('val') == house_value){
                jQuery('.flag-cat-select .SumoSelect select')[0].sumo.unSelectItem(index);
            }
        });
        jQuery('.point-cat-select .optWrapper ul.options li').each(function(index){
            if(jQuery(this).data('val') == house_value){
                jQuery('.point-cat-select .SumoSelect select')[0].sumo.unSelectItem(index);
            }
        });
    });
    jQuery('.flag-cat-select .optWrapper ul.options li').on('click', function(){
        var flag_value = jQuery(this).data('val');
        jQuery('.house-cat-select .optWrapper ul.options li').each(function(index){
            if(jQuery(this).data('val') == flag_value){
                jQuery('.house-cat-select .SumoSelect select')[0].sumo.unSelectItem(index);
            }
        });
        jQuery('.point-cat-select .optWrapper ul.options li').each(function(index){
            if(jQuery(this).data('val') == flag_value){
                jQuery('.point-cat-select .SumoSelect select')[0].sumo.unSelectItem(index);
            }
        });
    });
    jQuery('.point-cat-select .optWrapper ul.options li').on('click', function(){
        var point_value = jQuery(this).data('val');
        jQuery('.flag-cat-select .optWrapper ul.options li').each(function(index){
            if(jQuery(this).data('val') == point_value){
                jQuery('.flag-cat-select .SumoSelect select')[0].sumo.unSelectItem(index);
            }
        });
        jQuery('.house-cat-select .optWrapper ul.options li').each(function(index){
            if(jQuery(this).data('val') == point_value){
                jQuery('.house-cat-select .SumoSelect select')[0].sumo.unSelectItem(index);
            }
        });
    });
    //START AGENTS SELECTS HANDLING
    jQuery('.pink-cat-select-agents .optWrapper ul.options li').on('click', function(){
        var pink_value = jQuery(this).data('val');
        jQuery('.blue-cat-select-agents .optWrapper ul.options li').each(function(index){
            if(jQuery(this).data('val') == pink_value){
                jQuery('.blue-cat-select-agents .SumoSelect select')[0].sumo.unSelectItem(index);
            }
        });
    });
    jQuery('.blue-cat-select-agents .optWrapper ul.options li').on('click', function(){
        var blue_value = jQuery(this).data('val');
        jQuery('.pink-cat-select-agents .optWrapper ul.options li').each(function(index){
            if(jQuery(this).data('val') == blue_value){
                jQuery('.pink-cat-select-agents .SumoSelect select')[0].sumo.unSelectItem(index);
            }
        });
    });
    jQuery('.house-cat-select-agents .optWrapper ul.options li').on('click', function(){
        var house_value = jQuery(this).data('val');
        jQuery('.flag-cat-select-agents .optWrapper ul.options li').each(function(index){
            if(jQuery(this).data('val') == house_value){
                jQuery('.flag-cat-select-agents .SumoSelect select')[0].sumo.unSelectItem(index);
            }
        });
        jQuery('.point-cat-select-agents .optWrapper ul.options li').each(function(index){
            if(jQuery(this).data('val') == house_value){
                jQuery('.point-cat-select-agents .SumoSelect select')[0].sumo.unSelectItem(index);
            }
        });
    });
    jQuery('.flag-cat-select-agents .optWrapper ul.options li').on('click', function(){
        var flag_value = jQuery(this).data('val');
        jQuery('.house-cat-select-agents .optWrapper ul.options li').each(function(index){
            if(jQuery(this).data('val') == flag_value){
                jQuery('.house-cat-select-agents .SumoSelect select')[0].sumo.unSelectItem(index);
            }
        });
        jQuery('.point-cat-select-agents .optWrapper ul.options li').each(function(index){
            if(jQuery(this).data('val') == flag_value){
                jQuery('.point-cat-select-agents .SumoSelect select')[0].sumo.unSelectItem(index);
            }
        });
    });
    jQuery('.point-cat-select-agents .optWrapper ul.options li').on('click', function(){
        var point_value = jQuery(this).data('val');
        jQuery('.flag-cat-select-agents .optWrapper ul.options li').each(function(index){
            if(jQuery(this).data('val') == point_value){
                jQuery('.flag-cat-select-agents .SumoSelect select')[0].sumo.unSelectItem(index);
            }
        });
        jQuery('.house-cat-select-agents .optWrapper ul.options li').each(function(index){
            if(jQuery(this).data('val') == point_value){
                jQuery('.house-cat-select-agents .SumoSelect select')[0].sumo.unSelectItem(index);
            }
        });
    });
});
