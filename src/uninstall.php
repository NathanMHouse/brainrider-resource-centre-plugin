<?php

// Check to make sure that is actual uninstall, else exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) :
    exit ();
endif;

// Remove database tables 
delete_option( 'br_rc_settings_group' );