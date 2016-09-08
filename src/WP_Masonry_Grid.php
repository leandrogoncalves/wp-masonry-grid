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
 * @subpackage WP_Masonry_Grid/includes
 */



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
 * @author     leandrogoncalves <contato.leandrogoncalves@gmail.com>
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
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	protected function __construct() {

		$this->plugin_name = 'wp-masonry-grid';
		$this->version = '1.0.0';
		$this->site_url = get_site_url();
		$this->plugin_path = plugin_dir_path( dirname( __FILE__ ) );

		$this->load_dependencies();
		$this->set_locale();
		$this->define_public_hooks();
//		$this->define_admin_hooks();

	}


	/**
	 * seta uma variavel passando o nome e o valor para o um vetor associativo
	 * @param string $name  nome da variável
	 * @param string $value valor
	 */
	protected function set($name, $value='')
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
	 * Rendering views
	 * @param $templateNmae
	 */
	protected function render($templateNmae){
		$file =  $this->plugin_path . 'src/templates/'.$templateNmae.'.phtml' ;

		if( file_exists( $file ) ) {
			include( $file );
		}else{
			echo 'Template não encontrado em ' . $file;
		}

	}

	/**
	 * Get query args
	 * @return array|string
	 * @link http://php.net/manual/pt_BR/function.filter-input.php
	 */
	protected function getArgs(){
		$query_hooks = new WP_Masonry_Grid_Query();
		$query_args = [];
		$this->where = [];
		global $wpdb;

		$inputs = WP_Masonry_Grid_Static::getInput();


		$query_args = [
			'post_type'       => $this->type,
			'order'           => $this->order,
			'orderby'         => $this->order_by,
			'posts_per_page'  => $this->per_page,
			'paged'           => $this->paged,
			'post_status'     => 'publish',
		];

		if( !empty($inputs['wpmg']['tax'][$this->tax]) ) {

			$this->term = $inputs['wpmg']['tax'][$this->tax];

			$query_args['tax_query'][] = [
				'taxonomy' => $this->tax,
				'field'    => 'slug',
				'terms'    => $this->term,
			];

		}

		if( !empty($inputs['wpmg']['filter']['title'])){
			$query_hooks->setTitleFilter($inputs['wpmg']['filter']['title']);
		}

		if( !empty($inputs['wpmg']['filter']['letter'])){
			$query_hooks->setLetterFilter($inputs['wpmg']['filter']['letter']);
		}


		$this->loader->add_filter('posts_where', $query_hooks, 'post_where', 10, 2)->run();

		return $query_args;
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
		require_once $this->plugin_path . 'src/WP_Masonry_Grid_Loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once $this->plugin_path  . 'src/WP_Masonry_Grid_i18n.php';

		/***
		 * Class reponsiable for wp query implements
		 */
		require_once $this->plugin_path  . 'src/WP_Masonry_Grid_Query.php';

		/***
		 * Class reponiable for query string POST or GET
		 */
		require_once $this->plugin_path  . 'public/WP_Masonry_Grid_Static.php';


		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
//		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/WP_Masonry_Grid_Admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once $this->plugin_path  . 'public/WP_Masonry_Grid_Public.php';

		$this->loader = new WP_Masonry_Grid_Loader();
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
//		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

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
