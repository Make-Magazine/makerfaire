<?php

function random_pic($dir = '/uploads'){
    $files = glob($dir . '/*.*');
    $file = array_rand($files);
    return $files[$file];
}
