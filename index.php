<?php
get_header();
?>
<!--end header Bottom-->
<div class="container main-center">
<?php echo "this is index.php<br />"; ?>
	<div class="row">
	<div class="col-md-9 col-sm-12 marginTop30">
	<div id="form_thread" class="thread-form auto-form new-thread">
    				<form action="" method="post">
    					<input type="hidden" name="fe_nonce" class="fe_nonce" value="<?php echo wp_create_nonce( 'insert_thread' ) ?>">
    					<div class="text-search">
    						<div class="input-container">
    							<input class="inp-title" id="thread_title" maxlength="90" name="post_title" type="text" autocomplete="off" placeholder="<?php _e('Click here to start your new topic' , ET_DOMAIN) ?>">
    						</div>
    						<div class="btn-group cat-dropdown dropdown category-search-items collapse">
    							<span class="line"></span>
    							<button class="btn dropdown-toggle" data-toggle="dropdown">
    								<span class="text-select"></span>
    								<span class="caret"></span>
    							</button>
    							<?php
    							$categories = FE_ThreadCategory::get_categories();
    							?>
    							<select class="collapse" name="category" id="category">
    								<option value=""><?php _e('Please select' , ET_DOMAIN) ?></option>
    								<?php et_the_cat_select($categories) ?>
    							</select>
    						</div>
    				  	</div>
    					<div class="form-detail collapse">
    						<?php wp_editor( '' , 'post_content' , editor_settings() ); ?>
    						<?php
    							// $useCaptcha = et_get_option('google_captcha') ;
    							// if($useCaptcha){
    								do_action( 'fe_custom_fields_form' );
    							// }
    						?>
    						<div class="row line-bottom">
    							<div class="col-md-6">
    								<div class="show-preview">
    									<div class="skin-checkbox">
    										<span class="icon" data-icon="3"></span>
    										<input type="checkbox" name="show_preview" class="checkbox-show" id="show_topic_item" style="display:none" />
    									</div>
    									<a href="#"><?php _e('Show preview' , ET_DOMAIN) ?></a>
    								</div>
    							</div>
    							<div class="col-md-6">
    								<div class="button-event">
    									<input type="submit" value="
    									<?php
    										if($user_ID){
    											_e('Create Topic', ET_DOMAIN);
    										} else {
    											_e('Login and Create Topic', ET_DOMAIN);
    										}
    									?>
    									" class="btn">
    									<a href="#" class="cancel"><span class="btn-cancel"><span class="icon" data-icon="D"></span><?php _e('Cancel' , ET_DOMAIN) ?></span></a>
    								</div>
    							</div>
    						</div>
    					</div>
    				</form>
    				<div id="thread_preview">
    					<div class="name-preview"><?php _e('YOUR PREVIEW' , ET_DOMAIN) ?></div>
    			        <ul class="detail-preview list-post">
    			            <li>
    			              <span class="thumb"><?php echo  et_get_avatar($user_ID);?></span>
    			              <span class="title f-floatright" id="preview_title"><a href="#"><?php _e('Click here to start your new topic' , ET_DOMAIN) ?></a></span>
    			              <div class="post-information f-floatright">
    			                <span class="times-create"><?php _e('Just now in',ET_DOMAIN) ?></span>
    			                <span class="type-category"><span class="flags color-2"></span><?php _e('Please select.',ET_DOMAIN) ?></span>
    			                <!-- <span class="author"><span class="last-reply">Last reply</span> by <span class="semibold"><?php echo $current_user->user_login;?></span>.</span> -->
    			                <span class="comment"><span class="icon" data-icon="w"></span>0</span>
    			                <span class="like"><span class="icon" data-icon="k"></span>0</span>
    			              </div>
    			              <div class="text-detail f-floatright"></div>
    			            </li>
    			        </ul>
    				</div><!-- End Preview Thread -->
    			</div> <!-- End Form Thread -->
    			</div>

		<div class="col-md-9 col-sm-12 marginTop30 blog-listing" id="main_list_post">

			<?php
			if (  have_posts() ){ ?>
				<?php
				/**
				 * Display regular threads
				 */
				while (have_posts()){
					the_post();
					get_template_part( 'content' );
				} // end while
				?>
				<?php
			} else { ?>
				<div class="notice-noresult">
					<span class="icon" data-icon="!"></span><?php _e('No post has been created yet.', ET_DOMAIN) ?> <a href="#" id="create_first"><?php _e('Create the first one', ET_DOMAIN) ?></a>.
				</div>
				<?php
			} // end if
			?>

			<?php if(!get_option( 'et_infinite_scroll' )){ ?>

			<!-- Normal Paginations -->
			<div class="pagination pagination-centered" id="main_pagination">
				<?php
					$page = get_query_var('paged') ? get_query_var('paged') : 1;
					echo paginate_links( array(
						'base' 		=> str_replace('99999', '%#%', esc_url(get_pagenum_link( 99999 ))),
						'format' 	=> $wp_rewrite->using_permalinks() ? 'page/%#%' : '?paged=%#%',
						'current' 	=> max(1, $page),
						'total' 	=> $wp_query->max_num_pages,
						'prev_text' => '<',
						'next_text' => '>',
						'type' 		=> 'list'
					) );
				?>
			</div>
			<!-- Normal Paginations -->

			<?php } else { ?>

			<!-- Infinite Scroll -->
			<?php $fetch = ($page < $wp_query->max_num_pages) ? 1 : 0 ; ?>
			<div id="post_loading" class="hide" data-fetch="<?php echo $fetch ?>" data-status="scroll-blog" data-cat="<?php echo get_query_var('cat' );?>">
				<!-- <img src="<?php echo get_template_directory_uri(); ?>/img/ajax-loader.gif"> -->
				<div class="bubblingG">
					<span id="bubblingG_1">
					</span>
					<span id="bubblingG_2">
					</span>
					<span id="bubblingG_3">
					</span>
				</div>
				<?php _e( 'Loading more posts', ET_DOMAIN ); ?>
				<input type="hidden" value="<?php echo $page ?>" id="current_page">
				<input type="hidden" value="<?php echo $wp_query->max_num_pages ?>" id="max_page">
			</div>
			<!-- Infinite Scroll -->

			<?php } ?>
		</div>
		<div class="col-md-3 hidden-sm hidden-xs sidebar">
			<?php get_sidebar( 'blog' ) ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>

