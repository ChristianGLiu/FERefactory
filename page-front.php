<?php
/**
* Template Name: Front Page Template
*/
get_header();
global $wp_query, $wp_rewrite, $post, $current_user , $user_ID, $wpdb;

$data = et_get_unread_follow();
$term = get_term_by( 'slug' , get_query_var( "term" ), 'category') ;
?>
<div class="header-bottom header-filter">
<div class="main-center container">
<?php
fe_navigations();
?>

</div>
<div class="mo-menu-toggle visible-sm visible-xs">
<span style="
    font-size: 18px;
    font-weight: 900;
    color: brown;
">贴子类别</span>
<a class="icon-menu-tablet" href="#"><?php _e('open', ET_DOMAIN ) ?></a>
</div>

</div>
<!--end header Bottom-->
<div class="container main-center">
<div class="row">
<!--div id="arrow-down-topic" class="arrow-down bounce">
  <span>
猛击这里发表自己的贴子吧
  </span>
</div-->

<div class="side-adv">
<img src="/wp-content/uploads/2016/10/201610250725189.gif"></img>
</div>

<div class="col-md-9 col-sm-12 marginTop30">
<h7 class="home-house-feature"><a href="/%e6%88%bf%e5%b1%8b%e7%a7%9f%e5%94%ae/">每日房屋精选</a></h7>
<?php
echo do_shortcode('[es_my_listing_special]');

?>

<?php get_template_part('template/post', 'thread'); ?>
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
	<ul id="main_list_post" class="list-post">
	<?php
	if ( !empty( $sticky_threads[0] ) ){
		// load sticky thread
		get_template_part( 'template/sticky', 'thread' );
	}

	while ($thread_query_1->have_posts()){
		$thread_query_1->the_post();
		get_template_part( 'template/thread', 'loop' );
	} // end while
	?>
	</ul>
	<?php
	wp_reset_query();
}
?>

<!--div style="width:100%;">
<div style="width:49%"-->
<?php
//es_latest_props();
?>
<!--/div>
<div style="width:49%"-->
<?php
//echo geodir_sc_gd_listings();
?>
<!--/div>
</div-->

<?php
$thread_query_2 = FE_Threads::get_threads(array(
'post_type' 	=> 'post',
'cat' => '116',
'posts_per_page' => 4,
'orderby' => 'date',
'order'   => 'DESC',
));

if (  $thread_query_2->have_posts() ){ ?>
	<ul id="main_list_post" class="list-post">
	<?php
	if ( !empty( $sticky_threads[0] ) ){
		// load sticky thread
		get_template_part( 'template/sticky', 'thread' );
	}

	while ($thread_query_2->have_posts()){
		$thread_query_2->the_post();
		get_template_part( 'template/thread', 'loop' );
	} // end while
	?>
	</ul>
	<?php
	wp_reset_query();
}

?>
<div class="ad-area">
<div class=""><a href="/places/canada/nova-scotia/halifax/餐馆/panda-buffet-熊猫自助/" target="_blank"><img src="/wp-content/uploads/2016/10/buffet.jpg"></img></a></div>
<div class=""><a href="http://www.taishanstore.net" target="_blank"><img src="/wp-content/uploads/2016/10/taishanadv.gif"></img></a></div>
<div class=""><img src="/wp-content/uploads/2016/10/hawi-ad.png"></img></div>
<div class=""><a href="http://happykidshalifax.ca/" target="_blank"><img src="/wp-content/uploads/2016/10/happykids.gif"></img></a></div><br />
<div class=""><img src="/wp-content/uploads/2016/10/dsfasdfnew.jpg"></img></div>
<div class=""><img src="/wp-content/uploads/2016/10/yanfan.png"></img></div>
<div class=""><img src="/wp-content/uploads/2016/10/guojun.jpg"></img></div>
<div class=""><img src="/wp-content/uploads/2016/10/dandan.jpg"></img></div>
<div class=""><img src="/wp-content/uploads/2016/11/mmexport1477766613715.jpg"></img></div>
</div>
<?php

	$thread_query = FE_Threads::get_threads(array(
	'post_type' 	=> 'post',
	'paged' 		=> $page,
	'post__not_in' 	=> $sticky_threads[0],
	'cat' => '-56,-116',
	'orderby' => 'date',
    'order'   => 'DESC'
	));


if (  $thread_query->have_posts() ){ ?>
	<ul id="main_list_post" class="list-post">
	<?php
	if ( !empty( $sticky_threads[0] ) ){
		// load sticky thread
		get_template_part( 'template/sticky', 'thread' );
	}
	while ($thread_query->have_posts()){
		$thread_query->the_post();
		get_template_part( 'template/thread', 'loop' );
	} // end while
	?>
	</ul>
	<script type="text/javascript">
	var threads_exclude = <?php echo json_encode($sticky_threads[0]); ?>;
	</script>
	<?php
} else { ?>
	<div class="notice-noresult">
	<span class="icon" data-icon="!"></span><?php _e('No topic has been created yet.', ET_DOMAIN) ?> <a href="#" id="create_first"><?php _e('Create the first one', ET_DOMAIN) ?></a>.
	</div>
	<?php
} // end if
wp_reset_query();

?>
<?php if(!get_option( 'et_infinite_scroll' )){ ?>

	<!-- Normal Paginations -->
	<div class="pagination pagination-centered" id="main_pagination">
	<?php
	echo paginate_links( array(
	'base' 		=> str_replace('99999', '%#%', esc_url(get_pagenum_link( 99999 ))),
	'format' 	=> $wp_rewrite->using_permalinks() ? 'page/%#%' : '?paged=%#%',
	'current' 	=> max(1, $page),
	'total' 	=> $thread_query->max_num_pages,
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
	$fetch = ($page < $thread_query->max_num_pages) ? 1 : 0 ;
	//$check = round((int) 10 / (int) get_option( 'posts_per_page' ) , 0 , PHP_ROUND_HALF_DOWN);
	$check = floor((int) 10 / (int) get_option( 'posts_per_page' ));
	?>
	<div id="loading" class="hide" data-fetch="<?php echo $fetch ?>" data-status="scroll-index" data-check="<?php echo $check ?>">
	<!-- <img src="<?php echo get_template_directory_uri(); ?>/img/ajax-loader.gif"> -->
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
	<input type="hidden" value="<?php echo $thread_query->max_num_pages ?>" id="max_page">
	</div>
	<!-- Infinite Scroll -->

	<?php } ?>
</div>
<div class="col-md-3 hidden-sm hidden-xs sidebar">
<?php get_sidebar('home') ?>
</div>
</div>
</div>

<?php get_footer(); ?>

