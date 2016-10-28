<?php
et_get_mobile_header();
// header part
get_template_part( 'mobile/template', 'header' );

global $post,$user_ID,$current_user,$max_file_size;
the_post();
$thread 		= FE_Threads::convert($post);
$user_following = explode(',', (string) get_post_meta( $post->ID, 'et_users_follow',true));
$is_followed    = in_array($user_ID, $user_following);
if ( !empty($thread->category[0]) )
	$color = FE_ThreadCategory::get_category_color($thread->category[0]->term_id);
else
	$color = 0;

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
<div data-role="content" class="fe-content fe-content-thread">
	<div itemscope itemtype="http://schema.org/Article">
		<div class="fe-page-heading">
			<div class="fe-avatar fe-nav">

				<?php if(!$user_ID){?>
				 <?php echo "<div class='mobile-social-wrapper' style='float:left;margin-top:10px;'><span class='mobile-social-bar-content'>".open_social_login_html()."</span></div>";?>

				<a href="<?php echo et_get_page_link('login') ?>" class="fe-nav-btn fe-btn-profile">注册或登录点这里</a>
				<?php } else {?>
				 <?php
				 $current_user = wp_get_current_user();

				 echo "<span class='mobile-social-bar-label'>欢迎回来:".$current_user->display_name."</span>";?>
				<a href="<?php echo get_author_posts_url($user_ID) ?>" class="fe-head-avatar toggle-menu"><?php echo  et_get_avatar($user_ID);?></a>
				<?php } ?>
			</div>
			<ul class="fe-thread-actions">
				<li class="unfollow" style="<?php if(!$is_followed) echo 'display:none;' ?>">
					<a class="tog-follow" data-id="<?php echo $thread->ID ?>" href="#"><span class="fe-icon fe-icon-minus"></span> <?php _e('Unfollow', ET_DOMAIN) ?></a>
				</li>
				<li class="follow" style="<?php if($is_followed) echo 'display:none;' ?>">
					<a class="tog-follow" data-id="<?php echo $thread->ID ?>"  href="#"><span class="fe-icon fe-icon-plus"></span> <?php _e('Follow', ET_DOMAIN) ?></a>
				</li>
				<?php if($thread->post_status == "pending" && current_user_can( 'manage_threads' ) ){ ?>
				<li>
					<a class="fe-act fe-act-approve" href="#" data-act="approve" data-id="<?php echo $thread->ID;?>" ><span class="fe-icon fe-icon-approve"></span><?php _e('Approve', ET_DOMAIN) ?></a>
				</li>
				<li>
					<a class="fe-act fe-act-delete" href="#" data-act="delete" data-id="<?php echo $thread->ID;?>"><span class="fe-icon fe-icon-delete"></span><?php _e('Delete', ET_DOMAIN) ?></a>
				</li>
				<?php } ?>
			</ul>
		</div>
		<?php get_template_part( 'mobile/template', 'profile-menu' ) ?>
		<div class="fe-thread-info <?php if( !current_user_can( 'manage_threads' )) echo 'un-auth'?>">
			<?php if(current_user_can( 'manage_threads' )){?>
			<a href="#" class="fe-btn-ctrl"><span class="fe-icon fe-icon-edit"></span></a>
			<?php } ?>
			<div class="fe-info-container">
				<h1 itemprop="name" class="fe-title"><?php the_title(); ?></h1>
				<span class="time"><?php printf( __( 'Updated %s in', ET_DOMAIN ),et_the_time(strtotime($thread->et_updated_date))); ?></span>
				<?php if ( $thread->has_category ) {  ?>
				<span class="time"><span class="flags color-<?php echo $color ?>"></span><?php echo $thread->category[0]->name ?>.</span>
				<?php } else {  ?>
				<span class="time"><span class="flags color-0"></span><?php _e('No Category', ET_DOMAIN) ?>.</span>
				<?php } ?>
			</div>
			<div class="fe-info-actions fe-actions-container">
				<ul class="">
					<?php /* ?><li>
						<a class="fe-act" href="#"><span class="fe-icon fe-icon-star"></span> <?php _e('Highlight', ET_DOMAIN) ?></a>
					</li> */ ?>
					<?php if($thread->post_status == "closed"){ ?>
					<li>
						<a class="fe-act fe-act-approve" data-act="close" data-id="<?php echo $thread->ID;?>" href="#"><span class="fe-icon fe-icon-lock"></span><?php _e('Unclose', ET_DOMAIN) ?></a>
					</li>
					<?php } else { ?>
					<li>
						<a class="fe-act fe-act-approve" data-act="close" data-id="<?php echo $thread->ID;?>" href="#"><span class="fe-icon fe-icon-lock"></span><?php _e('Close', ET_DOMAIN) ?></a>
					</li>
					<?php } ?>
					<li>
						<a class="fe-act fe-act-delete" data-act="delete" data-id="<?php echo $thread->ID;?>" href="#"><span class="fe-icon fe-icon-del-blue"></span><?php _e('Delete', ET_DOMAIN) ?></a>
					</li>
				</ul>
			</div>
		</div>
		<div class="fe-th-posts">
			<!-- End Thread Content -->
			<article class="fe-th-post fe-th-thread" id="reply_<?php echo $thread->ID; ?>">
				<a href="#" class="fe-avatar">
					<?php echo et_get_avatar($post->post_author);?>
					<?php do_action( 'fe_user_badge', $post->post_author ); ?>
				</a>
				<div class="fe-th-container">
					<div class="fe-th-heading">
						<div class="fe-th-info">
							<span class="comment <?php if ( $thread->replied ) echo 'active' ?>" itemprop="interactionCount">
								<span class="fe-icon fe-icon-comment fe-sprite" data-icon="w"></span><?php echo $thread->et_replies_count ?>
							</span>
							<a href="#" class="like" data-id="<?php echo $thread->ID ?>">
								<span class="like <?php if ($thread->liked) echo 'active' ?>">
									<span class="fe-icon fe-icon-like fe-sprite" data-icon="k" itemprop="interactionCount"></span><span class="count"><?php echo $thread->et_likes_count ?></span>
								</span>
							</a>
							<span class="time">
								<?php echo et_the_time( strtotime( $thread->post_date ) ) ?>
							</span>
						</div>
						<span class="title" itemprop="author"><?php the_author() ?></span>
					</div>
					<div class="fe-th-content">
						<?php the_content(); ?>
					</div>
					<!-- form edit -->
					<div class="fe-topic-form hidden clearfix">
						<div class="fe-topic-input">
							<input type="hidden" name="fe_nonce" id="fe_nonce" value="<?php echo wp_create_nonce( 'edit_thread' ) ?>">
							<div class="fe-topic-dropbox">
								<select name="category" id="category">
									<?php
										$current_cat = empty($thread->category[0]) ? false : $thread->category[0]->term_id;
										$categories = FE_ThreadCategory::get_categories();
										et_the_cat_select($categories,$current_cat);
									?>
								</select>
							</div>
							<input type="text" name="thread_title" id="thread_title" value="<?php echo $thread->post_title ?>">
						</div>
						<div class="fe-topic-content" style="display:block;">
							<?php if(get_option('upload_images')){ ?>
							<div class="insert-image-wrap" data-id="<?php echo $thread->ID ?>">
								<div class="form-post" id="<?php echo $thread->ID ?>_images_upload_container">
									<a href="javascript:void(0)" id="<?php echo $thread->ID ?>_images_upload_browse_button"><img src="<?php echo get_template_directory_uri();?>/mobile/img/ico-pic.png" /><?php _e("Insert Image", ET_DOMAIN); ?></a>
									<span class="et_ajaxnonce" id="<?php echo wp_create_nonce( 'et_upload_images' ); ?>"></span>
									<span id="images_upload_text"><?php printf(__("Size must be less than < %sMB.", ET_DOMAIN),$max_file_size); ?></span>
								</div>
							</div>
							<?php } ?>
							<div class="textarea">
								<?php // wp_editor( get_the_content(), 'thread_content' , editor_settings()) ?>
								<textarea id="thread_content"><?php echo strip_tags(get_the_content()) ?></textarea>
							</div>
							<div class="fe-form-actions pull-right">
								<a href="#reply_<?php echo $thread->ID; ?>" class="fe-btn" id="update_thread" data-id="<?php echo $thread->ID; ?>" data-role="button"><?php _e('Save',ET_DOMAIN) ?></a>
								<a href="#" class="fe-btn-cancel fe-icon-b fe-icon-b-cancel cancel-modal ui-link"><?php _e('Cancel', ET_DOMAIN) ?></a>
							</div>
						</div>
					</div>
					<!-- form edit -->
					<div class="fe-th-ctrl">
						<div class="fe-th-ctrl-right">
							<?php if(user_can_edit($thread)){?>
							<a href="#reply_<?php echo $thread->ID ?>" class="fe-icon fe-icon-edit"></a>
							<?php } ?>
							<?php if($thread->post_status != "closed"){ ?>
							<a href="#reply_<?php echo $thread->ID ?>" data-id="<?php echo $thread->ID ?>" class="fe-icon fe-icon-quote"></a>
							<?php } ?>
							<!-- <a href="" class="fe-icon fe-icon-report"></a> -->
						</div>
						<?php if($thread->post_status != "closed"){ ?>
						<div class="fe-th-ctrl-left">
							<a href="#" class="fe-reply scroll_to_reply"><?php _e('Reply',ET_DOMAIN); ?> <span class="fe-icon fe-icon-reply"></span></a>
						</div>
						<?php } ?>
					</div>
				</div>
			</article>
			<!-- End Thread Content -->
			<!-- Start Loop Replies -->
			<?php
				$replies_query = FE_Replies::get_replies(array('paged' => get_query_var( 'page' ), 'post_parent' => $post->ID, 'order' => 'ASC' )) ;
				if ( $replies_query->have_posts() ){
					while ( $replies_query->have_posts() ) {
						$replies_query->the_post();
						$reply 			= FE_Replies::convert($post);
						get_template_part( 'mobile/template/reply', 'item' );
					} // end while
				} // end if
			?>
			<!-- End Loop Replies -->
		</div>
		<!-- button load more -->
		<?php
			wp_reset_query();
			$current_page = (get_query_var('paged')) ? get_query_var('paged') : 1;
			if($current_page < $replies_query->max_num_pages) {
		?>
		<a href="#" id="more_reply" class="fe-btn-primary" data-status="index" data-page="<?php echo $current_page ?>" data-id="<?php echo $thread->ID; ?>" data-role="button"><?php _e('Load More Replies',ET_DOMAIN) ?></a>
		<?php } ?>
		<!-- button load more -->
		<?php if($thread->post_status != "closed"){ ?>
		<div class="fe-container fe-topic-content fe-expanded">
			<div id="main_reply" class="fe-reply-box">
				<?php if(!$user_ID){ ?>
				<div class="fe-login-to-reply">
					<a href="<?php echo et_get_page_link('login'); ?> " rel="external" target="_self"><?php _e('Login to Reply',ET_DOMAIN) ?></a>
				</div>
				<?php } else{ ?>
				<div class="fe-reply-overlay">
				<span><?php _e('Touch to Reply',ET_DOMAIN) ?></span>
				</div>
				<?php } ?>
				<?php if(get_option('upload_images')){ ?>
				<div class="insert-image-wrap" style="display:none;">
					<div class="form-post" id="images_upload_container">
						<a href="javascript:void(0)" id="images_upload_browse_button"><img src="<?php echo get_template_directory_uri();?>/mobile/img/ico-pic.png" /><?php _e("Insert Image", ET_DOMAIN); ?></a>
						<span class="et_ajaxnonce" id="<?php echo wp_create_nonce( 'et_upload_images' ); ?>"></span>
						<span id="images_upload_text">
							<?php
							printf(__("Size must be less than < %sMB.", ET_DOMAIN),$max_file_size); ?>

						</span>
					</div>
				</div>
				<?php } ?>
				<textarea id="reply_content"></textarea>
				<div class="fe-reply-actions">
					<a href="#" class="fe-btn-primary" id="reply_thread" data-id="<?php echo $thread->ID; ?>" data-role="button"><?php _e('Reply',ET_DOMAIN) ?></a>
					<a href="#" class="fe-btn-cancel fe-icon-b fe-icon-b-cancel cancel-modal ui-link"><?php _e('Cancel', ET_DOMAIN) ?></a>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
<?php
// footer part
get_template_part( 'mobile/template', 'footer' );

et_get_mobile_footer();
?>