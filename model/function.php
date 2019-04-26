<?php
/**
 * get simple array without multi-dimension
 * 
 * @param array $array
 * @return array
 */
function getSimpleArray($array)
{
    $new_array = array();
    foreach ($array as $key => $value) {
        if (is_int($key)) {
            $new_array[] = $value;
        }
    }
    return $new_array;
}