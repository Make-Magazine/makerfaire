<?php

if ( ! defined('WPINC')) {
    die;
}

?>
<div class="wrap">
    <h1><?php _e('Lazy Load Optimizer settings', 'lazy-load-optimizer') ?></h1>
    <?php if($tabs): ?>
    <h2 class="nav-tab-wrapper">
        <?php foreach ($tabs as $tab => $name): ?>
            <?php $class = ($tab == $current) ? ' nav-tab-active' : ''; ?>
            <a class='nav-tab<?php echo $class ?>'
               href='?page=lazy-load-optimizer-admin&tab=<?php echo $tab ?>'><?php echo $name ?></a>
        <?php endforeach; ?>
    </h2>
    <?php endif; ?>

    <?php
        $file = __DIR__ . "/tabs/{$current}.php";
        if (file_exists($file)){
            include $file;
        }
    ?>

    <?php _e('If you like <strong>Lazy Load Optimizer</strong> please leave us a <a href="https://wordpress.org/support/plugin/lazy-load-optimizer/reviews?rate=5#new-post" target="_blank">★★★★★</a> rating. Thanks!','lazy-load-optimizer') ?>


</div>