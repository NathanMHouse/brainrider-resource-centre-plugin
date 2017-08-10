<?php
/**
 * Related posts template part for br resource centre plugin. Included by single.
 * 
 *
 */

// Vars (current post)
$post_id_array 				= array();
$post_id_array[] 			= get_the_ID();
$category_id_array 			= wp_get_post_categories($post->ID, 'ids');
$options 					= get_option( 'br_rc_settings_group' );
$related_posts_title 		= $options['br_rc_related_posts_title'];
$related_posts_description 	= $options['br_rc_related_posts_description'];
?>

<div class="br-rc-related-posts">
	
	<?php 
	// The section title
	if ( !empty( $related_posts_title ) ):
	?>
		<h2><?php echo esc_html( $related_posts_title ); ?></h2>

	<?php
	endif;
	// The section description
	if ( !empty($related_posts_description) ):
	?>
		<h4><?php echo esc_html( $related_posts_description ); ?></h4>

	<?php
	endif;

	// The arguments
	$args = array(
		'post_type'				 => 'br_rc_resource',
		'post_status'            => array( 'publish' ),
		'posts_per_page'         => '3',
		'post__not_in'			 => $post_id_array,
		'orderby'                => 'rand',
		'category__in'			 => $category_id_array,
	);

	// Filter the args
	$args = apply_filters( 'br_rc_related_posts_args_filter', $args );

	// The Query
	$query = new WP_Query( $args );

	// The Loop
	if ( $query->have_posts() ):
		while ( $query->have_posts() ):
			$query->the_post();
			
			// Vars (related post)
			$excerpt_image = get_the_post_thumbnail_url();
	?>
		<div class="br-rc-related-posts-post">

		<?php 
		// The excerpt image
		if ( $excerpt_image ):
		?>
			<div class="br-rc-related-posts-post-image col-md-4" 
				style="background: url('<?php echo $excerpt_image; ?>') 
					   no-repeat center/cover;">
			</div><!-- .br-rc-related-posts-post-image -->
		<?php 
		endif;

		// The excerpt content
		?>
		<div class="br-rc-related-posts-post-content 
			<?php echo ( $excerpt_image ) 
			           ? 'col-md-8' 
			           : 'col-md-12'; ?>">
			
			<?php 
			// The excerpt title
			?>
			<h3>
				<a href="<?php the_permalink(); ?>" 
				   title="<?php the_title_attribute(); ?>">
				   <?php echo br_rc_string_to_title( get_the_title() ); ?>
			   	</a>
			</h3>

			<?php
			// The excerpt type callout
			// Form styled as icon link to submit $_POST data
			?>		
			<form class="br-rc-related-posts-type-callout" method="post" 
				  action="<?php echo get_post_type_archive_link( 'br_rc_resource' ) ?>">
				<input type="hidden" name="br_rc_format" 
					   value="<?php echo br_rc_callout_output( $post, 'slug' ); ?>" />
				<button class="fa <?php echo br_rc_callout_output( $post, 'icon' ); ?>">
				</button>
			</form><!-- .br-rc-related-posts-type-callout -->

			<?php
			// The excerpt
			if ( $excerpt_image ):
				br_rc_custom_excerpt( 180 ); // with image
			else:
				br_rc_custom_excerpt( 240 ); // without image
			endif;

			// The excerpt CTA
			?>
			<a class="br-rc-related-posts-post-cta" href="<?php the_permalink(); ?>">
				<?php _e( 'Read More', 'brainrider-resource-centre' ); ?>
				<i class="fa fa-chevron-right"></i>
			</a><!-- .br-rc-related-posts-post-cta -->

		</div><!-- .br-rc-related-posts-post-content -->

	</div><!-- .br-rc-related-posts-post -->

	<?php endwhile;
	else:

		// No posts found
		?>
		<h4>
			<?php _e( 'No Related Resources Found', 
			          'brainrider-resource-centre' ); ?>
		</h4>
		<?php
	endif;

	// Restore original post data
	wp_reset_postdata();

	?>
	
</div><!-- .br-rc-related-posts -->