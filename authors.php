<?php


function credit_init() {
  add_rewrite_rule('^credit/([^/]+)/page/(\d+)/?$', 'index.php?page_id=0&credit=$matches[1]&page=$matches[2]', 'top');
  add_rewrite_rule('^credit/([^/]+)/?$', 'index.php?page_id=0&credit=$matches[1]', 'top');
  add_rewrite_tag("%credit%", '([^/]+)');
}
add_action( 'init', 'credit_init');

add_action( "template_redirect", "credit_url_rewrite");
function credit_url_rewrite() {
  if ( get_query_var( "credit" ) ) { 
    add_filter( "template_include", function() {
	return get_stylesheet_directory() . '/credit.php';
     });
  }
}

function modify_query( $query ) {
  if ( get_query_var( "credit" ) ) {
    $query->set("meta_query", array(
      array(
        'key' => "credit_name",
        'value' => urldecode(get_query_var("credit")),
        'compare' => "="
      )
    ));
    $query->set("paged", get_query_var("page"));
  }
}
add_action("pre_get_posts", "modify_query");

function credit_404() {
  if (get_query_var("credit") && !have_posts()) {
    global $wp_query;
    $wp_query->set_404();
  }
}
add_action("wp", "credit_404");

add_shortcode( 'credit', 'credit_shortcode');
function credit_shortcode( $atts, $content = null, $other = null, $force = false ) {
	$out = '';
	$classes = "credit-" . $atts["type"] . " ";
	if ($atts["type"] == "byline" && !$force) {
	        return $content;
		$classes .= "post-byline";
	}
	$link = '<a href="/credit/' . $atts["name"] . '/" title="View all of this person\'s work">';
	$link .= $atts["name"] . '</a>';
	$out .= '<span class="credit ' . $classes . '">' . $link;
	if (isset($atts["position"])) {
		$out .= ' (' . $atts["position"] . ')';
	}
	$out .= '</span>';
	return $out;
}

function tower_author_admin_init() {
  add_action( 'save_post', 'tower_author_save' );
}

add_action('admin_init', 'tower_author_admin_init');

function tower_author_save($post_id) {
        delete_post_meta($post_id, "credit_name");
	$content = get_post($post_id)->post_content;
	$pattern = get_shortcode_regex();
	$credits = array();
	$credit_names = array();
	if ( preg_match_all('/'.$pattern.'/s', $content, $matches) 
	     && array_key_exists( 2, $matches )
	     && in_array( 'credit', $matches[2] ) ) {
	  for ($i = 0; $i < count($matches[2]); $i++) {
	    if ($matches[2][$i] == "credit") {
	      $c = parse_credit($matches[3][$i]);
	      array_push($credits, $c);
	      add_post_meta($post_id, "credit_name", $c["name"]);
	    }
	  }
	}
	update_post_meta($post_id, "credits", $credits);
}

function parse_credit($match) {
  preg_match_all('/(name|position|type|nolink)="(.+?)"/si', $match, $attrs);
  return array_combine($attrs[1], $attrs[2]);
}

function format_with_oxford_comma($list) {
  if (count($list) < 2) {
    return $list[0];
  }
  $last = array_pop($list);
  $out = implode(", ", $list);
  if (count($list) > 1) {
    $out .= ",";
  }
  return $out . " and " . $last;
}

function the_credits( $type ) {
  $all_credits = get_post_meta( get_the_ID(), "credits", true);
  $credits = array();
  foreach ($all_credits as $credit) {
    if ( !isset($type) || (isset($credit["type"]) && $credit["type"] == $type) ) {
      array_push($credits, credit_shortcode($credit, null, null, true));
    }
  }
  if (count($credits) < 1) {
    return false;
  }
  echo "By " . format_with_oxford_comma($credits);
}