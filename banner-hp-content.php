<article id="post-<?php the_ID(); ?>" <?php post_class('group hp-banner-post'); ?>>	
	<div class="post-inner post-hover">
		
		<div class="post-thumbnail">
			<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
				<?php if ( has_post_thumbnail() ): ?>
					<?php the_post_thumbnail('home-page-banner-thumb'); ?>
				<?php elseif ( ot_get_option('placeholder') != 'off' ): ?>
					<img src="<?php echo get_template_directory_uri(); ?>/img/thumb-medium.png" alt="<?php the_title(); ?>" />
				<?php endif; ?>
				<?php if ( has_post_format('video') && !is_sticky() ) echo'<span class="thumb-icon"><i class="fa fa-play"></i></span>'; ?>
				<?php if ( has_post_format('audio') && !is_sticky() ) echo'<span class="thumb-icon"><i class="fa fa-volume-up"></i></span>'; ?>
				<?php if ( is_sticky() ) echo'<span class="thumb-icon"><i class="fa fa-star"></i></span>'; ?>
			</a>
			<?php if ( comments_open() && ( ot_get_option( 'comment-count' ) != 'off' ) ): ?>
				<a class="post-comments" href="<?php comments_link(); ?>"><span><i class="fa fa-comments-o"></i><?php comments_number( '0', '1', '%' ); ?></span></a>
			<?php endif; ?>
		</div><!--/.post-thumbnail-->
		<div class="overlay-text-container">
		<div class="post-meta group">
			<p class="post-category"><?php the_category(' / '); ?></p>
			<p class="post-date"><?php the_time('j M, Y'); ?></p>
		</div><!--/.post-meta-->
		
		<h2 class="post-title">
			<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a>
		</h2><!--/.post-title-->

		<div class="post-byline">
			<?php the_credits('byline'); ?>
		</div><!--/.post-byline-->

		<?php if (false /*ot_get_option('excerpt-length') != '0'*/): ?>
		<div class="entry excerpt">
			<?php echo trim_excerpt_to(get_the_excerpt(), 20); ?>
		</div><!--/.entry-->
		<?php endif; ?>
		</div>
		
	</div><!--/.post-inner-->	
</article><!--/.post-->
