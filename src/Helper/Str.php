<?php
/**
 * Created by PhpStorm.
 * User: Nam Ngo
 * Date: 2020-02-15
 * Time: 14:36
 */

namespace StCommonService\Helper;


class Str
{

    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param int $length
     * @return string
     * @throws \Exception
     */
    public static function random($length = 16)
    {
        $string = '';

        while (($len = strlen($string)) < $length) {
            $size = $length - $len;

            $bytes = random_bytes($size);

            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }

        return $string;
    }
}