<?php
/*
Template Name: Home Page
*/
?>

<?php
add_action( 'wp_enqueue_scripts', 'home_page_enqueue_styles' );
function home_page_enqueue_styles() {
  wp_enqueue_style( 'home-page-style', get_stylesheet_directory_uri() . '/page-home.css' );
}
 get_header();

$tags = get_tags();
for ($i=0;  $i < count($tags); $i++)
  {
    $tag_id_array[$i] = $tags[$i]->term_id;
  }

$the_section_array = array(
        "news",
	"opinions",
	"vanguard",
	"arts-and-entertainment",
	"sports"
);

$year_query = "SELECT meta_value FROM  $wpdb->postmeta WHERE meta_key = '_ao_issues_year' ORDER BY meta_id DESC LIMIT 1";
$year_result = $wpdb->get_results( $year_query );
$year = $year_result[0]->meta_value;

$number_query = "SELECT meta_value FROM  $wpdb->postmeta WHERE meta_key = '_ao_issues_number' ORDER BY meta_id DESC LIMIT 1";
$number_result = $wpdb->get_results( $number_query );
$number = $number_result[0]->meta_value + 1;

while ((!isset($others) || $others->found_posts < 8) && $number > 0) {
  $number -= 1;
  $args = array(
	    'posts_per_page' => -1,
	    'meta_query' => array(
	        array(
		      'key' => '_ao_issues_number',
		      'value' => $number
		      ),
	        array(
		      'key' => '_ao_issues_year',
		      'value' => $year
		      )
	        )
	    );

  $others = new WP_Query( $args );
}

$override_year = get_option("hp-override-year")["hp-override-year"];
$override_issue = get_option("hp-override-issue")["hp-override-issue"];
$override_category = get_option("hp-override-category")["hp-override-category"];

if ($override_year) {
  $year = $override_year;
}
if ($override_issue) {
  $number = $override_issue;
}
if ($override_category) {
  $the_section_array = array($override_category);
}

$args = array( // http://www.billerickson.net/code/wp_query-arguments/ for help with these
            // Change these category SLUGS to suit your use, or use the tag option
            'category_name' => $override_category,
            'tag' => 'featured-text',
	    'posts_per_page' => 5,
	    'orderby' => 'rand',
	    'meta_query' => array(
	        array(
		      'key' => '_ao_issues_number',
		      'value' => $number
		      ),
	        array(
		      'key' => '_ao_issues_year',
		      'value' => $year
		      )
	    )
        );

        $text_posts = new WP_Query( $args );

	    $args = array( // http://www.billerickson.net/code/wp_query-arguments/ for help with these
            // Change these category SLUGS to suit your use, or use the tag option
            'category_name' => $override_category,
            'tag' => 'triple',
	    'posts_per_page' => 6,
	    'orderby' => 'rand',
	    'meta_query' => array(
	        array(
		      'key' => '_ao_issues_number',
		      'value' => $number
		      ),
	        array(
		      'key' => '_ao_issues_year',
		      'value' => $year
		      )
	    )
        );

        $triple_query = new WP_Query( $args );
 ?>

<section class="content">
  <div class="pad group">
    <?php while ( have_posts() ): the_post(); ?>
    <article <?php post_class('group'); ?>>
      <?php get_template_part('inc/page-image'); ?>
      <div class="entry themeform">
  <?php the_content(); ?>
	<div class="clear"></div>
      </div><!--/.entry-->  
    </article>
    <?php endwhile; ?>
    
    <div class="content-wrap">
      <?php 
        /* The loop: the_post retrieves the content
         * of the new Page you created to list the posts,
         * e.g., an intro describing the posts shown listed on this Page..
         *
        if ( have_posts() ) :
            while ( have_posts() ) : the_post();

              // Display content of page
              get_template_part( 'content', get_post_format() ); 
              wp_reset_postdata();
  
            endwhile;
        endif; */ ?>

	<!-- REUSABLE BLOCK STARTS HERE (I think) -->
        <?php
	    $args = array( // http://www.billerickson.net/code/wp_query-arguments/ for help with these
            // Change these category SLUGS to suit your use, or use the tag option
	    'category_name' => $override_category?$override_category:'news',
            'tag' => 'banner',
	    'meta_query' => array(
	        array(
		      'key' => '_ao_issues_number',
		      'value' => $number
		      ),
	        array(
		      'key' => '_ao_issues_year',
		      'value' => $year
		      )
	    ),
	    'posts_per_page' => 1
        );

        $list_of_posts = new WP_Query( $args );
        ?>
        <?php if ( $list_of_posts->have_posts() ) : ?>
	    <?php /* The loop */ ?>
	    <?php while ( $list_of_posts->have_posts() ) : $list_of_posts->the_post(); ?>
	        <?php // Display content of posts
		      // Not sure about specific formatting
		      // Look up how to retrieve post information online
		      // Use inspect element to figure out how to manually duplicate the 
		      // automatic formatting done below ?>
		<?php get_template_part( 'banner-hp-content', get_post_format() ); ?>
	    <?php endwhile; ?>
	<?php endif; ?>
	<!-- REUSABLE BLOCK STARTS HERE (I think) -->
	<?php $text_posts->the_post(); ?>
	<?php get_template_part( 'text-hp-content', get_post_format() ); ?>
	<!-- REUSABLE BLOCK STARTS HERE (I think) -->
        <?php if ( $triple_query->have_posts() ) : ?>
	    <?php /* The loop */ ?><div class="hp-text-post">
	    <?php for ( $x = 0; $x < 3; $x++ ) : $triple_query->the_post(); ?>
		<?php get_template_part( 'block-3-hp-content', get_post_format() ); ?>
	    <?php endfor; ?></div>
	<?php endif; ?>
	<!-- REUSABLE BLOCK STARTS HERE (I think) -->
	<?php $text_posts->the_post(); ?>
	<?php get_template_part( 'text-hp-content', get_post_format() ); ?>
	<!-- HTML comment -->
        <?php
	    $args = array( // http://www.billerickson.net/code/wp_query-arguments/ for help with these
            // Change these category SLUGS to suit your use, or use the tag option
            'category_name' => $override_category?$override_category:'vanguard',
	    'offset' => $override_category ? 1 : 0,
            'tag' => 'banner',
	    'meta_query' => array(
	        array(
		      'key' => '_ao_issues_number',
		      'value' => $number
		      ),
	        array(
		      'key' => '_ao_issues_year',
		      'value' => $year
		      )
	    ),
	    'posts_per_page' => 1
        );

        $list_of_posts = new WP_Query( $args );
        ?>
        <?php if ( $list_of_posts->have_posts() ) : ?>
	    <?php /* The loop */ ?><div class="hp-banner-post">
	    <?php while ( $list_of_posts->have_posts() ) : $list_of_posts->the_post(); ?>
		<?php get_template_part( 'banner-hp-content', get_post_format() ); ?>
	    <?php endwhile; ?></div>
	<?php endif; ?>
	<!-- END OF REUSABLE BLOCK -->
	<?php $text_posts->the_post(); ?>
	<?php get_template_part( 'text-hp-content', get_post_format() ); ?>
<!-- HTML comment -->
        <?php if ( $triple_query->have_posts() ) : ?>
	    <?php /* The loop */ ?><div class="hp-triple-container">
	    <?php while ( $triple_query->have_posts() ) : $triple_query->the_post(); ?>
	        <?php // Display content of posts
		      // Not sure about specific formatting
		      // Look up how to retrieve post information online
		      // Use inspect element to figure out how to manually duplicate the 
		      // automatic formatting done below ?>
		<?php get_template_part( 'block-3-hp-content', get_post_format() ); ?>
	    <?php endwhile; ?></div>
	<?php endif; ?>
	<!-- END OF REUSABLE BLOCK -->
	<?php $text_posts->the_post(); ?>
	<?php get_template_part( 'text-hp-content', get_post_format() ); ?>

	<hr>
	<?php foreach ($the_section_array as $the_section): ?>
	<?php
	    $args = array(
	    'category_name' => $the_section,
            'tag__not_in' => $tag_id_array,
	    'posts_per_page' => -1,
	    'orderby' => 'rand',
	    'meta_query' => array(
	        array(
		      'key' => '_ao_issues_number',
		      'value' => $number
		      ),
	        array(
		      'key' => '_ao_issues_year',
		      'value' => $year
		      )
	        )
	    );

            $other_posts = new WP_Query( $args );
        ?>
        <?php if ( $other_posts->have_posts() ) : ?>
	    <?php while ( $other_posts->have_posts() ) : $other_posts->the_post(); ?>
		<?php get_template_part( 'text-small-content', get_post_format() ); ?>
	    <?php endwhile; ?>
	<?php endif; ?>
	<?php endforeach; ?>

</section><!--/.content-->

<?php // get_sidebar(); ?>

<?php get_footer(); ?>