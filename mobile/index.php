<?php
et_get_mobile_header();

get_template_part( 'mobile/template', 'header' );

global $post,$user_ID,$wp_rewrite,$wp_query,$current_user;

$data = et_get_unread_follow();
?>


			<div class="fe-tab bottom-fe-tab">
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


                        						<a href="/关于我们/">
                                                                    						关于我们
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
		<div data-role="content" class="fe-content">
			<div class="fe-nav">
				<a href="#fe_category" class="fe-nav-btn fe-btn-cats"><span class="fe-sprite"></span></a>

				<?php if(!$user_ID){?>
				 <?php echo "<div class='mobile-social-wrapper' style='margin-top:10px;margin-left:-20px;'><span class='mobile-social-bar-content'>".open_social_login_html()."</span></div>";?>

				<?php } else {?>
				 <?php
				 $current_user = wp_get_current_user();

				 echo "<span class='mobile-social-bar-label'>欢迎回来:".$current_user->display_name."</span>";?>
				<a href="<?php echo get_author_posts_url($user_ID) ?>" class="fe-head-avatar toggle-menu"><?php echo  et_get_avatar($user_ID);?></a>
				<?php } ?>
			</div>
			<?php get_template_part( 'mobile/template', 'profile-menu' ) ?>
			<div class="fe-breadcrumbs">

			</div>

			<div class="fe-posts" id="posts_container">
				<!-- Loop Thread -->
				<?php
					$page = get_query_var('paged') ? get_query_var('paged') : 1;
					if ( have_posts() ){
						while ( have_posts()){ the_post();
							//load_template( apply_filters( 'et_mobile_template_post', dirname(__FILE__) . '/mobile-template-post.php'), false);
							get_template_part( 'mobile/template/post', 'loop' );
						}
					}
				?>
				<!-- Loop Thread -->
			</div>
			<!-- button load more -->
			<?php
				wp_reset_query();
				if($page < $wp_query->max_num_pages) {
			?>
			<a href="#" id="more_blog" class="fe-btn-primary" data-cat="<?php echo get_query_var('cat');?>" data-page="<?php echo $page ?>" data-theme="d" data-role="button"><?php _e('Load more posts',ET_DOMAIN) ?></a>
			<?php } ?>
			<!-- button load more -->
		</div>
<?php
// footer part
get_template_part( 'mobile/template', 'footer' );

et_get_mobile_footer();
?>
