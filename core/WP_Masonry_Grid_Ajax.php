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
     * Option name
     *
     * @var string
     */
    protected $_option_name = 'wpmg_options';

    /**
     * array de variáveis
     *
     * @var array
     */
    protected $vars = [];


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
     * WP_Masonry_Grid_Ajax constructor.
     */
    public function __construct() {
        $this->plugin_path = plugin_dir_path( dirname( __FILE__ ) );
    }


    /**
     * Function to paginate results via Ajax
     */
    public function AjaxPagination(){

        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if(empty($dados)) die(0);


        $output = [
            'data'  => 0,
            'page'  => $dados['page']
        ];

        /**
         * @param 1 - string nonce
         * @param 2 - nome do acao no momento da geração do nonce pela função wp_nonce_field
         */
        if(wp_verify_nonce($dados['nonce'], 'wpmg-ajax-pagination')) die(0);

        $options = get_option( $this->_option_name );

        $output['opt'] = $options;
        
        $wpmg_query = new WP_Masonry_Grid_Query(
            $options['post_type'],
            $options['order'],
            $options['orderby'],
            $options['posts_per_page'],
            $dados['page'],
            $options['post_status']
        );

        $this->rs = $wpmg_query->getResults();

        if ( $this->rs ) {

            while ( $this->rs->have_posts() ) : $this->rs->the_post();

                ob_start();

                $this->ID = get_the_ID();
                $this->title = get_the_title();
                $this->permalink = get_the_permalink();

                $output['title'] = $this->title;

                if( null != $options['tax'] ) {
                    $tax_terms = get_the_terms($this->ID, $this->tax );

                    $seguimentos = [];
                    if(!empty($tax_terms)){
                        foreach ($tax_terms as  $tx){
                            $seguimentos[] = "<a href='?wpmg_tax={$tx->slug}'>{$tx->name}</a>";
                        }
                    }
                    $this->seguimentos = implode(' | ',  $seguimentos);

                }

                $this->customFields =  $options['acf'] ? WP_Masonry_Grid_Static::getACFCustomFields($options['acf'], $this->ID) : '';

                $this->render('loop_masonry');

                $output['data'][] =  ob_get_clean();

            endwhile;

        }

        wp_send_json( $output );

    }

}