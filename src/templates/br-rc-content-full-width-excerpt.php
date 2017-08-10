<?php
/**
 * Content excerpt full-width template part for br resource centre plugin. Included by archive.
 * 
 *
 */

// Vars
$excerpt_image 	= get_the_post_thumbnail_url();
$featured_class = ( get_post_meta( $post->ID, '_br_rc_featured_resource_toggle', true ) == '1'
					&& $wp_query->current_post == 0 
					&& get_query_var('paged') == 1 )
					? true : false;
?>


<div class="br-rc-content-excerpt-container br-rc-content-excerpt-fw-container col-md-3 col-sm-6
	<?php echo ( $featured_class ) 
			   ? 'br-rc-content-excerpt-container-featured' 
			   : ''; ?>">

	<?php
	// The featured callout
	if ( $featured_class ):
	?>
		<div class="br-rc-content-excerpt-featured-callout col-sm-12">
			<h4>
				<i class="fa fa-star"></i>
				<?php _e( 'Featured Resource', 
						  'brainrider-resource-centre' ); ?>
			</h4>
		</div><!-- .br-rc-content-excerpt-featured-callout -->

	<?php 
	endif;
	?>

	<div class="br-rc-content-excerpt-content br-rc-content-excerpt-fw-content"

		<?php 
		// The featured image
		if ( $excerpt_image ): ?>
		 style="background-image: url('<?php echo $excerpt_image; ?>')"
		<?php endif ?>
	>

		<?php
		// The resource type callout
		// Form styled as icon link to submit $_POST data
		// Only add if not a featured resource
		if ( !$featured_class ):
		?>		
			<form class="br-rc-content-excerpt-type-callout" method="post" 
				  action="<?php echo get_post_type_archive_link( 'br_rc_resource' ) ?>">
				<input type="hidden" name="br_rc_format" 
					   value="<?php echo br_rc_callout_output( $post, 'slug' ); ?>" />
					<button class="fa <?php echo br_rc_callout_output( $post, 'icon' ); ?>">
					</button>
			</form><!-- .br-rc-content-excerpt-type-callout -->
		<?php 
		endif;
		?>

		<div class="br-rc-content-excerpt-fw-content-overlay">
			<?php
			// The excerpt title
			?>
			<h2>
				<a href="<?php the_permalink(); ?>" 
					title="<?php the_title_attribute(); ?>">
					<?php echo br_rc_string_to_title( get_the_title() ); ?>
			   </a>
			</h2>

			<?php
			// The post excerpt
			br_rc_custom_excerpt( 80 ); 

			// The CTA
			?>
			<a class="br-rc-content-excerpt-cta br-rc-content-excerpt-fw-cta" href="<?php the_permalink(); ?>">
				<?php _e( 'Read More', 'brainrider-resource-centre' ); ?>
				<i class="fa fa-chevron-right"></i>
			</a><!-- .br-rc-content-excerpt-cta -->

		</div><!-- .br-rc-content-excerpt-fw-content-overlay -->

	</div><!-- .br-rc-content-excerpt-content -->
</div><!-- .br-rc-content-excerpt-container -->
