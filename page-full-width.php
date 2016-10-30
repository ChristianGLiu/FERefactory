<?php
/*
Template Name: page full width
*/
get_header();
?>
<?php the_post();?>

<div class="container main-center">
	<div class="row">
			<?php the_content() ;?>
	</div>
</div>

<?php get_footer() ?>

