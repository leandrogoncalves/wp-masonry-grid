<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/leandrogoncalves/wp-masonry-grid
 * @since      1.0.0
 *
 * @package    WP_Masonry_Grid
 * @subpackage WP_Masonry_Grid/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WP_Masonry_Grid
 * @subpackage WP_Masonry_Grid/public
 * @author     leandrogoncalves <contato.leandrogoncalves@gmail.com>
 */
class WP_Masonry_Grid_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;


	/**
	 * The plugin path
	 *
	 * @var String
	 */
	private $plugin_path;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->plugin_path = plugin_dir_url( __FILE__ );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WP_Masonry_Grid_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WP_Masonry_Grid_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, $this->plugin_path . 'css/wpmg-masonry-grid.css',
						  array(),
						  $this->version,
						  'all'
		);

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name.'-js' , $this->plugin_path . 'js/wp-masonry-grid-main.js',
						   array( 'jquery' ),
						   $this->version,
						   true
		);
		wp_enqueue_script( $this->plugin_name.'-salvattore' , $this->plugin_path . 'js/salvattore.min.js',
						   array( 'jquery' ),
						   $this->version,
						   true
		);
//		wp_enqueue_script( $this->plugin_name . 'isotope', $this->plugin_path . 'js/isotope.pkgd.min.js',
//						   array( 'jquery' ),
//						   $this->version, false
//		);
//		wp_enqueue_script( $this->plugin_name . 'imagesloaded', $this->plugin_path . 'js/imagesloaded.pkgd.min.js',
//						   array( 'jquery' ),
//						   $this->version, false
//		);

	}

}
