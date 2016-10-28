function emailValidation(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
function get_subitems(id, parent_id, child_selector) {
    jQuery.ajax({
        url: estatik_ajax.ajaxurl,
        method: "POST",
        data: {
            'action' : 'es_get_locations',
            'id' : id,
            'type' : parent_id
        },
        dataType: 'json',
        success: function(data) {
            var
                ul = jQuery(child_selector + ' ul'),
                length = data.length,
                item, i;
            empty_selector(child_selector);
            if ( child_selector == '.search_state') {
                empty_selector('.search_city');
            }
            for ( i = 0; i < length; i++ ) {
                item = data[i];
                ul.append('<li class="" value="' + item.id + '">'
                    + item.title + '</li>');
            }
            select_option_action();
            if ( parent_id == 'country_id' ) {
                jQuery('.search_state li').click(function() {
                    get_subitems(jQuery(this).attr('value'), 'state_id', '.search_city');
                });
                if ( typeof default_child_id !== 'undefined' ) {
                    set_default('.search_state', default_child_id);
                }
            }
            if ( parent_id == 'state_id' && typeof default_child_id !== 'undefined' ) {
                set_default('.search_city', default_child_id);
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            console.log( "error", xhr, ajaxOptions, thrownError );
        }
    });
}
function empty_selector(selector) {
    var ul_parent = jQuery(selector);
    ul_parent.find('ul').empty();
    ul_parent.find('span').text(ul_parent.find('.hidden-title').text());
}
function select_option_action() {
    jQuery(".es_search_select ul li").click(function(){
        jQuery(this).siblings('li').removeClass('selected');
        jQuery(this).addClass('selected');
        var selVal = jQuery(this).attr('value');
        var selText = jQuery(this).text();
        jQuery(this).parents('.es_search_select').find('span').text(selText);
        jQuery(this).parents('.es_search_select').find('input').val(selVal);
        jQuery(this).parents('.es_search_select').removeClass('focus')
        return false;
    });
    jQuery(".es_search_select li.selected").each(function(index, element) {
        var selText = jQuery(this).text();
        jQuery(this).parents('.es_search_select').find('span').text(selText);
    });
}
function set_default(location, location_id) {
    var name = jQuery(location).find('li[value=' + location_id + ']').addClass('selected').text();
    jQuery(location).find('span').text(name);
}
function single_page_navigation() {
    var navPos = parseInt(jQuery('.es_prop_single_tabs').offset().top),
        navPosLeft = parseInt(jQuery('.es_prop_single_tabs').offset().left),
        navWidth = parseInt(jQuery('.es_prop_single_tabs').width());
    jQuery(window).scroll(function(e) {
        if(jQuery(this).scrollTop()>=navPos){
            jQuery('.es_prop_single_tabs').addClass('fixed');
            jQuery('.es_prop_single_tabs').css({'left':navPosLeft+'px','width':navWidth+'px'});
        } else {
            jQuery('.es_prop_single_tabs').removeClass('fixed');
            jQuery('.es_prop_single_tabs').css({'left':'0px','width':'auto'});
        }
    });
    jQuery('.es_prop_single_pics').magnificPopup({
        delegate: 'a',
        type: 'image',
        tLoading: 'Loading image #%curr%...',
        mainClass: 'mfp-img-mobile',
        gallery: {
            enabled: true,
            navigateByImgClick: true,
            preload: [0,5] // Will preload 0 - before current, and 1 after the current image
        },
        image: {
            //tError: '<a href='%url%'>The image #%curr%</a> could not be loaded.',
            titleSrc: function(item) {
                //return item.el.attr('title') + '<small>by Marsel Van Oosten</small>';
            }
        }
    });
    stLight.options({
        publisher: 'e8d11332-5e62-42d4-a5e2-94bd0f0e0018',
        doNotHash: false,
        doNotCopy: false,
        hashAddressBar: false
    });
}
function single_page_slider() {
    var pagerWidth = jQuery('#es_prop_single_pager_outer').width()/EstatikApp.prop_singleview_photo_thumb_width;
    jQuery('.es_prop_single_pics').bxSlider({
        slideWidth: jQuery('#es_prop_single_slider_in').width(),
        slideMargin: 0,
        controls: false,
        infiniteLoop: false,
        maxSlides: 1,
        pagerCustom: '.es_prop_single_pager'
    });
    jQuery('.es_prop_single_pager').bxSlider({
        slideWidth: EstatikApp.prop_singleview_photo_thumb_width,
        slideMargin: 10,
        pager: false,
        infiniteLoop: false,
        controls: true,
        nextText: '<i class="fa fa-angle-right"></i>',
        prevText: '<i class="fa fa-angle-left"></i>',
        minSlides: parseInt(pagerWidth),
        maxSlides: parseInt(pagerWidth),
    });
    jQuery('.es_prop_single_pager li a').each(function(index, element) {
        jQuery(this).attr('data-slide-index',index);
    });
    // jQuery('.es_prop_single_pager').bxSlider({
    //   slideWidth: EstatikApp.prop_singleview_photo_thumb_width,
    //   slideMargin: 10,
    //   pager: false,
    //   infiniteLoop: false,
    //   minSlides: parseInt(pagerWidth),
    //   maxSlides: parseInt(pagerWidth)
    // });
    // jQuery('.es_prop_single_pager li a').each(function(index, element) {
    // 	jQuery(this).attr('data-slide-index',index);
    // });
}
jQuery(document).ready(function() {
    var height = jQuery('.es_my_listing .es_2columns_column > li, .es_my_listing .es_3columns_column > li, .es_my_listing .es_table_column > li').height();
    jQuery('ul.es_table_column li, .es_2columns_column li, .es_3columns_column li').css('height', height - 20);
    jQuery('.es-table-inner [type=radio]').click(function() {
        if (jQuery(this).is(':checked')) {
            jQuery(this).closest('label').addClass('active')
        } else {
            jQuery(this).closest('label').removeClass('active')
        }
        jQuery('.es-table-inner [type=radio]').each(function() {
            if (jQuery(this).is(':checked')) {
                jQuery(this).closest('label').addClass('active')
            } else {
                jQuery(this).closest('label').removeClass('active')
            }
        });
    });
    jQuery('.es-table-inner [type=radio]').each(function() {
        if (jQuery(this).is(':checked')) {
            jQuery(this).closest('label').addClass('active')
        } else {
            jQuery(this).closest('label').removeClass('active')
        }
    });
    var country = jQuery('.search_country input[type=hidden]').val(),
        state = jQuery('.search_state input[type=hidden]').val(),
        city = jQuery('.search_city input[type=hidden]').val();
    if ( typeof country != 'undefined' && country.length > 0 ) {
        get_subitems(country, 'country_id', '.search_state', state);
    }
    if ( typeof state != 'undefined' && state.length > 0 ) {
        get_subitems(state, 'state_id', '.search_city', city);
    }
    jQuery('.search_country li').click(function() {
        get_subitems(jQuery(this).attr('value'), 'country_id', '.search_state');
    });
    jQuery('.search_state li').click(function() {
        get_subitems(jQuery(this).attr('value'), 'state_id', '.search_city');
    });
    jQuery("input[type='reset']").click(function(){
        window.location.search = '';
    });
    jQuery(".es_error").click(function(){
        jQuery(this).hide();
    });
    jQuery('.es_prop_single_tabs ul li a').click(function(){
        jQuery('.es_prop_single_tabs ul li a').removeClass('active');
        jQuery(this).addClass('active');
        var current_id = jQuery(this).attr('href');
        var current_id_pos = parseInt(jQuery(current_id).offset().top);
        //alert(jQuery(current_id_pos));
        jQuery('html,body').animate({ scrollTop: current_id_pos-58 });
        return false;
    });
    jQuery(".es_close_pop,.es_request_info_popup_overlay").click(function(){
        jQuery('#es_request_form_popup').fadeOut(500);
        return false;
    });
    jQuery("#es_request_form input[type='submit']").click(function(){
        var es_request_form_email = jQuery('#es_request_form input[name="your_email"]').val();
        var es_request_form_message = jQuery('#es_request_form textrea').val();
        var es_request_form_captcha = jQuery('#es_request_form input[name="captcha_code"]').val();
        var es_request_form_captcha_check = jQuery('#es_request_form input[name="captcha_check"]').val();
        if (es_request_form_captcha=='') {
            jQuery('#request_form_error').text(jQuery("#enterSecurityCode").val()).show();
            return false;
        }
        if (es_request_form_captcha!=es_request_form_captcha_check) {
            jQuery('#request_form_error').text(jQuery("#incorrectCodeEntered").val()).show();
            return false;
        }
        if (es_request_form_email=='' || es_request_form_message=='') {
            jQuery('#request_form_error').text(jQuery("#enterYourEmail").val()).show();
            return false;
        }
        if (!emailValidation(es_request_form_email)) {
            jQuery('#request_form_error').text(jQuery("#notValidYourEmail").val()).show();
            return false;
        } else {
            jQuery('#request_form_error').hide();
            return true;
        }
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
    jQuery('p').each(function() {
        var jQuerythis = jQuery(this);
        if(jQuerythis.html().replace(/\s|&nbsp;/g, '').length == 0)
            jQuerythis.remove();
    });
    jQuery("#es_content #es_map_pop_outer a#es_closePop,#es_content #es_overlay").click(function(){
        jQuery('#es_content #es_map_pop_outer').removeClass('esShow');
    });
    jQuery("#es_toTop a").click(function(){
        jQuery('html,body').animate({scrollTop:0},500);
        return false;
    });
    /*jQuery(".es_search_select input").blur(function(){
     var obj = jQuery(this);
     setTimeout(function(){
     obj.parents('.es_search_select').removeClass('focus')
     },100)
     });*/
    jQuery(".es_search_select span, .es_search_select small").click(function(){
        jQuery(this).parents('.es_search_select').addClass('focus')
        //jQuery(this).parents('.es_search_select').find('input').focus();
    });
    jQuery(".es_search_select").mouseleave(function(){
        jQuery(".es_search_select").removeClass('focus')
    });
    jQuery(".es_search_select ul li").click(function(){
        jQuery(this).siblings('li').removeClass('selected');
        jQuery(this).addClass('selected');
        var selVal = jQuery(this).attr('value');
        var selText = jQuery(this).text();
        jQuery(this).parents('.es_search_select').find('span').text(selText);
        jQuery(this).parents('.es_search_select').find('input').val(selVal);
        jQuery(this).parents('.es_search_select').removeClass('focus')
        return false;
    });
    jQuery(".es_search_select li.selected").each(function(index, element) {
        var selText = jQuery(this).text();
        jQuery(this).parents('.es_search_select').find('span').text(selText);
    });
    jQuery('.es_wrapper select').change(function(){
        var selText = jQuery(this).find('option:selected').text();
        jQuery(this).parent('.es_select').find('.es_select_arow').text(selText);
    });
    jQuery( '.es_address_auto' ).autocomplete({
        source: EstatikApp.availableTags
    });
    jQuery('#es_date_added').datepicker({
        showOn: 'button',
        buttonImage: EstatikApp.dir_url + 'front_templates/images/es_calender_icon.jpg',
        buttonImageOnly: true,
    });
});
function es_map_view_click(obj) {
    var mapLatLong = jQuery(obj).attr('href');
    if(mapLatLong!=""){
        var arr = mapLatLong.split(",");
        es_initialize(arr[0],arr[1]);
    }else{
        jQuery('#es_content #es_map').text('Not defined.');
    }
    jQuery('#es_content #es_map_pop_outer').addClass('esShow');
    return false;
}
function es_initialize(lat,long) {
    if ( document.getElementById('es_map') === null ) return;
    var myLatlng = new google.maps.LatLng(lat,long);
    var mapOptions = {
        zoom: 16,
        scrollwheel: false,
        center: myLatlng
    }
    var map = new google.maps.Map(document.getElementById('es_map'), mapOptions);
    var marker = new google.maps.Marker({
        position: myLatlng,
        map: map,
    });
}
jQuery(window).load(function(){
    jQuery('#es_content .es_my_listing ul li .es_my_list_pic').equalHeights('.es_my_list_pic');
    // jQuery('#es_content .es_my_listing ul li').equalHeights('#es_content .es_my_listing ul li');
    remove_listing_style();
    if ( jQuery('.es_single_in').length ) {
        single_page_slider();
        single_page_navigation();
    }
    jQuery('.es_tabs ul li a').each(function(index, element) {
        var href = jQuery(this).attr('href'),
            type = href.replace('#', '');
        if ( type == EstatikApp.prop_title ) {
            jQuery(this).trigger('click');
        }
    });
});
jQuery(window).resize(function(){
    jQuery('#es_content .es_my_listing ul li .es_my_list_pic').equalHeights('.es_my_list_pic');
    // jQuery('#es_content .es_my_listing ul li').equalHeights('#es_content .es_my_listing ul li');
    remove_listing_style();
});
function remove_listing_style(){
    var winWidth = jQuery(window).width();
    if(winWidth<1025) {
        // jQuery("#es_content").removeClass('es_2columns_column es_table_column es_list_column ');
        jQuery(".es_2columns_column").removeClass('es_2columns_column');
        jQuery(".es_table_column").removeClass('es_table_column');
        jQuery(".es_list_column").removeClass('es_list_column');
    }
}
jQuery.fn.equalHeights = function(group) {
    var currentTallest = 0;
    jQuery(this).each(function(){
        jQuery(this).each(function(i){
            if (jQuery(this).height() > currentTallest) { currentTallest = jQuery(this).height(); }
        });
    });
    jQuery(group).css({'height': currentTallest});
    return this;
};
////////////////////// MapView widget functions
//var map;
//var marker;
if (typeof esMapViewInit != 'function') {
    function esMapViewInit() {
        if ( document.querySelector('.widget .esMapViewOuter .esMapView') === null ) return;
        var myLatlng = new google.maps.LatLng(EstatikApp.widgetMapview.latitude,
                EstatikApp.widgetMapview.longitude),
            isDraggable = jQuery(document).width() > 768 ? true : false,
            zoom = jQuery('.esMapViewOuter .esMapView').attr('zoom'),
            mapOptions = {
                zoom: parseInt(zoom),
                draggable: isDraggable,
                scrollwheel: false,
                center: myLatlng,
            },
            map = new google.maps.Map(document.querySelector('.widget .esMapViewOuter .esMapView'), mapOptions);
        // console.log(document.querySelector('.widget .esMapViewOuter .esMapView'));
        // console.log(map);
        setMarkers(map, EstatikApp.widgetMapview.mapinfos);
    }
    //var markersArray = new Array();
    function setMarkers(map, locations) {
        var mapinfo, myLatLng, mapIcon, closeIcon, marker, i;
        for ( i = 0; i < locations.length; i++ ) {
            mapinfo = locations[i];
            myLatLng = new google.maps.LatLng(mapinfo[1], mapinfo[2]);
            mapIcon   = '';
            closeIcon = '';
            marker = '';
            if(mapinfo[4].indexOf('rent')!=-1){
                mapIcon = 'mapIconRed';
            }else{
                mapIcon = 'mapIconBlue';
            }
            mapIcon = EstatikApp.dir_url + 'front_templates/images/' + mapIcon
                + jQuery('.esMapViewOuter .esMapView').attr('icon-style');
            mapIcon_hover = mapIcon + '_hover.png';
            mapIcon += '.png';
            marker = new google.maps.Marker({
                position: myLatLng,
                map: map,
                icon: mapIcon,
                title: mapinfo[0],
            });
            google.maps.event.addListener(marker, 'mouseover', function() {
                marker.setIcon(mapIcon_hover);
            });
            google.maps.event.addListener(marker, 'mouseout', function() {
                marker.setIcon(mapIcon);
            });
            var infobox = new InfoBox();
            google.maps.event.addListener(marker, 'click', (function(marker, i) {
                return function() {
                    map.setCenter(marker.getPosition());
                    infobox.close();
                    closeIcon = 'close';
                    // if(locations[i][4].indexOf('rent')!=-1){
                    // 	closeIcon = 'mapRedClose';
                    // }else{
                    // 	closeIcon = 'mapBlueClose';
                    // }
                    infobox = new InfoBox({
                        content: '',
                        disableAutoPan: false,
                        //maxWidth: 400,
                        //pixelOffset: new google.maps.Size(-140, 0),
                        zIndex: null,
                        boxStyle: {
                            background: 'url(\'http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobox/examples/tipbox.gif\') no-repeat',
                        },
                        closeBoxURL: EstatikApp.dir_url + 'front_templates/images/' + closeIcon + '.png',
                        infoBoxClearance: new google.maps.Size(1, 1)
                    });
                    infobox.setContent(locations[i][3]);
                    //infobox.setContent(".stripcslashes('locations[i][3]').");
                    infobox.open(map, marker);
                }
            })(marker, i));
        }
    }
}
google.maps.event.addDomListener(window, 'load', esMapViewInit);
