<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
echo preg_replace('/([\w.-]+@[\w.-]+\.[a-zA-Z]{2,6})/', '$1', 'tifosi@bvb-tifosi.de  <tifosi@bvb-tifosi.de>');
if (preg_match('/([\w.-]+@[\w.-]+\.[a-zA-Z]{2,6})/', 'tifosi@bvb-tifosi.de  <tifosi@bvb-tifosi.de>', $m)) {
    var_dump($m[0]);
}
?>
