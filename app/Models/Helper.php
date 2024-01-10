<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Helper
{
    /**
     * Генерируем строку случайного ЦИФЕРНОГО кода нужной длины
     * @param int $length
     * @return string
     */
    public static function generateRandomNumericString(int $length = 6): string
    {
        return self::generateRandomString($length, true, false);
    }

    /**
     * Генерируем строку произвольной длины
     * @param int $length
     * @param bool $usingNumeric
     * @param bool $usingLatinChars
     * @return string
     */
    public static function generateRandomString(int $length = 6, bool $usingNumeric = true, bool $usingLatinChars = true): string
    {
        $numbers = '0123456789';
        $latinChars = 'abcdefghijklmnopqrstuvwxyz';
        $latinChars .= mb_strtoupper($latinChars);

        // Составим ресурсную строку:
        $string = '';
        if ($usingNumeric) {
            $string .= $numbers;
        }
        if ($usingLatinChars) {
            $string .= $latinChars;
        }
        // Добьем строку до минимальной длины
        while (strlen($string) < $length) {
            $string .= $string;
        }

        return substr(str_shuffle($string), 0, $length);
    }

    /**
     * Заменяем строку русских символов на транслит
     * @param $value
     * @return string
     */
    static function translit($value): string
    {
        $converter = array(
            'а' => 'a',    'б' => 'b',    'в' => 'v',    'г' => 'g',    'д' => 'd',
            'е' => 'e',    'ё' => 'e',    'ж' => 'zh',   'з' => 'z',    'и' => 'i',
            'й' => 'y',    'к' => 'k',    'л' => 'l',    'м' => 'm',    'н' => 'n',
            'о' => 'o',    'п' => 'p',    'р' => 'r',    'с' => 's',    'т' => 't',
            'у' => 'u',    'ф' => 'f',    'х' => 'h',    'ц' => 'c',    'ч' => 'ch',
            'ш' => 'sh',   'щ' => 'sch',  'ь' => '',     'ы' => 'y',    'ъ' => '',
            'э' => 'e',    'ю' => 'yu',   'я' => 'ya',
        );

        $value = mb_strtolower(trim($value));
        $value = strtr($value, $converter);
        $value = mb_ereg_replace('[^-0-9a-z]', '-', $value);
        $value = mb_ereg_replace('[-]+', '-', $value);
        $value = trim($value, '-');

        return $value;
    }

    /**
     * Возвращает текущий домен сервера из текущего запроса
     * @return string вида "http://domain.ru/"
     */
    static function HOST(): string
    {
        return ((!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS'])) ?
                "https://" :
                "http://").self::DOMAIN();
    }

    static function DOMAIN(): string
    {
        return (isset($_SERVER,$_SERVER["HTTP_HOST"])) ? $_SERVER["HTTP_HOST"] : "localhost";
    }

    /**
     * @return string
     */
    static function randomColor(): string
    {
        return '#' . dechex(rand(0,10000000));
    }

    /**
     * @return string
     */
    static function getIp(): string
    {
        $keys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'REMOTE_ADDR'
        ];
        foreach ($keys as $key) {
            if (!empty($_SERVER[$key])) {
//                dd( $_SERVER[$key]);
                $k = explode(',', $_SERVER[$key]);
                $ip = trim(end($k));
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return '';
    }

    /**
     * Генерация целочисленного значения из диапазона
     * @param int $min
     * @param int $max
     * @return int
     */
    static function generateRandomInteger(int $min = 1, int $max = 10): int
    {
        $min = (int)$min;
        $max = (int)$max;
        if ($min < 1) {
            $min = 1;
        }
        if ($max < 1) {
            $max = 1;
        }

        return mt_rand($min, $max);
    }

    /**
     * Оставляет только цифры
     * @param $str
     * @return string
     */
    public static function keepOnlyDigits($str): string
    {
        return is_string($str) ? preg_replace('/[^0-9]/', '', $str) : '';
    }
}
