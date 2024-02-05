<?php
/**
 * Template Name: I <3 Maker Faire
 */
get_header(); ?>

<div class="clear"></div>

<div class="container">

	<div class="row">

		<div class="content col-md-8">

			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

				<article <?php post_class(); ?>>

					<script type="text/javascript" charset="utf-8" async defer>
						jQuery('body').ready(function( $ ){
							var love = $.cookie('love_maker_faire');
							console.log( love );
							if ( love ) {
								console.log('Loving it...');
								$('.page-content').show();
								$('#newsletter-hearts').hide();
							} else {
								$('#newsletter-hearts').show();
							};
							$('#newsletter-hearts').submit( function( e ) {
								e.preventDefault();
								console.log( 'Clicked...' );
								$.getJSON(
									this.action + "?callback=?",
									$(this).serialize(),
									function( data ) {
										if ( data.Status === 400 ) {
											console.log( "Error: " + data.Message );
										} else { // 200
											console.log( "Success: " + data.Message );
											$.cookie( 'love_maker_faire', true, { expires: 365, path: '/' } );
											$('.page-content').show();
											$('#newsletter-hearts').hide();
										}
									}
								);
							});
						});
					</script>

					<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>

					<form id="newsletter-hearts" class="form-horizontal hide" action="http://newsletter.makezine.com/t/r/s/thqyuj/" method="post">
						<fieldset>
							<div class="control-group">
								<label class="control-label" for="cm-name">Name</label>
								<div class="controls">
									<input id="fieldName" name="cm-name" type="text" />
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="fieldEmail">Email</label>
								<div class="controls">
									<input id="fieldEmail" name="cm-thqyuj-thqyuj" type="email" required />
								</div>
							</div>
							<div class="control-group">
								<div class="controls">
									<button class="btn submit" type="submit">Subscribe</button>
								</div>
							</div>
						</fieldset>
					</form>


					<div class="page-content hide">
						<?php the_content(); ?>
					</div>

					<div class="clear"></div>

				</article>

			<?php endwhile; ?>

				<ul class="pager">

					<li class="previous"><?php previous_posts_link('&larr; Previous Page'); ?></li>
					<li class="next"><?php next_posts_link('Next Page &rarr;'); ?></li>

				</ul>

			<?php else: ?>

				<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>

			<?php endif; ?>

		</div><!--Content-->

		<?php get_sidebar(); ?>

	</div>

</div><!--Container-->

<?php get_footer(); ?>