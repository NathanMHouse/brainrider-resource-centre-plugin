<?php
/**
 * Banner archive template part for br resource center plugin. Included by archive.
 * 
 *
 */

// Vars 
$options 			= get_option( 'br_rc_settings_group' );
$banner_title 		= $options['br_rc_banner_title'];
$banner_description = $options['br_rc_banner_description'];
$banner_image_url 	= $options['br_rc_banner_image_url'];
?>

<div class="br-rc-archive-banner" 
	 style="background-image: url('<?php echo esc_url( $banner_image_url ); ?>');">
	<div class="br-rc-archive-banner-overlay">
		<div class="container">
			<div class="col-md-8 col-md-offset-2">

				<?php 
				// The main archive
				if ( is_post_type_archive( 'br_rc_resource' ) ) :

					// The banner title (main)
					if ( !empty( $banner_title ) ):
					?>
						<h1><?php echo esc_html( $banner_title ); ?></h1>
					<?php
					endif;
					
					// The banner description
					if ( !empty( $banner_description ) ):
					?>
						<h3><?php echo esc_html( $banner_description ); ?></h3>
					<?php 
					endif;
					
				// The taxonomy archive (i.e. category)
				else:
					$taxononmy_object = get_queried_object();

					// The banner title (category)
					?>
					<h1><?php echo ucfirst( $taxononmy_object->name ); ?></h1>

					<?php 
					// The banner description (category)
					if ( !empty( $taxononmy_object->description ) ):
					?>
						<h3><?php echo $taxononmy_object->description; ?></h3>
				<?php
					endif;
				endif;
					?>

			</div><!-- .col -->
		</div><!-- .container -->
	</div><!-- .br-rc-archive-banner-overlay -->
</div><!-- .br-rc-archive-banner -->