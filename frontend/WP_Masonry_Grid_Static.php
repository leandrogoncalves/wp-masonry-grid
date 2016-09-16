<?php
/**
 * The stic  functionality of the plugin.
 *
 * @link       https://github.com/leandrogoncalves/wp-masonry-grid
 * @since      1.0.0
 *
 * @package    WP_Masonry_Grid
 * @subpackage WP_Masonry_Grid/frontend
 */


if(!defined('ABSPATH')) die('Wordpress is required');
/**
 * Class WP_Masonry_Grid_Static
 *
 *
 * @package    WP_Masonry_Grid
 * @subpackage WP_Masonry_Grid/frontend
 * @author     Leandro Goncalves <contato.Leandro Goncalves@gmail.com>
 */
class WP_Masonry_Grid_Static
{

    /**
     * Get POST and GET streams and sanitize the data
     * @return array|mixed
     */
    public static function getInput(){
        $inputs = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST'){

            $args = [
                'wpmg'   => [
                    'filter' => FILTER_SANITIZE_STRING,
                    'flags'  => FILTER_REQUIRE_ARRAY
                ]
            ];

            $inputs = filter_input_array(INPUT_POST, $args);
        }

        if ($_SERVER['REQUEST_METHOD'] == 'GET'){
            $inputs['pg'] = filter_input(INPUT_GET, 'pg', FILTER_VALIDATE_INT);
        }

        return $inputs;
    }

    /**
     * Check if the value from stream is equals the input value
     * @param $name
     * @param $value
     * @return string
     */
    public static function checked($name, $value){
        $inputs =  self::getInput();
        $checado = false;

        $field = explode('-',$name);

        if(!empty($inputs) && isset($inputs['wpmg'])){
            if(array_key_exists($field[1], $inputs['wpmg'])){
                if(array_key_exists($field[2], $inputs['wpmg']['filter'])){
                    switch ($field[2]){
                        case 'letter' :
                            $checado = $inputs['wpmg']['filter']['letter'] == $value ? true : false;
                            break;
                    }
                }
                if($field[1] == 'tax'){
                    if(is_array($inputs['wpmg']['tax'])){
                        foreach ($inputs['wpmg']['tax'] as $taxonomia) {
                            if(is_array($taxonomia)){
                                $checado = in_array($value,$taxonomia) ? true : false;
                            }
                        }
                    }
                }
            }
        }

        return $checado ? " checked='checked' " : '';
    }


    /**
     * Get the value of text entry sent by form
     * @param $name
     * @return mixed
     */
    public static function getValue($name){
        $inputs = self::getInput();
        $field  = explode('-',$name);

        if(!empty($inputs) && isset($inputs['wpmg'])){
            if(array_key_exists($field[1], $inputs['wpmg'])){
                if(array_key_exists($field[2], $inputs['wpmg']['filter'])){
                    return  $inputs['wpmg']['filter']['title'];
                }
            }
        }
    }



    /**
     * Get a custom post from ACF plugin
     * @param $fieldNames
     * @param $id
     */
    public static function getACFCustomFields($fieldNames, $id){

        if(!function_exists('get_field')) wp_die('O plugin Advanced Custom Fields é necessario');

        $acfFiels = [];

        if(!is_array($fieldNames) &&  !(FALSE === stripos($fieldNames,',' )) ){
            $fieldNames = explode(',', $fieldNames);
        }

        if(is_array($fieldNames) && ! empty($fieldNames)){
            foreach ($fieldNames as $field) {
                $acfFiels[$field]  =  get_field($field, $id);
            }
        }else{
            $acfFiels = [$fieldNames => get_field($fieldNames, $id)];
        }


         return $acfFiels ? [$id => $acfFiels] : '';

    }



}