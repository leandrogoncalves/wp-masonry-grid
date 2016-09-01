<?php
/**
 * @link              https://github.com/leandrogoncalves/wp-masonry-grid
 * @since             1.0.0
 * @package           WP_Masonry_Grid
 *
 * @wordpress-plugin
 * Plugin Name:       Wanna Isotope
 * Plugin URI:        https://github.com/leandrogoncalves/wp-masonry-grid
 * Description:       A plugin to easily build Masonry grid Layouts with any content.
 * Version:           1.0.4
 * Author:            jjmrestituto, wannathemes
 * Author URI:        https://github.com/leandrogoncalves/wp-masonry-grid
 * License:           MIT
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-masonry-grid
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'src/WP_Masonry_Grid.php';
require plugin_dir_path( __FILE__ ) . 'public/WP_Masonry_Grid_Shortcode.php';


$plugin = new WP_Masonry_Grid();
$shortcode = new WP_Masonry_Grid_Shortcode();
$plugin->run();
