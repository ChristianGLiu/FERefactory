<?php 
global $wpdb; 
$es_settings = es_front_settings(); 
$es_per_page =  $es_settings->no_of_listing;
$theme_url = get_template_directory_uri();
$paged = isset($_GET['page_no']) ? $_GET['page_no'] : 0;
$agent_sql = "SELECT * FROM {$wpdb->prefix}estatik_agents WHERE agent_status = 1 order by agent_id desc limit $paged,$es_per_page";
$count = "SELECT count(*) as total_record FROM {$wpdb->prefix}estatik_agents WHERE agent_status = 1";
$es_agent_result = $wpdb->get_results($agent_sql);
$upload_dir = wp_upload_dir(); 
$total_record = $wpdb->get_row($count);
require_once(PATH_DIR . 'front_templates/includes/pagination.php');
$config['base_url']  = '?';
$config['total_rows'] = $total_record->total_record;
$config['per_page']  = $es_per_page;
$config['uri_segment'] = 3;
$config['num_links']  = 1; 
$pagination = new Pagination();
$pagination->initialize($config); 
$es_pagination = $pagination->create_links();
?>
<div id="es_content" class="clearfix"> 
 
	<div class="es_agents_content">
        
        <div class="es_agents_content_listing">
            <ul>
                <?php
                
                if(!empty($es_agent_result)){
                    foreach ($es_agent_result as $es_agent) { 
                ?>	
        
                    <li class="clearfix">
                        <?php
							$image_name = explode("/",$es_agent->agent_pic);
							$image_name = end($image_name);
							$image_path = str_replace($image_name,"",$es_agent->agent_pic);
							$latest_image = $image_path.'agent_'.$image_name;
						?>
                        <div class="es_agent_pic" 
                             style="background-image: url(<?php echo $upload_dir['baseurl'] . $latest_image?>" ></div>
                        <div class="es_agent_info_head clearfix">
                                <div class="es_agent_info es_agent_name"><?php echo $es_agent->agent_name?></div>
                                <div class="es_agent_info es_agent_rating 
                                            es_rating_<?php echo $es_agent->agent_rating?>">
                                    <a></a>
                                    <a></a>
                                    <a></a>
                                    <a></a>
                                    <a></a>
                                </div>
                                <?php if($es_agent->agent_tel!=0){ ?>
                                    <div class="es_agent_info es_agent_tel">
                                        <img src="<?php echo $theme_url?>/images/phone.png" />
                                        <?php echo $es_agent->agent_tel?>
                                    </div>
                                <?php } ?>
                                <a class="es_agent_info es_agent_email" 
                                   href="mailto:<?php echo $es_agent->agent_email?>">
                                    <img src="<?php echo $theme_url?>/images/email.png" />
                                    <?php echo $es_agent->agent_email?>
                                </a>
                                <a class="es_agent_info es_agent_web" 
                                   href="<?php echo $es_agent->agent_web?>" target="_blank">
                                    <img src="<?php echo $theme_url?>/images/link.png" />
                                    <?php echo $es_agent->agent_web?>
                                </a>
                        </div>
                        <div class="es_agent_info_desc">
                            <div class="es_agent_info es_agent_company">
                                <?php _e("Company", 'es-plugin'); ?>: <?php echo $es_agent->agent_company?>
                            </div>
                            <div class="es_agent_info es_agent_about">
                                <?php echo $es_agent->agent_about?>
                            </div>
                            <div class="es_agent_info es_agent_info_quantity">
                                <?php $agent_prop_quantity = $wpdb->get_var( 
                                    "SELECT COUNT(agent_id) as total FROM {$wpdb->prefix}estatik_properties 
                                        WHERE agent_id = {$es_agent->agent_id} AND prop_pub_unpub = 1"
                                );?>
                                <?php _e("Properties sold", 'es-plugin'); ?>: 
                                <strong><?php echo $es_agent->agent_sold_prop?></strong>
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