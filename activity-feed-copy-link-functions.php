<?php
/**
 * BuddyPress Activity Feed Copy Link
 *
 * Plugin Name:       Activity Feed Copy Link For BuddyPress
 * Plugin URI:        https://github.com/HemantTheCodingDev/activity-feed-copy-link-for-buddypress
 * Description:       This plugin adds a "Copy Link" button to each activity feed in BuddyPress.
 * Version:           1.0.0
 * Author:            Hemant Jha
 * Author URI:        https://hemantjha2.website3.me/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       activity-feed-copy-link-for-buddypress
 * Domain Path:       /languages
 *
 * @package           Activity-feed-copy-link-for-buddypress
 * @link              #
 * @since             1.0.0
 */

define( 'BP_ACTIVITY_FEED_COPY_LINK_URL', plugin_dir_url( __FILE__ ) );
define( 'BP_ACTIVITY_FEED_COPY_LINK_PATH', plugin_dir_path( __FILE__ ) );

// Enqueue FontAwesome for the icon
add_action('wp_enqueue_scripts', 'enqueue_fontawesome_local');
function enqueue_fontawesome_local() {
    wp_enqueue_style('fontawesome', BP_ACTIVITY_FEED_COPY_LINK_URL . 'assets/css/all.min.css', array(), '5.15.3');
}

/** Bp_activity_feed_copy_link_enqueue_scripts */
function bp_activity_feed_copy_link_enqueue_scripts() {
	wp_enqueue_style( 'bp-activity-feed-copy-link-css', BP_ACTIVITY_FEED_COPY_LINK_URL . 'assets/css/bp-activity-feed-copy-link.css', array(), '1.0.0', 'all' );
}
add_action( 'wp_enqueue_scripts', 'bp_activity_feed_copy_link_enqueue_scripts' );

// Add copy functionality to each activity feed
function bp_share_activity_filter_new() {
    $activity_link_copy = bp_get_activity_thread_permalink();
    $nonce = wp_create_nonce('copy_link_nonce'); // Generate a nonce
    ?>
    <div class="generic-button">
        <!-- Copy Link Button -->
        <a id="copy-link-button" class="button-copylink far fa-copy bp-primary-action" aria-expanded="false" href="<?php echo esc_url( add_query_arg( '_wpnonce', $nonce, $activity_link_copy ) ); ?>" role="button" onClick='copyText(event)'>
            <span class="bp-screen-reader-text">Copy Link</span>
            <span class="comment-count">Copy Link</span>
        </a>
    </div>
    <script>
        function copyText(event) {
            event.preventDefault(); // Prevent the default action of the anchor tag

            var copyText = "<?php echo esc_js($activity_link_copy); ?>"; // Ensure the URL is properly escaped for JavaScript

            // Create a temporary input to hold the text to copy
            var tempInput = document.createElement("input");
            tempInput.value = copyText;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand("copy");
            document.body.removeChild(tempInput);

            alert('Link copied');
        }
    </script>
    <?php
}
add_action( 'bp_activity_entry_meta', 'bp_share_activity_filter_new' );
?>
