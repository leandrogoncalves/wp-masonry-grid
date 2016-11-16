<?php

/**
 * Define the Query functionality
 *
 * Loads and defines the query files for this plugin
 *
 * @link       https://github.com/leandrogoncalves/wp-masonry-grid
 * @since      1.0.0
 *
 * @package    WP_Masonry_Grid
 * @subpackage WP_Masonry_Grid/core
 */

if(!defined('ABSPATH')) die('Wordpress is required');

/**
 * Class WP_Masonry_Grid_Query
 *
 * @package WP_Masonry_Grid
 * @author  Leandro Goncalves <contato.Leandro Goncalves@gmail.com>
 *
 * @since 1.0.0
 */
class WP_Masonry_Grid_Query
{
    /**
     * @var array
     */
    private $where = [];

    /**
     * @var array
     */
    public $args = [];

    /**
     * @var array
     */
    private $request = [];

    /**
     * @var array
     */
    private $paged = [];

    /**
     * @var array
     */
    private $term = [];

    /**
     * @var array
     */
    private $tax = [];

    /**
     * WP_Masonry_Grid_Query constructor.
     */
    public function __construct($type='posts', $order='', $orderby='', $per_page='9', $paged='1',  $post_status ='publish') {
        $this->args = [
            'post_type'       => $type,
            'order'           => $order,
            'orderby'         => $orderby,
            'posts_per_page'  => $per_page,
            'paged'           => $paged,
            'post_status'     => $post_status,
        ];

    }

    /**
     * Set args a lot
     * @param array $args
     */
    public function setArgs(array $args){
        $this->args = [
            'post_type'       => 'posts',
            'order'           => '',
            'orderby'         => '',
            'posts_per_page'  => '9',
            'paged'           => '1',
            'post_status'     => 'publish',
        ];

        $this->args = array_merge($this->args, $args);

        return $this;
    }

    /**
     * @param array $args
     */
    public function setArg($name, $args)
    {
        $this->args[$name] = $args;
        return $this;
    }

    /**
     * Retrive the wp_query result
     * @param array $filter
     * @return WP_Query
     */
    public function getResults(array $filter = []){
        $this->tax = !empty($filter['tax']) ? $filter['tax'] : '';
        return new WP_Query($this->getQueryArgs());
    }

    /**
     * Retrieve the arguments of wp_query
     * @return array
     */
    public function getQueryArgs(){
        global $wpdb;

        //Pega dados vindos do request
        $this->request = WP_Masonry_Grid_Static::getInput();

        //PAGINACAOç configura a pãgina atual
        $this->args['paged'] = isset($this->request['pg']) ? (int) $this->request['pg'] : $this->args['paged']  ;
        $this->args['paged'] = !empty(absint( get_query_var( 'paged' ) )) ? (int) absint( get_query_var( 'paged' ) ) : $this->args['paged'] ;

        $this->args['offset'] = ($this->args['paged'] - 1) * $this->args['posts_per_page'];

        //Filtro por taxonimia vinda do request
        if( !empty($this->request['wpmg']['tax'][$this->tax]) ) {

            $this->term = $this->request['wpmg']['tax'][$this->tax];

            $this->args['tax_query'][] = [
                'taxonomy' => $this->tax,
                'field'    => 'slug',
                'terms'    => $this->term,
            ];

        }

        //Filtro especifico por titulo vindo do request
        if( !empty($this->request['wpmg']['filter']['title'])){
            $this->where[] =  ' AND ' . $wpdb->posts. '.post_title LIKE \'%' . esc_sql( $wpdb->esc_like($this->request['wpmg']['filter']['title']) ) . '%\'';
        }

        //Filtro especificao por letra vinda do request
        if( !empty($this->request['wpmg']['filter']['letter'])){
            $this->where[] =  ' AND ' . $wpdb->posts. '.post_title LIKE \'' . esc_sql( $wpdb->esc_like($this->request['wpmg']['filter']['letter']) ) . '%\'';
        }


        return $this->args;

    }

    /**
     * Verify if this is loaded
     * @return int
     */
    public function isLoaded(){return(1);}


    /**
     * Add custom where clause in sql query
     * @param $where
     * @param $wp_query
     * @return string
     */
    public function post_where($where, &$wp_query){

        if(is_array($this->where)){
            if(!empty($this->where)){
                foreach ($this->where as $w) {
                    $where .= $w;
                }
            }
        }
        return $where;
    }

}