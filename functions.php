<?php

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
  wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

}

if ( function_exists( 'add_image_size' ) ) {
	
	add_image_size( 'half-post', 300);
	
	add_image_size( 'home-page-triple-thumb', 420, 320, true );
	add_image_size( 'home-page-banner-thumb', 1320, 320, true );
}

remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'custom_trim_excerpt');

function trim_excerpt_to($text, $excerpt_length) {
	$words = explode(' ', $text, $excerpt_length + 1);
	if (count($words) > $excerpt_length) {
		array_pop($words);
		array_push($words, '...');
		$text = implode(' ', $words);
	}
	return $text;
}
function custom_trim_excerpt($text) { // Fakes an excerpt if needed
	global $post;
	if ( '' == $text ) {
		$text = get_the_content('');
		$text = strip_shortcodes($text);
		$text = apply_filters('the_content', $text);
		$text = str_replace(']]>', ']]>', $text);
		$text = strip_tags($text);
		$excerpt_length = 100;

		$words = explode(' ', $text);
		if (strtolower($words[0]) == "by") { array_shift($words); }
		while ($words[0] == "," || (count($words) > 0 && $words[0] == "")) { array_shift($words); }
		if (strtolower($words[0]) == "and") { array_shift($words); }
		$text = trim_excerpt_to(implode(" ", $words), $excerpt_length);
	}
	return $text;
}

include_once("authors.php");


function fix_authors() {
  $pt = $post;
  global $post;
  $query = new WP_Query(array("posts_per_page" => -1, "post_type" => "post"));
  $c = 0;
  while ($query->have_posts()) {
    $query->the_post();
    if (get_post_meta($post->ID, "_fixed_authors", true) != 1) {
      $content = "";
      foreach(get_coauthors() as $a) {
	$parts = explode(" (", $a->display_name);
	$name = $parts[0];
	$position = trim($parts[1], ")");
	$shortcode = "[credit name=\"$name\" position=\"$position\" type=\"byline\"]";
	$content .= $shortcode;
      }
      $post->post_content = $content . $post->post_content;
      wp_update_post(array("ID" => $post->ID, "post_content" => $post->post_content) );
      tower_author_save($post->ID);
      update_post_meta($post->ID, "_fixed_authors", 1);
      echo $c . "<br>";
      $c++;
    }
  }
  $post = $pt;
}

add_action("admin_post_fix_authors", "fix_authors");