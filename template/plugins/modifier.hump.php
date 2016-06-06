<?php
/**
 * Smarty Modifier 将带下划线的字符串转换为驼峰命名法
 *
 * @param   string  $string 要转换的字符串
 * @return  string          转换后的字符串
 */
function smarty_modifier_hump ($string) {

    $clips  = explode('_', $string);
    $name   = array_shift($clips);

    foreach ($clips as $subName) {

        $name   .= ucfirst(strtolower($subName));
    }

    return  $name;
}
