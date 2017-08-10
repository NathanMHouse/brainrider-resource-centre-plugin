<?php
/**
 * Content excerpt template part for br resource centre plugin. Included by archive.
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

<div class="row">
	<div class="br-rc-content-excerpt-container col-xs-12 
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

		// The featured image
		if ( $excerpt_image ):
		?>
			<div class="br-rc-content-excerpt-image 
				<?php echo ( $featured_class ) 
						   ? 'col-sm-12' 
						   : 'col-sm-4'; ?>"
			     style="background: url('<?php echo $excerpt_image; ?>')
				 		no-repeat center/cover;">
			</div><!-- .br-rc-content-excerpt-image -->
		<?php 
		endif;
		?>

		<div class="br-rc-content-excerpt-content 
			<?php echo ( $excerpt_image && !$featured_class ) 
					   ? 'col-sm-8' 
					   : 'col-sm-12'; ?>">

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
			// The resource type callout
			// Form styled as icon link to submit $_POST data
			?>		
			<form class="br-rc-content-excerpt-type-callout" method="post" 
				action="<?php echo get_post_type_archive_link( 'br_rc_resource' ) ?>">
				<input type="hidden" name="br_rc_format" 
					value="<?php echo br_rc_callout_output( $post, 'slug' ); ?>" />
				<button class="fa <?php echo br_rc_callout_output( $post, 'icon' ); ?>">
				</button>
			</form><!-- .br-rc-content-excerpt-type-callout -->

			<?php
			// The category links
			if ( br_rc_custom_taxonomies_terms_links( $post, 'br_rc_category' ) ):
				$categories_output  = '<div class="br-rc-content-excerpt-category-links">';
				$categories_output .= '<i class="fa fa-folder"></i>';
				$categories_output .= br_rc_custom_taxonomies_terms_links( $post, 'br_rc_category' );
				$categories_output .= '</div>';
				echo $categories_output;
			endif;

			// The post excerpt
			if ( $excerpt_image ) {
				br_rc_custom_excerpt( 160 ); // with image
			} else {
				br_rc_custom_excerpt( 240 ); // without image
			}

			// The CTA
			?>
			<a class="br-rc-content-excerpt-cta" href="<?php the_permalink(); ?>">
				<?php _e( 'Read More', 'brainrider-resource-centre' ); ?>
				<i class="fa fa-chevron-right"></i>
			</a><!-- .br-rc-content-excerpt-cta -->

		</div><!-- .br-rc-content-excerpt-content -->
	</div><!-- .br-rc-content-excerpt-container -->
</div><!-- .row -->