<?php

/**
 * The [wpmg] ajax of the plugin.
 *
 * @link       https://github.com/leandrogoncalves/wp-masonry-grid
 * @since      1.0.0
 *
 * @package    WP_Masonry_Grid
 * @subpackage WP_Masonry_Grid/src
 */



if(!defined('ABSPATH')) die('Wordpress is required');
/**
 * Class WP_Masonry_Grid_Ajax
 *
 * @package WP_Masonry_Grid
 * @author  Leandro Goncalves <contato.Leandro Goncalves@gmail.com>
 *
 * @since 1.0.0
 */
class WP_Masonry_Grid_Ajax
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
     * Option name
     *
     * @var string
     */
    private $_option_name = 'wpmg_options';

    /**
     * array de variáveis
     *
     * @var array
     */
    private $vars = [];

    /**
     * @var WP_Masonry_Grid_View Object
     */
    private $view;

    /**
     * @var WP_Masonry_Grid_Query
     */
    private $query;

    /**
     * WP_Masonry_Grid_Ajax constructor.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        $this->plugin_path = plugin_dir_path( dirname( __FILE__ ) );
        $this->_load_dependences();
        $this->view  = new WP_Masonry_Grid_View();
        $this->query = new WP_Masonry_Grid_Query();
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
     * Load classes
     */
    private function _load_dependences(){

        require_once $this->plugin_path . 'core/WP_Masonry_Grid_Query.php';

        require_once $this->plugin_path . 'core/WP_Masonry_Grid_View.php';

    }



    /**
     * Function to paginate results via Ajax
     */
    public function AjaxPagination(){

        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        wp_send_json( ['teste'=>1] );
        /*
        if(empty($dados)) die('error data');


        $output = [
            'data'  => 0,
            'page'  => ++$dados['page']
        ];

        /**
         * @param 1 - string nonce
         * @param 2 - nome do acao no momento da geração do nonce pela função wp_nonce_field

        if(wp_verify_nonce($dados['nonce'], 'wpmg-ajax-pagination')) die('nonce error');

        $options = get_option( $this->_option_name );

        $args = [
            'post_type'        => $options['type'],
            'order'            => $options['order'],
            'orderby'          => $options['orderby'] ,
            'posts_per_page'   => $options['posts_per_page'] ,
            'paged'            => $dados['page'] ,
            'post_status'      => $options['post_status'],
            'suppress_filters' => true //PROPRIEDADE NECESSARIA PARA USAR WP_QUERY COM AJAX ** NAO REMOVER **
        ];

        $rs = $this->query->setArgs($args)->getResults();

        $output['no_more'] = ($rs->max_num_pages == $dados['page'] || $rs->max_num_pages == 0) ? true : false;

        $results = [];

        if ( $rs ) {

            while ( $rs->have_posts() ) : $rs->the_post();


                $vars['ID'] = get_the_ID();
                $vars['title'] = get_the_title();
                $vars['permalink'] = get_the_permalink();


                if( null != $options['tax'] ) {
                    $tax_terms = get_the_terms($vars['ID'], $options['tax'] );

                    $seguimentos = [];
                    if(!empty($tax_terms)){
                        foreach ($tax_terms as  $tx){
                            $seguimentos[] = "<a href='?wpmg_tax={$tx->slug}'>{$tx->name}</a>";
                        }
                    }
                    $vars['seguimentos'] = implode(' | ',  $seguimentos);

                }

                $vars['customFields'] = $options['acf'] ? WP_Masonry_Grid_Static::getACFCustomFields($options['acf'], $vars['ID'] ) : '';

                $results[] = $this->view->render('frontend/loop_masonry', $vars);

            endwhile;

        }

        $output['query'] = $rs;
        $output['args'] = $this->query->getQueryArgs();
        $output['data'] = $results;

        wp_reset_query();

        wp_send_json( $output );
*/
    }




}