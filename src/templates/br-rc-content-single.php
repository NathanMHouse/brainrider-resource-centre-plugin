<?php
/**
 * Content single template part for br resource centre plugin. Included by br rc single.
 * 
 *
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
	<div class="post-content">
		<?php the_content(); ?>
	</div><!-- post-content -->
	
</article><!-- #post-## -->
<?php

	// Set action to execute after single content loaded
	do_action( 'br_rc_after_content_single')
?>