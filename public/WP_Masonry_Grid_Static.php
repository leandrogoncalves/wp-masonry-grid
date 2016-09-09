<?php

/**
 * Class WP_Masonry_Grid_Static
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

        if(!empty($inputs)){
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

        if(!empty($inputs)){
            if(array_key_exists($field[1], $inputs['wpmg'])){
                if(array_key_exists($field[2], $inputs['wpmg']['filter'])){
                    return  $inputs['wpmg']['filter']['title'];
                }
            }
        }
    }
    
}