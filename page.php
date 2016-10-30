<?php get_header() ?>
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
<?php the_post();?>

<div class="container main-center">
	<div class="row">
		<div class="col-md-9 marginTop30">
			<h1><?php the_title() ?></h1>
			<?php the_content() ;?>
		</div>
		<div class="col-md-3 hidden-sm hidden-xs sidebar">
			<?php get_sidebar( ); ?>
			<!-- end widget -->
		</div>
	</div>
</div>

<?php get_footer() ?>