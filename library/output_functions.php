<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/* TruncateText
//truncates text on a word or other string and pads it
*/
function TruncateText($string, $limit, $break = ".", $pad = "...") {
// return with no change if string is shorter than $limit
    if (strlen($string) <= $limit)
        return $string;
    // is $break present between $limit and the end of the string? 
    if (false !== ($breakpoint = strpos($string, $break, $limit))) {
        if ($breakpoint < strlen($string) - 1) {
            $string = substr($string, 0, $breakpoint) . $pad;
        }
    }

    return $string;
}

/*
 * NGPS thumbnail
 */

function newThumb($str,$size) {
    $newEnding="_".$size.".JPG";
    $newString = str_replace(".JPG", $newEnding, $str);
    return $newString;
    }
?>
