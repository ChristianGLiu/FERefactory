<?php 
global $wpdb; 
$es_settings = es_front_settings(); ?>
<div id="es_content" class="clearfix"> 
 
	<div class="es_agents_content">
        
        <div class="es_agents_content_listing">
            <ul>
                <?php
                
                $es_settings = es_front_settings();
                $es_per_page = 	$es_settings->no_of_listing;
                
                $paged = (isset($_GET['page_no']))?$_GET['page_no']:0;
                
                $agent_sql = "SELECT * FROM ".$wpdb->prefix."estatik_agents WHERE agent_status = 1 order by agent_id desc limit ".$paged.",".$es_per_page."";
                $count = 'SELECT count(*) as total_record FROM '.$wpdb->prefix.'estatik_agents WHERE agent_status = 1 ';
                
                $es_agent_result = $wpdb->get_results($agent_sql);
                $upload_dir = wp_upload_dir(); 
                
                $es_count_listing = $wpdb->get_results($count);
                $total_record 	  = $es_count_listing[0];
                
         
                require_once(PATH_DIR . 'front_templates/includes/pagination.php');
                $config['base_url']  = '?';
                $config['total_rows'] = $total_record->total_record;
                $config['per_page']  = $es_per_page;
                $config['uri_segment'] = 3;
                $config['num_links']  = 1; 
                $pagination = new Pagination();
                $pagination->initialize($config); 
                $es_pagination = $pagination->create_links();
                if(!empty($es_agent_result)){
                foreach ($es_agent_result as $es_agent) { 
                //print_r($es_agent);	
                ?>	
        
                    <li class="clearfix">
                        <div class="es_agent_pic">
                            <?php
								$image_name = explode("/",$es_agent->agent_pic);
								$image_name = end($image_name);
								$image_path = str_replace($image_name,"",$es_agent->agent_pic);
								$latest_image = $image_path.'agent_'.$image_name;
							?>
                            <img src="<?php echo $upload_dir['baseurl']?><?php echo $latest_image?>" alt="" />
                        </div>
                        <div class="es_agents_info">
                            <div class="es_agent_info_head clearfix">
                                <div class="es_agent_info_head_left">
                                    <h5><?php echo $es_agent->agent_name?></h5>
                                    <?php if($es_agent->agent_tel!=0){ ?>
                                    	<h4><?php echo $es_agent->agent_tel?></h4>
                                    <?php } ?>
                                    <p><a href="mailto:<?php echo $es_agent->agent_email?>"><?php echo $es_agent->agent_email?></a></p>
                                    <p><a href="<?php echo $es_agent->agent_web?>" target="_blank"><?php echo $es_agent->agent_web?></a></p>
                                </div>
                                <div class="es_agent_info_head_right">
                                    <div class="es_agent_info_quantity">
                                        <?php $agent_prop_quantity = $wpdb->get_var( "SELECT COUNT(agent_id) as total FROM ".$wpdb->prefix."estatik_properties WHERE agent_id = ".$es_agent->agent_id." AND prop_pub_unpub = 1");?>
<!--                                        <p>--><?php //_e("Properties Q-ty", 'es-plugin'); ?><!--: <a href="--><?php //echo get_author_posts_url($es_agent->agent_id);?><!--">--><?php //echo $agent_prop_quantity?><!--</a></p>-->
                                        <p><?php _e("Properties sold", 'es-plugin'); ?>: <?php echo $es_agent->agent_sold_prop?></p>
                                    </div>
                                    <div class="es_agent_rating es_rating_<?php echo $es_agent->agent_rating?>">
                                        <label><?php _e("Rating", 'es-plugin'); ?>:</label>
                                        <a></a>
                                        <a></a>
                                        <a></a>
                                        <a></a>
                                        <a></a>
                                    </div>
                                </div>
                            </div>
                            <div class="es_agent_info_desc">
                                <p><?php _e("Company", 'es-plugin'); ?>: <?php echo $es_agent->agent_company?></p>
                                <p><?php echo $es_agent->agent_about?></p>
                            </div>
                        </div>
                    </li>
                
                <?php } ?>
                <?php } else { ?>
                	<p><?php _e("No active Agent.", 'es-plugin'); ?></p>
                <?php } ?>
            </ul>
        </div>
        <?php echo $es_pagination; ?>
        
	</div>
	
    <?php if($es_settings->powered_by_link==1) { ?>
        <div class="es_powered_by
">
            <p><?php _e("Powered by", 'es-plugin'); ?> <a href="http://www.estatik.net" target="_blank">Estatik</a></p>
        </div>    
    <?php } ?>
    
</div>