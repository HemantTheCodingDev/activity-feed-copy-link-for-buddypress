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
add_action('wp_enqueue_scripts', 'enqueue_fontawesome');
function enqueue_fontawesome() {
    wp_enqueue_style('fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css');
}

/** Bp_activity_feed_copy_link_enqueue_scripts */
function bp_activity_feed_copy_link_enqueue_scripts() {
	wp_enqueue_style( 'bp-activity-feed-copy-link-css', BP_ACTIVITY_LINK_PREVIEW_URL . 'assets/css/bp-activity-feed-copy-link.css', array(), '1.0.0', 'all' );
}
add_action( 'wp_enqueue_scripts', 'bp_activity_feed_copy_link_enqueue_scripts' );

// Add copy functionality to each activity feed
function bp_share_activity_filter_new() { ?>
    <div class="generic-button">
		<?php $activity_link_copy = bp_get_activity_thread_permalink(); ?>
		<i class="far fa-copy" style="cursor: pointer;" onClick='copyText(this)'><span style="position: absolute;left: -9999px;"><?php echo $activity_link_copy; ?></span><span class="copy_activity_link"> Copy Link</span></i>
	</div>
	<script>
		function copyText(element) {
			var range, selection, worked;

			if (document.body.createTextRange) {
				range = document.body.createTextRange();
				range.moveToElementText(element);
				range.select();
			} else if (window.getSelection) {
				selection = window.getSelection();
				range = document.createRange();
				range.selectNodeContents(element);
				selection.removeAllRanges();
				selection.addRange(range);
			}

			try {
				document.execCommand('copy');
				alert('Link copied');
			}
			catch (err) {
				alert('unable to copy link');
			}
		}
	</script>
	<?php
}
add_action( 'bp_activity_entry_meta', 'bp_share_activity_filter_new' );

?>
