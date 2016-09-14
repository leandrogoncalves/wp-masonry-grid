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
    private $args = [];

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
    public function __construct($type='', $order='', $order_by='', $per_page='', $paged='', $post_status ='publish') {
        $this->args = [
            'post_type'       => $type,
            'order'           => $order,
            'orderby'         => $order_by,
            'posts_per_page'  => $per_page,
            'paged'           => $paged,
            'post_status'     => $post_status,
        ];

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
    private function getQueryArgs(){
        global $wpdb;
        $this->request = WP_Masonry_Grid_Static::getInput();

        $this->paged = isset($this->request['pg']) ? (int) $this->request['pg'] : absint( get_query_var( 'paged' ) )  ;


        if( !empty($this->request['wpmg']['tax'][$this->tax]) ) {

            $this->term = $this->request['wpmg']['tax'][$this->tax];

            $this->args['tax_query'][] = [
                'taxonomy' => $this->tax,
                'field'    => 'slug',
                'terms'    => $this->term,
            ];

        }

        if( !empty($this->request['wpmg']['filter']['title'])){
            $this->where[] =  ' AND ' . $wpdb->posts. '.post_title LIKE \'%' . esc_sql( $wpdb->esc_like($this->request['wpmg']['filter']['title']) ) . '%\'';
        }

        if( !empty($this->request['wpmg']['filter']['letter'])){
            $this->where[] =  ' AND ' . $wpdb->posts. '.post_title LIKE \'' . esc_sql( $wpdb->esc_like($this->request['wpmg']['filter']['letter']) ) . '%\'';
        }


        return $this->args;

    }


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