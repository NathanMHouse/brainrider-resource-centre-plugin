<?php
/**
 * Default archive template for br resource center plugin.
 * 
 *
 */

// Vars 
$options             = get_option( 'br_rc_settings_group' );
$banner_image_toggle = $options['br_rc_banner_toggle'];
$filter_type         = $options['br_rc_filter_type'];

// The header
get_header();

// The banner
if ( $banner_image_toggle == '1' ):
    include br_rc_archive_banner_template();
endif;

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

// The horizontal filter controls (desktop and mobile)
if ( $filter_type == 'horizontal' ):
    echo br_rc_post_filter( 'horizontal' );
endif;

// The main content
?>
<div class="page-main" id="br-rc-archive">

    <div class="container">
        <main id="content" role="main">
            <div class="row">

                <?php 
                // The posts
                ?>
                <div class="col-md-8">

                    <?php 
                    // The vertical filter controls (desktop)
                    if ( $filter_type == 'vertical' ):
                    ?>
                        <div class="mobile-filter hidden-md-up">
                            <?php
                                echo br_rc_post_filter( 'vertical' );
                            ?>
                        </div><!-- .mobile-filter -->
                    <?php
                    endif;
                    ?>

                    <?php 
                    global $wp_query;

                    if ( $wp_query->have_posts() ): 
                        while ( $wp_query->have_posts() ): $wp_query->the_post();
                            include br_rc_content_excerpt_template();
                        endwhile;
                     else:
                        include br_rc_content_none_template();
                    endif;  

                    wp_reset_postdata();  
                    ?>

                    <?php
                    // Output BR RC pagination if it exists
                    if ( function_exists( 'br_rc_pagination' ) ):
                        echo br_rc_pagination();

                        // Else, output WordPress pagination as a fallback
                    else:
                        the_posts_pagination();
                    endif;
                    ?>

                </div><!-- .col -->

                 <div class="col-md-4">
                    <?php 
                    // The vertical filter controls (desktop)
                    if ( $filter_type == 'vertical' ):
                    ?>
                        <div class="desktop-filter hidden-sm-down">
                            <?php
                            echo br_rc_post_filter( 'vertical' );
                            ?>
                        </div><!-- .desktop-filter -->
                    <?php
                    endif;
                    ?>

                    <?php
                    // The sidebar content
                    get_sidebar();
                    ?>
                </div><!-- .col -->

            </div><!-- .row -->
        </main><!-- #content -->

    </div><!-- .container -->
</div><!-- #archive -->

<?php 
// The footer
get_footer();