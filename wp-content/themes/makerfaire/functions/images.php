<?php

function random_pic($dir = '/uploads'){
    $files = glob($dir . '/*.*');
    $file = array_rand($files);
    return str_replace(ABSPATH, get_site_url() . '/', $files[$file]);
}
