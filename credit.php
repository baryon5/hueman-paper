<?php 
$author_name = urldecode(get_query_var("credit"));

function filter_title($title, $sep) {
  global $author_name;
  return $author_name . " | " . get_bloginfo( 'name', 'display' );
}
if (have_posts()) {
  add_filter("wp_title", "filter_title");
}
else {
  include( get_query_template("404") ) ;
  die();
}

$backup_query = $wp_query;
$wp_query = new WP_Query(array('post_type' => 'post',
			       'posts_per_page' => 1));
get_header();
$wp_query = $backup_query;

 ?>

<section class="content">
    <div class="page-title pad group">
  <h1><i class="fa fa-user"></i><?php echo $author_name; ?><span>&apos;s Work</span></h1>
    </div>
	<div class="pad group">		
		
		<?php if ((category_description() != '') && !is_paged()) : ?>
			<div class="notebox">
				<?php echo category_description(); ?>
			</div>
		<?php endif; ?>
		
		<?php if ( have_posts() ) : ?>
		
			<?php if ( ot_get_option('blog-standard') == 'on' ): ?>
				<?php while ( have_posts() ): the_post(); ?>
					<?php get_template_part('content-standard'); ?>
				<?php endwhile; ?>
			<?php else: ?>
			<div class="post-list group">
				<?php $i = 1; echo '<div class="post-row">'; while ( have_posts() ): the_post(); ?>
					<?php get_template_part('content'); ?>
				<?php if($i % 2 == 0) { echo '</div><div class="post-row">'; } $i++; endwhile; echo '</div>'; ?>
			</div><!--/.post-list-->
			<?php endif; ?>
		
			<?php get_template_part('inc/pagination'); ?>
			
		<?php endif; ?>
		
	</div><!--/.pad-->
	
</section><!--/.content-->

<?php
$backup_query = $wp_query;
$wp_query = new WP_Query(array('post_type' => 'post',
			       'posts_per_page' => 1));
get_sidebar();
get_footer();
$wp_query = $backup_query;
?>