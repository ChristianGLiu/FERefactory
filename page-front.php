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
<a class="icon-menu-tablet" href="#"><?php _e('open', ET_DOMAIN ) ?></a>
</div>

</div>
<!--end header Bottom-->
<div class="container main-center">
<div class="row">
<div class="col-md-9 col-sm-12 marginTop20">
<div class="col-md-2"><a href="/places/canada/nova-scotia/halifax/餐馆/panda-buffet-熊猫自助/" target="_blank"><img src="/wp-content/uploads/2016/10/pandaBufffetAdv.gif"></img></a></div>
<div class="col-md-2"><a href="http://www.taishanstore.net" target="_blank"><img src="/wp-content/uploads/2016/10/taishanadv.gif"></img></a></div>
<div class="col-md-4"><img src="/wp-content/uploads/2016/10/advneed.png"></img></div>
<div class="col-md-4"><img src="/wp-content/uploads/2016/10/advneed.png"></img></div>
</div>
<div class="side-adv">
<img src="/wp-content/uploads/2016/10/201610250725189.gif"></img>
</div>
<div class="col-md-9 col-sm-12 marginTop30">
<div id="arrow-down-topic" class="arrow-down bounce">
  <span>
猛击这里发表自己的贴子吧
  </span>
</div>
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
$thread_query_2 = FE_Threads::get_threads(array(
'post_type' 	=> 'post',
'cat' => '117',
'posts_per_page' => 2,
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

$thread_query_3 = FE_Threads::get_threads(array(
'post_type' 	=> 'post',
'cat' => '14',
'posts_per_page' => 2,
'orderby' => 'date',
'order'   => 'DESC',
));
if (  $thread_query_3->have_posts() ){ ?>
	<ul id="main_list_post" class="list-post">
	<?php
	if ( !empty( $sticky_threads[0] ) ){
		// load sticky thread
		get_template_part( 'template/sticky', 'thread' );
	}

	while ($thread_query_3->have_posts()){
		$thread_query_3->the_post();
		get_template_part( 'template/thread', 'loop' );
	} // end while
	?>
	</ul>
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
	<ul id="main_list_post" class="list-post">
	<?php
	if ( !empty( $sticky_threads[0] ) ){
		// load sticky thread
		get_template_part( 'template/sticky', 'thread' );
	}

	while ($thread_query_8->have_posts()){
		$thread_query_8->the_post();
		get_template_part( 'template/thread', 'loop' );
	} // end while
	?>
	</ul>
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
	<ul id="main_list_post" class="list-post">
	<?php
	if ( !empty( $sticky_threads[0] ) ){
		// load sticky thread
		get_template_part( 'template/sticky', 'thread' );
	}

	while ($thread_query_4->have_posts()){
		$thread_query_4->the_post();
		get_template_part( 'template/thread', 'loop' );
	} // end while
	?>
	</ul>
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
	<ul id="main_list_post" class="list-post">
	<?php
	if ( !empty( $sticky_threads[0] ) ){
		// load sticky thread
		get_template_part( 'template/sticky', 'thread' );
	}

		while ($thread_query_5->have_posts()){
		$thread_query_5->the_post();
		get_template_part( 'template/thread', 'loop' );
	} // end while
	?>
	</ul>
	<?php
	}

	wp_reset_query();

	$thread_query_7 = FE_Threads::get_threads(array(
    'post_type' 	=> 'post',
    'cat' => '119',
    'posts_per_page' => 2,
    'orderby' => 'date',
    'order'   => 'DESC',
    ));
    if (  $thread_query_7->have_posts() ){ ?>
    	<ul id="main_list_post" class="list-post">
    	<?php
    	if ( !empty( $sticky_threads[0] ) ){
    		// load sticky thread
    		get_template_part( 'template/sticky', 'thread' );
    	}

    		while ($thread_query_7->have_posts()){
    		$thread_query_7->the_post();
    		get_template_part( 'template/thread', 'loop' );
    	} // end while
    	?>
    	</ul>
    	<?php
    	}

    	wp_reset_query();

	$thread_query = FE_Threads::get_threads(array(
	'post_type' 	=> 'post',
	'paged' 		=> $page,
	'post__not_in' 	=> $sticky_threads[0],
	'category__not_in' => array(60,115,14,117,56,119,129)
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

