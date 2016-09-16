<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/leandrogoncalves/wp-masonry-grid
 * @since      1.0.0
 *
 * @package    WP_Masonry_Grid
 * @subpackage WP_Masonry_Grid/core
 */

if(!defined('ABSPATH')) die('Wordpress is required');

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WP_Masonry_Grid
 * @subpackage WP_Masonry_Grid/includes
 * @author     Leandro Goncalves <contato.Leandro Goncalves@gmail.com>
 */
abstract class WP_Masonry_Grid {


	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      WP_Masonry_Grid_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * array de variáveis
	 *
	 * @var array
	 */
	protected $vars = [];


	/**
	 * Option name
	 *
	 * @var string
	 */
	protected $_option_name = 'wpmg_options';

	/**
	 * Capability name
	 *
	 * @var string
	 */
	private $_capability = 'wpmg_manager_cap';

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	protected function __construct() {

		if (version_compare(PHP_VERSION, '5.5.0', '<')) {
			wp_die(__("This plugin require the PHP version 5.5.0 or later ", 'grp_plugin'));
		}
		
		
		$this->plugin_name = 'wp-masonry-grid';
		$this->version = '1.0.0';
		$this->site_url = get_site_url();
		$this->plugin_path = plugin_dir_path( dirname( __FILE__ ) );

		$this->load_dependencies();
		
		$this->loader = new WP_Masonry_Grid_Loader();
		$this->view = new WP_Masonry_Grid_View();

		$this->set_locale();
		$this->define_public_hooks();
//		$this->define_admin_hooks();

	}


	/**
	 * seta uma variavel passando o nome e o valor para o um vetor associativo
	 * @param string $name  nome da variável
	 * @param string $value valor
	 */
	private function set($name, $value='')
	{
		$this->vars[$name] = $value;
		return $this;
	}

	/**
	 * retorna o valor da variável chamada por $name
	 * @param  string $name nome da variavel
	 * @return mixed       	valor da variavel
	 */
	public function __get($name)
	{
		if(!isset($this->vars[$name])) $this->set($name);
		return  $this->vars[$name];
	}



	/**
	 * Get query args
	 * @return array|string
	 * @link http://php.net/manual/pt_BR/function.filter-input.php
	 */
	protected function getResults(array $filter = []){

		$query_hooks = new WP_Masonry_Grid_Query(
			$this->type,
			$this->order,
			$this->orderby,
			$this->posts_per_page,
			$this->paged,
			'publish'
		);

		$this->loader->add_filter('posts_where', $query_hooks, 'post_where', 10, 2)->run();

		$result  = $query_hooks->getResults($filter);

		return $result;
	}



	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - WP_Masonry_Grid_Loader. Orchestrates the hooks of the plugin.
	 * - WP_Masonry_Grid_i18n. Defines internationalization functionality.
	 * - WP_Masonry_Grid_Admin. Defines all hooks for the admin area.
	 * - WP_Masonry_Grid_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	protected function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once $this->plugin_path . 'core/WP_Masonry_Grid_Loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once $this->plugin_path  . 'core/WP_Masonry_Grid_i18n.php';

		/***
		 * Class reponsiable for wp query implements
		 */
		require_once $this->plugin_path  . 'core/WP_Masonry_Grid_Query.php';
		
		/***
		 * Class reponsiable for wp ajax implements
		 */
		require_once $this->plugin_path  . 'core/WP_Masonry_Grid_Ajax.php';

		/***
		 * Class reponsiable for load views implements
		 */
		require_once $this->plugin_path  . 'core/WP_Masonry_Grid_View.php';

		/***
		 * Class reponiable for query string POST or GET
		 */
		require_once $this->plugin_path  . 'frontend/WP_Masonry_Grid_Static.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once $this->plugin_path  . 'frontend/WP_Masonry_Grid_Public.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
//		require_once $this->plugin_path. 'backend/WP_Masonry_Grid_Admin.php';


	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WP_Masonry_Grid_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	protected function set_locale() {

		$this->plugin_i18n = new WP_Masonry_Grid_i18n();
		$this->plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $this->plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	protected function define_admin_hooks() {

		$this->plugin_admin = new WP_Masonry_Grid_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $this->plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this->plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	protected function define_public_hooks() {

		$this->plugin_public = new WP_Masonry_Grid_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $this->plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $this->plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_enqueue_scripts', $this->plugin_public, 'localizeAjaxScript' );

		$this->ajax_plugin = new WP_Masonry_Grid_Ajax();
		
		$this->loader->add_action( 'wp_ajax_nopriv_wpmg_ajax_pagination', $this->ajax_plugin , 'AjaxPagination' );
		$this->loader->add_action( 'wp_ajax_wpmg_ajax_pagination', $this->ajax_plugin , 'AjaxPagination' );

	}

	/**
	 * Create the options
	 */
	protected function _update_options(array $opt = [])
	{
		delete_option($this->_option_name);
		$options = get_option( $this->_option_name );


		if( !empty($options) ){
			$insert_opt = array(
				'type'         => (!empty($opt['post'])) ? $opt['post'] : $options['type'],
				'posts_per_page'  => (!empty($opt['posts_per_page'])) ? $opt['posts_per_page'] : $options['posts_per_page'],
				'order'        => (!empty($opt['order'])) ? $opt['order'] : $options['order'],
				'orderby'      => (!empty($opt['orderby'])) ? $opt['orderby'] : $options['orderby'],
				'post_status'  => (!empty($opt['post_status'])) ? $opt['post_status'] : $options['post_status'],
				'tax'          => (!empty($opt['tax'])) ? $opt['tax'] : $options['tax'],
				'term'         => (!empty($opt['term'])) ? $opt['term'] : $options['term'],
				'acf'          => (!empty($opt['acf'])) ? $opt['acf'] : $options['acf'],
				'paged'        => (!empty($opt['paged'])) ? $opt['paged'] : $options['paged'],
				'pagination'   => (!empty($opt['pagination'])) ? $opt['pagination'] : $options['pagination'],
			);
			update_option( $this->_option_name, $insert_opt );
		}
		else
		{
			$update_opt = array(
				'type'         => (!empty($opt['post'])) ? $opt['post'] : $this->type,
				'posts_per_page'     => (!empty($opt['posts_per_page'])) ? $opt['posts_per_page'] :  $this->posts_per_page,
				'order'        => (!empty($opt['order'])) ? $opt['order'] :  $this->order,
				'orderby'      => (!empty($opt['orderby'])) ? $opt['orderby'] :  $this->orderby,
				'post_status'  => (!empty($opt['post_status'])) ? $opt['post_status'] : $this->post_status,
				'tax'          => (!empty($opt['tax'])) ? $opt['tax'] :  $this->tax,
				'term'         => (!empty($opt['term'])) ? $opt['term'] :  $this->term,
				'acf'          => (!empty($opt['acf'])) ? $opt['acf'] :  $this->acf,
				'paged'        => (!empty($opt['paged'])) ? $opt['paged'] :  $this->paged,
				'pagination'   => (!empty($opt['pagination'])) ? $opt['pagination'] :  $this->pagination,
			);
			add_option( $this->_option_name, $update_opt );
		}
	}



	/**
	 * Create the capability
	 */
	private function _create_capability()
	{
		$role = get_role('administrator');

		if( !$role->has_cap( $this->_capability ) )  $role->add_cap($this->_capability );
	}
	
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    WP_Masonry_Grid_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
