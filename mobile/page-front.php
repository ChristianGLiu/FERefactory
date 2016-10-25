<?php
et_get_mobile_header();

get_template_part( 'mobile/template', 'header' );

global $post,$user_ID,$wp_rewrite,$wp_query,$current_user,$max_file_size;

$data = et_get_unread_follow();
?>
		<div data-role="content" class="fe-content">
			<div class="fe-nav">
				<a href="#fe_category" class="fe-nav-btn fe-btn-cats"><span class="fe-sprite"></span></a>

				<?php if(!$user_ID){?>
				 <?php echo "<div class='mobile-social-wrapper' style='margin-top:10px;margin-left:-20px;'><span class='mobile-social-bar-label' style='margin-top:10px;margin-left:-20px;'>一键登录：</span><span class='mobile-social-bar-content'>".open_social_login_html()."</span></div>";?>

				<a href="<?php echo et_get_page_link('login') ?>" class="fe-nav-btn fe-btn-profile"><span class="fe-sprite"></span></a>
				<?php } else {?>
				 <?php
				 $current_user = wp_get_current_user();

				 echo "<span class='mobile-social-bar-label'>欢迎回来:".$current_user->display_name."</span>";?>

				<a href="<?php echo get_author_posts_url($user_ID) ?>" class="fe-head-avatar toggle-menu"><?php echo  et_get_avatar($user_ID);?></a>
				<?php } ?>
			</div>
			<?php get_template_part( 'mobile/template', 'profile-menu' ) ?>

			<div class="fe-tab">
				<ul class="fe-tab-items">
					<li class="fe-tab-item fe-tab-item-5 <?php if (!is_tax( 'category' ) || current_user_can( 'manage_threads' )) echo 'fe-current current'; ?>">
						<a href="<?php echo home_url() ?>">
							<span class="fe-tab-name"><?php _e('ALL POSTS',ET_DOMAIN) ?>
							<?php
								if(!empty($data) && count($data['unread']['data']) > 0){
							?>
								<span class="count <?php if ( et_get_option("pending_thread") && (et_get_counter('pending') > 0) &&(current_user_can("manage_threads") || current_user_can( 'trash_threads' )) || is_tax( 'category' )) { echo 'mana'; }?>"><?php echo count($data['unread']['data']) ?></span>
							<?php } ?>
							</span>
						</a>
					</li>
					<li class="fe-tab-item fe-tab-item-6">
						<?php if($user_ID){?>
						<a href="<?php echo et_get_page_link("following") ?>">
						<?php } else { ?>
						<a href="<?php echo et_get_page_link("login") ?>">
						<?php } ?>
							<span class="fe-tab-name"><?php _e('FOLLOWING',ET_DOMAIN) ?>
							<?php if($user_ID && count($data['follow']) > 0){ ?>
								<span class="count <?php if ( et_get_option("pending_thread") && (et_get_counter('pending') > 0) &&(current_user_can("manage_threads") || current_user_can( 'trash_threads' )) || is_tax( 'category' )) { echo 'mana'; }?>"><?php echo count($data['follow']) ;?></span>
							<?php } ?>
							</span>
						</a>
					</li>
					<li class="fe-tab-item fe-tab-item-7">
                    						<a href="/房屋租售/">
                    						加东房产
                    						</a>
                    					</li>
                    					<li class="fe-tab-item fe-tab-item-8">
                                                            						<a href="/二手交易/">
                                                            						二手交易
                                                            						</a>
                                                            					</li>
                                                            					<li class="fe-tab-item fe-tab-item-9">
                                                                                                    						<a href="/商家黄页/">
                                                                                                    						商家黄页
                                                                                                    						</a>
                                                                                                    					</li>
					<?php if ( et_get_option("pending_thread") && (et_get_counter('pending') > 0) &&(current_user_can("manage_threads") || current_user_can( 'trash_threads' )) ) {?>
					<li class="fe-tab-item fe-tab-item-3 fe-tab-3">
						<a href="<?php echo et_get_page_link("pending");?>">
							<span class="fe-tab-name"><?php _e('PENDING',ET_DOMAIN) ?>
								<!-- <span class="count">3</span> -->
							</span>
						</a>
					</li>
					<?php } else if ( is_tax( 'category' ) ){ ?>
						<li class="fe-tab-item fe-tab-item-3 fe-tab-3 current fe-current">
							<a href="#">
								<span class="fe-tab-name"><?php single_term_title( ) ?>
									<!-- <span class="count">3</span> -->
								</span>
							</a>
						</li>
					<?php } ?>
				</ul>
			</div>
			<!--div class="arrow bounce">
              <span>
            猛击这里发表自己的贴子吧
              </span>
            </div-->
			<div class="fe-new-topic fe-container">
				<div class="fe-topic-form">
					<div class="fe-topic-input">
						<div class="fe-topic-dropbox">
							<select name="category" id="category">
								<option value=""><?php _e('Please select',ET_DOMAIN) ?></option>
								<?php
									$categories = FE_ThreadCategory::get_categories();
									et_the_cat_select($categories);
								?>
							</select>
						</div>
						<?php if($user_ID){ ?>
						<input type="text" maxlength="90" name="thread_title" id="thread_title" placeholder="<?php _e('Touch here to start a new topic',ET_DOMAIN) ?>">
						<?php }else{ ?>
						<a href="<?php echo et_get_page_link('login');?>" rel="external" target="_self" class="login_before_create_thread" ><?php _e('Please login to start a new topic',ET_DOMAIN) ?> </a>
						<?php } ?>
					</div>

					<div class="fe-topic-content">
						<?php if(get_option('upload_images')){ ?>
						<div class="insert-image-wrap">
							<div class="form-post" id="images_upload_container">
								<a href="javascript:void(0)" id="images_upload_browse_button"><img src="<?php echo get_template_directory_uri();?>/mobile/img/ico-pic.png" /><?php _e("Insert Image", ET_DOMAIN); ?></a>
								<span class="et_ajaxnonce" id="<?php echo wp_create_nonce( 'et_upload_images' ); ?>"></span>
								<span id="images_upload_text"><?php printf(__("Size must be less than < %sMB.", ET_DOMAIN),$max_file_size) ; ?></span>
							</div>
						</div>
						<?php } ?>
						<div class="textarea">
							<textarea id="thread_content" class=""></textarea>
							<?php // wp_editor( '', 'thread_content' , editor_settings()) ?>
						</div>
						<?php do_action( 'fe_custom_fields_form' );?>
						<div class="fe-submit">
							<a href="#" class="fe-btn-primary" id="create_thread" data-role="button"><?php _e('Create',ET_DOMAIN) ?></a>
							<a href="#" class="fe-btn-cancel fe-icon-b fe-icon-b-cancel cancel-modal ui-link"><?php _e('Cancel',ET_DOMAIN) ?></a>
						</div>
					</div>
				</div>
			</div>
			<div class="fe-posts" id="posts_container">
				<!-- Sticky threads -->
				<?php
				$sticky_threads = et_get_sticky_threads();
				if ( !empty( $sticky_threads[0] ) ){
					get_template_part( 'mobile/template/thread', 'sticky' );
				}
				?>

				<!-- Loop Threads -->

				<?php
                $page 			= get_query_var('page') ? get_query_var('page') : 1;
                $sticky_threads = et_get_sticky_threads();


                $thread_query_1 = FE_Threads::get_threads(array(
                'post_type' 	=> 'post',
                'cat' => 56,
                'posts_per_page' => 2,
                'orderby' => 'date',
                'order'   => 'DESC',
                ));

                if (  $thread_query_1->have_posts() ){ ?>
                	<?php
                	if ( !empty( $sticky_threads[0] ) ){
                		// load sticky thread
                		get_template_part( 'template/sticky', 'thread' );
                	}

                	while ($thread_query_1->have_posts()){ $thread_query_1->the_post();
                    							get_template_part( 'mobile/template/thread', 'loop' );
                    						}
                	?>
                	<?php
                	wp_reset_query();
                }
                $thread_query_2 = FE_Threads::get_threads(array(
                'post_type' 	=> 'post',
                'cat' => '117',
                'posts_per_page' => 2,
                'orderby' => 'date',
                'order'   => 'DESC',
                ));

                if (  $thread_query_2->have_posts() ){ ?>
                	<?php
                	if ( !empty( $sticky_threads[0] ) ){
                		// load sticky thread
                		get_template_part( 'template/sticky', 'thread' );
                	}

                	while ($thread_query_2->have_posts()){ $thread_query_2->the_post();
                    							get_template_part( 'mobile/template/thread', 'loop' );
                    						}
                	?>
                	<?php
                	wp_reset_query();
                }

                $thread_query_3 = FE_Threads::get_threads(array(
                'post_type' 	=> 'post',
                'cat' => '14',
                'posts_per_page' => 2,
                'orderby' => 'date',
                'order'   => 'DESC',
                ));
                if (  $thread_query_3->have_posts() ){ ?>
                	<?php
                	if ( !empty( $sticky_threads[0] ) ){
                		// load sticky thread
                		get_template_part( 'template/sticky', 'thread' );
                	}

                	while ($thread_query_3->have_posts()){ $thread_query_3->the_post();
                                        							get_template_part( 'mobile/template/thread', 'loop' );
                                        						}
                	?>
                	<?php
                	wp_reset_query();
                }

                 $thread_query_8 = FE_Threads::get_threads(array(
                                'post_type' 	=> 'properties',
                                'posts_per_page' => 2,
                                'orderby' => 'date',
                                'order'   => 'DESC',
                                ));
                                if (  $thread_query_8->have_posts() ){ ?>
                                	<?php
                                	if ( !empty( $sticky_threads[0] ) ){
                                		// load sticky thread
                                		get_template_part( 'template/sticky', 'thread' );
                                	}

                                	while ($thread_query_8->have_posts()){ $thread_query_8->the_post();
                                                        							get_template_part( 'mobile/template/thread', 'loop' );
                                                        						}
                                	?>
                                	<?php
                                	wp_reset_query();
                                }

                $thread_query_4 = FE_Threads::get_threads(array(
                'post_type' 	=> 'post',
                'cat' => '115',
                'posts_per_page' => 2,
                'orderby' => 'date',
                'order'   => 'DESC',
                ));

                if (  $thread_query_4->have_posts() ){ ?>
                	<?php
                	if ( !empty( $sticky_threads[0] ) ){
                		// load sticky thread
                		get_template_part( 'template/sticky', 'thread' );
                	}

                	while ($thread_query_4->have_posts()){ $thread_query_4->the_post();
                                        							get_template_part( 'mobile/template/thread', 'loop' );
                                        						}
                	?>
                	<?php
                	wp_reset_query();
                }
                $thread_query_5 = FE_Threads::get_threads(array(
                'post_type' 	=> 'post',
                'cat' => '60',
                'posts_per_page' => 2,
                'orderby' => 'date',
                'order'   => 'DESC',
                ));
                if (  $thread_query_5->have_posts() ){ ?>
                	<?php
                	if ( !empty( $sticky_threads[0] ) ){
                		// load sticky thread
                		get_template_part( 'template/sticky', 'thread' );
                	}

                	while ($thread_query_5->have_posts()){ $thread_query_5->the_post();
                                        							get_template_part( 'mobile/template/thread', 'loop' );
                                        						}
                	?>
                	<?php
                	}

                	 $thread_query_7 = FE_Threads::get_threads(array(
                                    'post_type' 	=> 'post',
                                    'cat' => '119',
                                    'posts_per_page' => 2,
                                    'orderby' => 'date',
                                    'order'   => 'DESC',
                                    ));

                                    if (  $thread_query_7->have_posts() ){ ?>
                                    	<?php
                                    	if ( !empty( $sticky_threads[0] ) ){
                                    		// load sticky thread
                                    		get_template_part( 'template/sticky', 'thread' );
                                    	}

                                    	while ($thread_query_7->have_posts()){ $thread_query_7->the_post();
                                                            							get_template_part( 'mobile/template/thread', 'loop' );
                                                            						}
                                    	?>
                                    	<?php
                                    	wp_reset_query();
                                    }

                	$thread_query = FE_Threads::get_threads(array(
                	'post_type' 	=> 'post',
                	'paged' 		=> $page,
                	'post__not_in' 	=> $sticky_threads[0],
                	'category__not_in' => array(60,115,14,117,56,119,129)
                	));


                if (  $thread_query->have_posts() ){ ?>
                	<?php
                	if ( !empty( $sticky_threads[0] ) ){
                		// load sticky thread
                		get_template_part( 'template/sticky', 'thread' );
                	}
                	while ($thread_query->have_posts()){ $thread_query->the_post();
                                        							get_template_part( 'mobile/template/thread', 'loop' );
                                        						}
                                        						?>

                	<?php
                }
                ?>


				<!-- Loop Thread -->
			</div>
			<!-- button load more -->
			<?php
				wp_reset_query();
				if($page < $thread_query->max_num_pages) {
			?>
			<a href="#" id="more_thread" class="fe-btn-primary" data-term="<?php echo get_query_var('term');?>" data-status="index" data-page="<?php echo $page ?>" data-theme="d" data-role="button"><?php _e('Load more threads',ET_DOMAIN) ?></a>
			<?php } ?>
			<!-- button load more -->
		</div>
<?php
// footer part
get_template_part( 'mobile/template', 'footer' );

et_get_mobile_footer();
?>
