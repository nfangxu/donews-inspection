<?php
/**
 * Created by PhpStorm.
 * User: nfangxu
 * Date: 2018/9/29
 * Time: 13:20
 */

namespace Fangxu\SafetyInspection;

interface SafetyInspection
{
    /**
     * @param string|array $text
     * @param callback $func
     * @return mixed
     */
    public function text($text, $func);

    /**
     * @param string|array $urls
     * @param callback $func
     * @return mixed
     */
    public function image($urls, $func);
}