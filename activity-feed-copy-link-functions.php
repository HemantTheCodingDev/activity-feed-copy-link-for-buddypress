<?php
// Prevent direct access to this file
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * BuddyPress Activity Feed Copy Link
 *
 * Plugin Name:       Activity Feed Copy Link For BuddyPress
 * Plugin URI:        https://github.com/HemantTheCodingDev/activity-feed-copy-link-for-buddypress
 * Description:       This plugin adds a "Copy Link" button to each activity feed in BuddyPress.
 * Version:           1.0.1
 * Author:            Hemant Jha
 * Author URI:        https://hemantjha2.website3.me/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       activity-feed-copy-link-for-buddypress
 * Domain Path:       /languages
 *
 * @package           Activity-feed-copy-link-for-buddypress
 * @link              #
 * @since             1.0.1
 */

define( 'AFCL_ACTIVITY_FEED_COPY_LINK_URL', plugin_dir_url( __FILE__ ) );
define( 'AFCL_ACTIVITY_FEED_COPY_LINK_PATH', plugin_dir_path( __FILE__ ) );

// Enqueue styles and scripts
add_action( 'wp_enqueue_scripts', 'afcl_activity_feed_copy_link_enqueue_assets' );
function afcl_activity_feed_copy_link_enqueue_assets() {
    // Enqueue FontAwesome
    wp_enqueue_style( 'fontawesome', AFCL_ACTIVITY_FEED_COPY_LINK_URL . 'assets/css/font-awesome.min.css', array(), '4.7.0' );

    // Enqueue Plugin CSS
    wp_enqueue_style( 'afcl-activity-feed-copy-link-css', AFCL_ACTIVITY_FEED_COPY_LINK_URL . 'assets/css/afcl-activity-feed-copy-link.css', array(), '1.0.0', 'all' );

    // Enqueue Plugin JavaScript
    wp_enqueue_script( 'afcl-copy-link-script', AFCL_ACTIVITY_FEED_COPY_LINK_URL . 'assets/js/copy-link.js', array(), '1.0.0', true );

    // Localize script to pass the AJAX URL and nonce to JS
    wp_localize_script( 'afcl-copy-link-script', 'afcl_nonce_data', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'afcl_copy_link_nonce' ),
    ));
}

// Add copy link button to each activity feed
add_action( 'bp_activity_entry_meta', 'afcl_add_copy_link_button' );
function afcl_add_copy_link_button() {
    // Get activity link
    $activity_link = bp_get_activity_thread_permalink();

    // Create a nonce for verification
    if ( is_user_logged_in() ) {
        $nonce = wp_create_nonce( 'afcl_copy_link_nonce' );  // Nonce for logged-in users
    } else {
        $nonce = wp_create_nonce( 'afcl_copy_link_nonce_guest' );  // Nonce for guests
    }

    ?>
    <div class="generic-button">
        <button class="button-copylink afcl-primary-action" data-link="<?php echo esc_url( $activity_link ); ?>" data-nonce="<?php echo esc_attr( $nonce ); ?>" aria-expanded="false" tabindex="0">
            <i class="fa fa-copy"></i><span class="afcl-screen-reader-text"><?php esc_html_e( 'Copy Link', 'activity-feed-copy-link-for-buddypress' ); ?></span>
        </button>
    </div>
    <?php
}

// Validate nonce on the server side
add_action( 'wp_ajax_afcl_validate_nonce', 'afcl_validate_nonce_callback' );
add_action( 'wp_ajax_nopriv_afcl_validate_nonce', 'afcl_validate_nonce_callback' ); // For non-logged-in users

function afcl_validate_nonce_callback() {
    // Check if nonce is set in $_POST
    if ( isset( $_POST['nonce'] ) ) {
        // Unsling the nonce before using it and sanitize
        $nonce = sanitize_text_field( wp_unslash( $_POST['nonce'] ) ); // Unslash and sanitize the nonce

        // Validate nonce for logged-in users
        if ( is_user_logged_in() ) {
            if ( ! wp_verify_nonce( $nonce, 'afcl_copy_link_nonce' ) ) {
                wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
            }
        } else {
            // Validate nonce for non-logged-in users
            if ( ! wp_verify_nonce( $nonce, 'afcl_copy_link_nonce_guest' ) ) {
                wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
            }
        }

        wp_send_json_success(); // Nonce is valid
    }

    wp_send_json_error( array( 'message' => 'No nonce found' ) ); // Return error if no nonce is found
}
