<?php
/**
 * Banner single template part for br resource centre plugin. Included by single.
 * 
 *
 */

// Vars
$excerpt_image = get_the_post_thumbnail_url();
?>

<div class="br-rc-single-banner">
	<div class="container">
		<div class="row">
			<div class="br-rc-single-banner-container">

				<?php 
				// The banner image
				if ( $excerpt_image ):
				?>
					<div class="br-rc-single-banner-image col-md-4 col-md-push-8" 
						style="background: url('<?php echo $excerpt_image; ?>') 
							no-repeat center/cover;">
					</div><!-- .br-rc-single-banner-image -->
				<?php 
				endif;
				?>
				
				<?php 
				// The banner content
				?>
				<div class="br-rc-single-banner-content 
					<?php echo ( $excerpt_image ) 
							   ? 'col-md-8 col-md-pull-4' 
							   : 'col-md-12'; ?>">

					<?php
					// The resource type callout
					// Form styled as icon link to submit $_POST data
					?>		
					<form class="br-rc-single-banner-type-callout" method="post"
						action="<?php echo get_post_type_archive_link( 'br_rc_resource' ) ?>">
						<input type="hidden" name="br_rc_format" 
							value="<?php echo br_rc_callout_output( $post, 'slug' ); ?>" />
						<button class="fa <?php echo br_rc_callout_output( $post, 'icon' ); ?>">
							<span><?php echo br_rc_callout_output( $post, 'label' ); ?></span>
						</button>
					</form><!-- .br-rc-single-banner-type-callout -->

					<?php
					// The title
					the_title( '<h1>', '</h1>' );

					// The category links
					if ( br_rc_custom_taxonomies_terms_links( $post, 'br_rc_category' ) ):
						$categories_output  = '<div class="br-rc-single-banner-category-links">';
						$categories_output .= '<i class="fa fa-folder"></i>';
						$categories_output .=  br_rc_custom_taxonomies_terms_links( $post, 'br_rc_category' );
						$categories_output .= '</div>';
						echo $categories_output;
					endif;

					// The branding
					?>

					<div class="br-rc-single-banner-branding">
						<a href="http://www.brainrider.com">
							<img src="<?php echo plugins_url( 'brainrider-resource-centre/assets/images/br-rc-branding-logo.png' ); ?>" 
								alt="<?php _e( 'Brainrider logo.', 
											   'brainrider-resource-centre' ); ?>">
						</a>
					</div><!-- .br-rc-single-banner-branding -->

				</div><!-- .br-rc-single-banner-content -->

				<?php 
				// The social sharing
				?>
					<div class="br-rc-single-banner-social 
						<?php 
						if ( ! shortcode_exists( 'addtoany' ) ) :
							echo 'hidden-md-up'; 
						endif; ?>">
						<?php 
						if ( shortcode_exists( 'addtoany' ) ):
							$social_links_output  = '<span>';
							$social_links_output .= __( 'Share:', 'brainrider-resource-centre' );
							$social_links_output .= do_shortcode( '[addtoany]' ); 
							echo $social_links_output;
						endif;
						?>
					</div><!-- .br-rc-single-banner-social -->

			</div><!-- .br-rc-single-banner-container -->
		</div><!-- .row -->
	</div><!-- .container -->
</div><!-- .br-rc-single-banner -->