<?php

/**
 * Class WP_Masonry_Grid_Static
 */
class WP_Masonry_Grid_Static
{
    public static function getInput(){
        $inputs = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST'){

            $args = array(
                'wpmg'   => [
                    'filter' => FILTER_SANITIZE_STRING,
                    'flags'  => FILTER_REQUIRE_ARRAY
                ]
            );

            $inputs = filter_input_array(INPUT_POST, $args);
        }


        if ($_SERVER['REQUEST_METHOD'] == 'GET'){


            $inputs['pg'] = filter_input(INPUT_GET, 'pg', FILTER_VALIDATE_INT);
        }

        return $inputs;

    }

    public static function checked($name, $value){
        $inputs =  self::getInput();
        $checado = false;

        $field = explode('-',$name);

        if(array_key_exists($field[1], $inputs['wpmg'])){
            if(array_key_exists($field[2], $inputs['wpmg']['filter'])){
                switch ($field[2]){
                    case 'title' :
                        $checado = $inputs['wpmg']['filter']['title'] == $value ? true : false;
                        break;
                    case 'letter' :
                        $checado = $inputs['wpmg']['filter']['letter'] == $value ? true : false;
                        break;
                }
            }
        }

        return $checado ? ' checked="checked" ' : '';
    }

}