<?php 

if (!class_exists('ESSB_Logger_ShareCounter_Update')) {
    include_once (ESSB3_CLASS_PATH . 'loggers/class-sharecounter-update.php');
}

$log = ESSB_Logger_ShareCounter_Update::get_log();

if (is_array($log)) {
    $log = array_reverse($log);
}

// possible array reverse
echo '<div class="advanced-flex advanced-flex-wrap">';
foreach ($log as $key => $data) {
    echo '<div class="advanced-flex-row">';
    echo '<div class="advanced-flex-cell w15">' . $data['date'] . '</div>';
    echo '<div class="advanced-flex-cell w15"><span class="tag-network-log essb-network-color-'.$data['network'].'">' . $data['network'] . '</span></div>';
    echo '<div class="advanced-flex-cell w70">';
    echo '<a href="'.esc_url($data['url']). '" target="_blank">' . $data['url'] . '</a>';
    echo '<br/>';
    echo '<a href="'.esc_url($data['request']). '" target="_blank">' . $data['request'] . '</a>';
    echo '</div>';
    echo '<div class="advanced-flex-cell w100"><pre>' . $data['response'] . '</pre></div>';
    echo '</div>';
}

if (empty($log)) {
    echo '<div class="advanced-flex-row"><div class="advanced-flex-cell w100">There is no information for a counter update at the moment</div></div>';
}

echo '</div>';