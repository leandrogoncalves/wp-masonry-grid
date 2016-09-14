<?php

/**
 * The [wpmg] Viwer of the plugin.
 *
 * @link       https://github.com/leandrogoncalves/wp-masonry-grid
 * @since      1.0.0
 *
 * @package    WP_Masonry_Grid
 * @subpackage WP_Masonry_Grid/src
 */

if(!defined('ABSPATH')) die('Wordpress is required');

/**
 * Class WP_Masonry_Grid_View
 *
 * @package WP_Masonry_Grid
 * @author  Leandro Goncalves <contato.Leandro Goncalves@gmail.com>
 *
 * @since 1.0.0
 */
class WP_Masonry_Grid_View
{
    /**
     * array de variáveis
     *
     * @var array
     */
    private $vars = [];


    /**
     * WP_Masonry_Grid_View constructor.
     */
    public function __construct() {
        $this->plugin_path = plugin_dir_path( dirname( __FILE__ ) ) ;
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
     * Rendering views
     * @param $templateNmae
     */
    public function render($template, array $vars = []){

        if(!empty($vars)) foreach ($vars as $k => $att) $this->{$k} = $att;

        if(FALSE === stripos($template, '/')){
            $templateName = filter_var($template, FILTER_SANITIZE_STRING);
            $file =  $this->plugin_path . 'core/templates/'.$templateName.'.phtml' ;
        }else{
            $tmp = explode('/',$template);
            $templatePath =  $tmp[0];
            $templateName = filter_var($tmp[1], FILTER_SANITIZE_STRING);
            $file =  $this->plugin_path . $templatePath  .'/templates/'.$templateName.'.phtml' ;
        }



        if( file_exists( $file ) ) {
            ob_start();
            include( $file );
            return ob_get_clean();
        }else{
            echo 'Template não encontrado em ' . $file;
        }

    }


}