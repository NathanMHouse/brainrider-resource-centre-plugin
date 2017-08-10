<?php
/**
 * Content none template part for br resource centre plugin. Included by archive.
 * 
 *
 */
?>

<div class="br-rc-no-results">

	<?php
	// The filter terms
	?>
	<div class="br-rc-searched-terms">
		<?php 
		echo br_rc_no_results_terms(); 
		?>
	</div><!-- .br-rc-searched-terms -->

	<?php
	// The recent resources
	?>
	<div class="br-rc-recent-resources">
		<h2>
			<?php _e( 'Recent Resources', 'brainrider-resource-centre' ); ?>
		</h2>

		<?php 
		$args = array(
		'post_type'              => array( 'br_rc_resource' ),
		'posts_per_page'         => '3',
		);

		$query = new WP_Query( $args );

		if ( $query->have_posts() ):

			while ( $query->have_posts() ):

				$query->the_post();

				// The excerpts
				include br_rc_content_excerpt_template();

			endwhile;

		endif;

		// Restore original post data
		wp_reset_postdata();
		?>
	</div><!-- .br-rc-recent-resources -->

	<?php 
	// The site search
	?>
	<div class="br-rc-site-search">
		<h2>
			<?php _e( 'Site-wide Search', 'brainrider-resource-centre' ); ?>
		</h2>
		<h3>
			<?php _e( 'Still not what you\'re looking for? Try a site-wide search.',
					  'brainrider-resource-centre' ); ?>
		</h3>
		<form role="search" method="get" class="search-form" action="/">
			<input type="search" class="search-field-input" value="" name="s" 
				title="Search">
			<input type="submit" class="search-field-submit" 
				value="<?php esc_attr_e( 'Search', 
										 'brainrider-resource-centre' ); ?>">
		</form>
	</div><!-- .br-rc-site-search -->

</div><!-- .br-rc-no-results -->
