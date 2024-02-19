<div class="mpf-p-popup-holder">
	<?php do_action('mpfy_popup_before_section', get_the_ID(), $map_id); ?>

	<div class="mpfy-p-popup-background"></div>
	<section class="<?php echo implode(' ', $classes); ?>">
		<div class="mpfy-p-holder mpfy-p-color-popup-background">

			<div class="mpfy-p-top mpfy-p-color-header-background">
				<p class="mpfy-p-date mpfy-p-color-header-date-background"><span><?php the_time('M'); ?></span><?php the_time('j'); ?></p>
				<h1 class="mpfy-p-color-blue mpfy-p-color-accent-color"><?php the_title(); ?></h1>
				<ul class="mpfy-p-nav-info">
					<li class="mpfy-p-name"><?php the_author(); ?></li>
					<li class="mpfy-p-category"><?php the_category(', '); ?></li>
					<li class="mpfy-p-comment-number"><a href="<?php the_permalink(); ?>"><?php comments_number('0 comments', '1 comment', '% comments'); ?></a></li>
				</ul>
				<a href="#" class="mpfy-p-close"></a>
			</div>

			<div class="mpfy-p-bottom">
				<?php do_action('mpfy_popup_before_content_layout', get_the_ID()); ?>

				<div class="mpfy-p-sidebar">
					<?php do_action('mpfy_popup_location_information', get_the_ID(), $map_id); ?>
					<div class="cl">&nbsp;</div>
					<div id="mpfy-p-sidebar-top" class="mpfy-p-sidebar">
						<div class="cl">&nbsp;</div>
						<?php get_sidebar(); ?>
						<div class="cl">&nbsp;</div>
					</div>
					<div class="cl">&nbsp;</div>
				</div>

				<div class="mpfy-p-content">
					<div class="mpfy-p-holder">
						<div class="mpfy-p-entry">
							<?php do_action('mpfy_popup_content_before', get_the_ID()); ?>
							<?php the_content(); ?>
							<?php do_action('mpfy_popup_content_after', get_the_ID()); ?>
						</div>
					</div>
				</div>
				<div class="cl">&nbsp;</div>
				<div id="mpfy-p-sidebar-bottom" class="mpfy-p-sidebar"></div>
				<div class="cl">&nbsp;</div>

				<div class="mpfy-p-comments">
					<?php
					define('MPFY_COMMENT_REDIRECT_URL', add_query_arg('mpfy-pin', get_the_ID(), $_SERVER['HTTP_REFERER']));
					add_filter( 'comments_template', 'mpfy_comments_template' );
					add_filter( 'comment_form', 'mpfy_comments_redirect_field' );
					comments_template();
					remove_filter( 'comments_template', 'mpfy_comments_template' );
					remove_filter( 'comment_form', 'mpfy_comments_redirect_field' );
					?>
				</div>
			</div>		
		</div>
	</section>	
</div>