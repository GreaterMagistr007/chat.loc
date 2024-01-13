<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Возвращаем отрицательный ответ (Для API)
     * @param $text
     * @param array $return_values_array
     */
    static function error($text,$return_values_array=[]){
        exit(json_encode(array_merge([
                "text" =>  $text,
                "type"  =>  "error"
            ],$return_values_array)
        ));
        die();
    }

    /**
     * Возвращаем положительный ответ (Для API)
     * @param $text
     * @param array $return_values_array
     * @return false|string
     */
    static function success($text,$return_values_array=[]){
        exit(json_encode(array_merge([
                "text" =>  $text,
                "type"  =>  "success"
            ],$return_values_array)
        ));
        die();
    }
}
