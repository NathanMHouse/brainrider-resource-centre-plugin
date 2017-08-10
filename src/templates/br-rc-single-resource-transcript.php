<?php
/**
 * Transcript template part for br resource centre plugin. Included by single where applicable.
 * 
 *
 */

// Vars
$br_rc_transcript_text = get_post_meta( $post->ID, '_br_rc_transcript_text', true );

// The transcript button
?>
<button id="br-rc-transcript-drawer-toggle" name="br-rc-transcript-drawer-toggle">
	<i class="fa fa-file-text-o"></i>
	<?php _e( 'Transcript', 'brainrider-resource-centre' ); ?>
	<i id="br-rc-transcript-drawer-icon" class="fa fa-angle-down"></i>
</button><!-- #br-rc-transcript-header -->

<?php 
// The transcript content
?>
<div id="br-rc-transcript-content">

	<?php 
	echo wpautop( $br_rc_transcript_text );

	?>

</div><!-- #br-rc-transcript-content -->