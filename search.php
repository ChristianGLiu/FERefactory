<?php
get_header();
global $wp_query,$et_query, $wp_rewrite, $post,$current_user , $user_ID;

$data = et_get_unread_follow();
?>

<div class="header-bottom header-filter">
	<div class="main-center">
		<ul class="nav-link">
				<li>
                                				<a href="/关于我们/">
                                				关于我们

                                				</a>
                                			</li>

		</ul>
	</div>
	<div class="mo-menu-toggle visible-sm visible-xs">
	<span style="
        font-size: 18px;
        font-weight: 900;
        color: brown;
    ">贴子类别</span>
		<a class="icon-menu-tablet" href="#"><?php _e('open',ET_DOMAIN) ?></a>
	</div>
</div>
<!--end header Bottom-->
<div class="container main-center">
	<div class="row">
		<div class="col-md-9 marginTop30">
			<?php get_template_part('template/post', 'thread'); ?>
			<ul id="main_list_post" class="list-post">
			<?php
			if (  have_posts() ){ ?>

					<?php
					/**
					 * Display regular threads
					 */
					while (have_posts()){
						the_post();
						get_template_part( 'template/thread', 'loop' );
					} // end while
					?>

				<?php
			} else {
				$s = !empty($et_query) ? implode(' ', $et_query['s']) : '';
			?>
				<div class="notice-noresult">
					<span class="icon" data-icon="!"></span><?php echo sprintf( __( "0 results found for %s. Please try again.", ET_DOMAIN ), $s );?>
				</div>
				<?php
			} // end if
			?>
			</ul>

			<?php
				global $et_query;
				$page = get_query_var('paged') ? get_query_var('paged') : 1;
				if(!get_option( 'et_infinite_scroll' )){
			?>
			<!-- Normal Paginations -->
			<div class="pagination pagination-centered" id="main_pagination">
				<?php
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
			<?php
				$fetch = ($page < $wp_query->max_num_pages) ? 1 : 0 ;
				$check = floor((int) 10 / (int) get_option( 'posts_per_page' ));
			?>
			<div id="loading" class="hide" data-fetch="<?php echo $fetch ?>" data-status="scroll-search" data-s="<?php echo implode(' ', $et_query['s']); ?>" data-check="<?php echo $check ?>" <?php if(isset($_GET['tax_cat'])){ echo 'data-term="'.$_GET['tax_cat'].'"'; } ?>>
				<div class="bubblingG">
					<span id="bubblingG_1">
					</span>
					<span id="bubblingG_2">
					</span>
					<span id="bubblingG_3">
					</span>
				</div>
				<?php _e( 'Loading more threads', ET_DOMAIN ); ?>
				<input type="hidden" value="<?php echo $page ?>" id="current_page">
				<input type="hidden" value="<?php echo $wp_query->max_num_pages ?>" id="max_page">
			</div>
			<!-- Infinite Scroll -->

			<?php } ?>
		</div>
		<div class="col-md-3 hidden-sm hidden-xs">
			<?php get_sidebar( ) ?>
			<!-- end widget -->
		</div>
	</div>
</div>

<?php get_footer(); ?>

