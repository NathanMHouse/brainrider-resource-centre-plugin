<?php
/**
 * Default single post template for br resource centre plugin.
 * 
 *
 */

// Vars
$pardot_form_array 		 = json_decode( get_post_meta( $post->ID, '_br_rc_pardot_form_url', true ), true );

$pardot_form_parameters	 = ( get_post_meta( $post->ID, '_br_rc_pardot_form_parameters', true )
						   && substr( get_post_meta( $post->ID, '_br_rc_pardot_form_parameters', true ), 0, 1 ) != '?')
						   ? '?' . get_post_meta( $post->ID, '_br_rc_pardot_form_parameters', true )
						   : get_post_meta( $post->ID, '_br_rc_pardot_form_parameters', true );


$pardot_form_url 		 = $pardot_form_array['url'] . $pardot_form_parameters;
$transcript_toggle 		 = ( get_post_meta( $post->ID, '_br_rc_transcript_toggle', true ) == '1' 
							 && get_post_meta( $post->ID, '_br_rc_transcript_text', true ) != '')
						   ? true 
						   : false; 

// The header
get_header();

// The breadcrumbs	
if ( function_exists( 'bcn_display' ) ):
?>
    <div class="br-rc-breadcrumbs">
        <div class="container">
            <?php
                bcn_display();
            ?>
        </div><!-- .container -->
    </div><!-- .br-rc-breadcrumbs -->
<?php 
endif;

// The banner
include br_rc_single_banner_template();
?>

<div class="page-main" id="br-rc-single">
	<div class="container">
		<div class="row">

			<?php 
			// The main content
			?>
			<main class="col-md-8" id="content" role="main">

				<?php 
				// The pardot iframe (mobile)
				if ( $pardot_form_url ): 
				?>
					<div class="sidebar">
						<div class="pardot-form hidden-md-up">
							<iframe src="<?php echo esc_attr( $pardot_form_url ); ?>" 
								width="100%" 
								frameborder="0">
							</iframe>
						</div><!-- .pardot-form -->
					</div><!-- .sidebar -->

				<?php 
				endif;
				
				// The post
				while ( have_posts() ): the_post();
					include br_rc_single_content_template();
				endwhile; 

				// The resource transcript
				if ( $transcript_toggle ):
					include br_rc_single_transcript_template();
				endif;

				// The related resources
				include br_rc_single_related_posts_template();
				?>

			</main><!-- .col -->

			<?php 
			// The sidebar
			?>
			<aside class="sidebar col-md-4">
				
				<?php 
				// The pardot iframe (desktop)
				if ( $pardot_form_url ): 
				?>
					<div class="pardot-form hidden-sm-down">
						<iframe src="<?php echo esc_attr( $pardot_form_url ); ?>" 
								width="100%" 
								frameborder="0">
						</iframe>
					</div><!-- .pardot-form -->
				<?php
				endif;
				
				// The sidebar content
				get_sidebar();
			 	?>
			</aside><!-- .sidebar -->
			
		</div><!-- .row -->
	</div><!-- .container -->
</div><!-- .page-main -->

<?php
// The footer
get_footer();