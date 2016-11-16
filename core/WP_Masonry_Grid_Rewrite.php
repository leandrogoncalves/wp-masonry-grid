<?php

/**
 * The rewrite rule url functionality of the plugin.
 *
 * @link       https://github.com/leandrogoncalves/wp-masonry-grid
 * @since      1.0.0
 *
 * @package    WP_Masonry_Grid
 * @subpackage WP_Masonry_Grid/frontend
 */

if(!defined('ABSPATH')) die('Wordpress is required');
/**
 * The rewrite rule url functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WP_Masonry_Grid
 * @subpackage WP_Masonry_Grid/core
 * @author     Leandro Goncalves <contato.Leandro Goncalves@gmail.com>
 */
class WP_Masonry_Grid_Rewrite
{
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
     * The Options name of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $opt_name;


    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $plugin_name       The name of the plugin.
     * @param    string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version, $opt_name ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->opt_name = $opt_name;

    }


    public function load_all_rules(){
        $this->add_seguiments_rule();
    }


    /**
     * Register the rewrite rule for custom taxonies of the site.
     * This run in init hook
     *
     * @since    1.0.0
     */
    private function add_seguiments_rule() {

        $opts =  get_option( $this->opt_name );

        if(!empty($opts['tax'])){

            flush_rewrite_rules();

            add_rewrite_rule(
                '(.?.+?)/'.$opts['tax'].'/?([^/]*)/?$'
                , 'index.php?pagename=$matches[1]&wpmg_tax_value=$matches[2]&wpmg_tax_type='.$opts['tax']
                ,'top'
            );

            add_rewrite_tag('%wpmg_tax_type%','([^/]*)');
            add_rewrite_tag('%wpmg_tax_value%','([^/]*)');

        }


    }





}