<?php

/**
 * Class WP_Masonry_Grid_Query
 */
class WP_Masonry_Grid_Query
{
    /**
     * @var array
     */
    private $where = [];

    /**
     * @var string
     */
    private $postTable = '';

    /**
     * WP_Masonry_Grid_Query constructor.
     */
    public function __construct() { }

    /**
     * @param array $where
     */
    public function setTitleFilter($title)
    {
        global $wpdb;
        $this->where[] =  ' AND ' . $wpdb->posts. '.post_title LIKE \'%' . esc_sql( $wpdb->esc_like($title) ) . '%\'';
    }

    /**
     * @param array $where
     */
    public function setLetterFilter($letter)
    {
        global $wpdb;
        $this->where[] =  ' AND ' . $wpdb->posts. '.post_title LIKE \'' . esc_sql( $wpdb->esc_like($letter) ) . '%\'';
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