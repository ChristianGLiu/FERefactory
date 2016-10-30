<?php
 
wp_reset_query();
wp_reset_postdata();
global $post;
$current_id = get_post( $post );
if(!empty($current_id)){
	$current_id = $current_id->ID;
}
$queried_object = get_queried_object();
 
$current_page = "";
$single_page  = "";
$author_page  = "";
$archive_page = "";
$search_page  = "";
$category_page= "";
$current_page = ""; 
 
if(is_single() && $show_on_pages=="show_on_checked_pages"){
 
	$single_page = in_array('single_page',$choosed_pages);
	
}else if( isset($queried_object->user_login) && $queried_object->user_login!="" && $show_on_pages=="show_on_checked_pages") {
	
	$author_page = in_array('author_page',$choosed_pages);
 
}else if(is_tax() && $show_on_pages=="show_on_checked_pages") {
	
	$archive_page = in_array('archive_page',$choosed_pages);
	
}else if(is_category() && $show_on_pages=="show_on_checked_pages") {
	
	$category_page = in_array('category_page',$choosed_pages);
	
}else if(is_search() && $show_on_pages=="show_on_checked_pages") {
	
	$search_page = in_array('search_page',$choosed_pages);
	
} else if($show_on_pages=="show_on_checked_pages") {
	
	$current_page = in_array($current_id,$choosed_pages); 
	
}
if(is_single() && $show_on_pages=="hide_on_checked_pages"){
	
	$single_page = in_array('single_page',$choosed_pages);
	$author_page=true;
	$archive_page=true;
	$search_page=true;
	$category_page=true;
	$current_page = true;
	
}else if( isset($queried_object->user_login) && $queried_object->user_login!="" && $show_on_pages=="hide_on_checked_pages") {
	
	$author_page = in_array('author_page',$choosed_pages);
	$single_page=true;
	$archive_page=true;
	$search_page=true;
	$category_page=true;
	$current_page = true;
	
}else if(is_tax() && $show_on_pages=="hide_on_checked_pages") {
	
	$archive_page = in_array('archive_page',$choosed_pages);
	$$author_page=true;
	$single_page=true;
	$search_page=true;
	$category_page=true;
	$current_page = true;
	
}else if(is_category() && $show_on_pages=="hide_on_checked_pages") {
	
	$category_page = in_array('category_page',$choosed_pages);
	$$author_page=true;
	$single_page=true;
	$archive_page=true;
	$search_page=true;
	$current_page = true;
	
}else if(is_search() && $show_on_pages=="hide_on_checked_pages") {
	
	$search_page = in_array('search_page',$choosed_pages);
	$author_page=true;
	$single_page=true;
	$archive_page=true;
	$category_page=true;
	$current_page = true;
	
} else if($show_on_pages=="hide_on_checked_pages") {
	
	$current_page = in_array($current_id,$choosed_pages); 
	
	$author_page=true;
	$single_page=true;
	$archive_page=true;
	$search_page=true;
	$category_page=true;
	
}
  
if(
$show_on_pages=="all_pages"
|| 
$show_on_pages=="show_on_checked_pages" && ($current_page==true || $author_page==true || $single_page==true || $archive_page==true || $search_page==true || $category_page==true )
||
$show_on_pages=="hide_on_checked_pages" && ( $current_page==false || $author_page==false || $single_page==false || $archive_page==false || $search_page==false || $category_page==false )
&&
in_array($widget_id,$choosed_pages)
){
?>
<div id="es_slideShow" class="cleafix">
  
    <script type="text/javascript">
    jQuery(window).load(function(e) { 
		var slide_width = "",
			slides_number = jQuery(window).width() > 900 ? <?php echo $number_of_images?> : 1;
		<?php if ( !empty($images_width) ) { ?>
			slide_width = <?php echo $images_width ?>;
		<?php } else { ?>
			slide_width = jQuery('#<?php echo $widget_id?>').closest('.es_slideshow_outer').width();
			<?php if ( $slide_effect == 'horizontal' ) { ?>
				slide_width /= slides_number;
			<?php } ?>
		<?php } ?>
		console.log(jQuery('#<?php echo $widget_id?>'));
		console.log(slide_width, slides_number);
        jQuery('#<?php echo $widget_id?>').bxSlider({
          pager: false,
		  slideWidth: slide_width,
		  mode: '<?php echo $slide_effect?>',
		  moveSlides: 1,
		  auto: true,
		  autoHover: true,
          slideMargin: 20,
          infiniteLoop: true,
		  <?php if($show_arrows!=1) { ?>
		   controls: false,
		   auto: true,   
		   mode: 'fade',
		  <?php } ?>
		  minSlides: slides_number,
          maxSlides: slides_number
        });
    });
    </script>
    
    <style type="text/css">
    	#es_content .es_slide_list{ width:<?php echo $images_width?>px; height:<?php echo $images_height?>px; }
    </style>
    
    <div class="es_slideshow_outer" id="layout_<?php echo $layout?>">
    	 
        <?php 
		if($title!=""){ ?>
        <h3><?php echo $title?></h3> 
        <?php } ?>
        
        <?php
      
            global $wpdb;
             
			$es_settings = es_front_settings();
			$currency_sign_ex = explode(",", $es_settings->default_currency);
			if(count($currency_sign_ex)==1){
				$currency_sign = $currency_sign_ex[0];
			}else {
				$currency_sign = $currency_sign_ex[1];	
			}
			
             
			$where_array = array();
			$where_ids = array();
 			
			if($only_featured == 1){
                $only_featured   =	'AND prop_featured 	=  "'.$only_featured.'"';
            }else{
				$only_featured = "";	
			}
			
			if($category != ''){
                $where_array[]  =	'prop_category 	=  "'.$category.'"';
            }
            if($type != ''){
                $where_array[]  =	'prop_type 	=  "'.$type.'"';
            }
			if(!empty($listing_ids)){
				$listing_ids = explode(",",$listing_ids);
				foreach ($listing_ids as $listing_id){
					$where_ids[]  =	'prop_id 	=  "'.$listing_id.'"';
				}
			}
  
			if(!empty($where_array) && !empty($where_ids)){
				
				$whereid =  implode(" OR ",$where_ids);
				$where =  implode(" AND ",$where_array);
				
				$sql = 'SELECT * FROM '.$wpdb->prefix.'estatik_properties WHERE '.$where.' OR '.$whereid.' '.$only_featured.' AND prop_pub_unpub = 1 order by prop_id desc';
				
			} else if(!empty($where_array)){
				
				$where =  implode(" AND ",$where_array);
				
				$sql = 'SELECT * FROM '.$wpdb->prefix.'estatik_properties WHERE '.$where.' '.$only_featured.' AND prop_pub_unpub = 1 order by prop_id desc';
				
			} else {
				
				$sql = 'SELECT * FROM '.$wpdb->prefix.'estatik_properties WHERE prop_pub_unpub = 1 '.$only_featured.' order by prop_id desc';	
			
			}
 
			
			$es_dimension = $wpdb->get_row( 'SELECT dimension_title FROM '.$wpdb->prefix.'estatik_manager_dimension WHERE dimension_status=1' );
			
            $es_my_listing = $wpdb->get_results($sql); 
  			
            if(!empty($es_my_listing)) { ?>
        
                <ul id="<?php echo $widget_id?>">
                    
                    <?php
                        foreach($es_my_listing as $list) {
  
                    ?>
                
                        <li>
                            <div class="es_slide_list">
                                <?php 
                                $image_sql = "SELECT prop_meta_value FROM ".$wpdb->prefix."estatik_properties_meta WHERE prop_id = ".$list->prop_id." AND prop_meta_key = 'images'";
    
								$uploaded_images = $wpdb->get_row($image_sql);
								if(!empty($uploaded_images)){
									$upload_image_data = unserialize($uploaded_images->prop_meta_value);		
								}
                                if(!empty($upload_image_data)) {
	                                $upload_dir = wp_upload_dir(); 
	                                
	                                $list_image_name = explode("/",$upload_image_data[0]);
									$list_image_name = end($list_image_name);
	                                $list_image_path = str_replace($list_image_name,"",$upload_image_data[0]);
	                                $image_url = $list_image_path.'list_'.$list_image_name;
	                                
	                                $image_url = $upload_dir['baseurl'] . $image_url;
	                                $img = "<img style='width: {$images_width}px; height: {$images_height}px;' src='$image_url' />";
								} else{
	                                $image_url = plugin_dir_url(__FILE__) . '../images/placeholder.png';
	                                $img = "<img src='$image_url' />";
                                    // $img = '<p>'.__("No Image", 'es-plugin').'</p>';
                                } ?>
                                <a href="<?php echo get_permalink($list->prop_id);?>"
                                   style="background-image: url(<?php echo $image_url?>)">
                                	<?php echo $img; ?>
                                </a>
                                <div class="es_slide_list_upper">
                                    <div class="es_slide_list_price clearfix">
                                        <?php
                                        $prop_cat = $wpdb->get_row( 'SELECT cat_title FROM '.$wpdb->prefix.'estatik_manager_categories WHERE cat_id = "'.$list->prop_category.'"');
                                        ?>
                                        <strong>
                                            <?php if(!empty($prop_cat) && $prop_cat->cat_title!=""){ ?>
                                                <?php echo $prop_cat->cat_title?>
                                            <?php } ?>
                                        </strong>
                                        <?php if($es_settings->price==1) {
                                           $price_format = explode("|",$es_settings->price_format);
                                        ?>
                                            <strong><?php if($es_settings->currency_sign_place=='before'){ echo $currency_sign ; }?><?php echo number_format($list->prop_price,$price_format[0],$price_format[1],$price_format[2]);?><?php if($es_settings->currency_sign_place=='after'){ echo $currency_sign; }?></strong>
                                        <?php } ?>
                                    </div>
                                    <div class="es_slide_list_specs clearfix">
                                        <span class="es_dimen"><small><?php if($list->prop_area!=0) { ?><?php echo $list->prop_area?><?php } ?> <?php if(!empty($es_dimension)) { echo $es_dimension->dimension_title; } ?></small></span>
                                        <span class="es_bd"><small><?php if($list->prop_bedrooms!=0) { ?><?php echo $list->prop_bedrooms?><?php } ?> <?php _e("beds", 'es-plugin') ?></small></span>
                                        <span class="es_bth"><small><?php if($list->prop_bathrooms!=0) { ?><?php echo $list->prop_bathrooms?><?php } ?> <?php _e("bath", 'es-plugin') ?></small></span>
                                    </div>
                                </div>
                            </div>
                        </li>
                    
                    <?php  } ?>
                    
                </ul> 
        
        	<?php } else { ?>
            	<p><?php _e("No Slide", 'es-plugin') ?></p>
            <?php } ?>
        
    </div>
</div>
<?php } ?>