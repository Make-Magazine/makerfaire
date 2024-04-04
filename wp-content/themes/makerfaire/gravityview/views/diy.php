<?php
gw_multi_file_merge_tag()->register_settings();
echo '  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-responsive-tabs@2.0.3/dist/css/bootstrap-responsive-tabs.min.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap-responsive-tabs@2.0.3/dist/js/jquery.bootstrap-responsive-tabs.min.js"></script>';
/**
 * @package GravityView-DIY
 * @subpackage GravityView-DIY/templates
 *
 * @global \GV\Template_Context $gravityview
 */
$template = $gravityview->template;

gravityview_before($gravityview);

$container = apply_filters('gravityview-diy/container', 'div', $gravityview);

?>

<?php if ($container) { ?>
    <<?php echo $container; ?> class="<?php gv_container_class('gv-diy-container gv-diy-multiple-container', true, $gravityview); ?>">
    <?php } ?>

    <?php gravityview_header($gravityview);

    /**
     * @filter `gravityview-diy/wrap/multiple` Should each entry in Multiple Entries context be wrapped in minimal HTML containers?
     * @param bool $wrap Default: true
     */
    $wrap = apply_filters('gravityview-diy/wrap/multiple', true, $gravityview);

    /**
     * @action `gravityview_diy_body_before` (deprecated)
     * @action `gravityview/template/diy/body/before`
     */
    $template::body_before($gravityview);

    // There are no entries.
    if (!$gravityview->entries->count()) {

        if (!$wrap) {
            echo gv_no_results(true, $gravityview);
        } else {
    ?>
            <div class="gv-diy-view gv-no-results">
                <div class="gv-diy-view-title">
                    <h3><?php echo gv_no_results(true, $gravityview); ?></h3>
                </div>
            </div>
            <?php
        }
    } elseif ($gravityview->fields->by_position('directory_diy-diy')->by_visible()->count()) {

        // There are entries. Loop through them.
        foreach ($gravityview->entries->all() as $entry) {

            if ($wrap) {
                $entry_slug = GravityView_API::get_entry_slug($entry->ID, $entry->as_entry());

                /**
                 * @filter `gravityview/template/list/entry/class`
                 * @filter `gravityview_entry_class`
                 */
                $entry_class = $template::entry_class('gv-diy-view', $entry, $gravityview);
            ?>

                <div id="gv_diy_<?php echo esc_attr($entry_slug); ?>" class="<?php echo esc_attr($entry_class); ?> card" style="width:90%; margin:20px auto;">

                <?php
            }

            /**
             * @action `gravityview_entry_before`
             * @action `gravityview/template/diy/entry/before`
             */
            $template::entry_before($entry, $gravityview);

            /**
             * Output the field.
             */
            foreach ($gravityview->fields->by_position('directory_diy-diy')->by_visible()->all() as $field) {
                $str = $template->the_field($field, $entry);
                
                //find all tabs
                preg_match_all("/\[tab\]\s*(.[\S\s]*?)\s*\[\/tab\]/", $str, $tabs);
                //check if this is just a regular display without tabs
                if(empty($tabs[1])){
                    echo $str;
                    continue;
                }
                $tab_outer      = '';
                $tab_content    = '';
                $active         = 'active';
                
                foreach ($tabs[1] as $key => $tab) {
                    if($key<>0) $active = '';
                    //find the title
                    preg_match_all("/\[title\]\s*(.[\S\s]*?)\s*\[\/title\]/", $tab, $title_array);
                    $title = (!empty($title_array[1][0]) ? $title_array[1][0] : 'tab-' . $key);
                    $tab_name = strtolower(str_replace(' ', '-', $title)). esc_attr($entry_slug);

                    $tab_outer .= '<li class="nav-item">'; //start new tab
                    $tab_outer .= '  <button class="nav-link '.$active.'" id="' . $tab_name . '-tab" data-toggle="tab" data-target="#' . $tab_name . '" type="button" role="tab" aria-controls="' . $tab_name . '" aria-selected="'.($active?'true':'false').'">' . $title . '</button>';
                    $tab_content .= '<div class="tab-pane '.($active? $active:'').'" id="' . $tab_name . '" role="tabpanel" aria-labelledby="' . $tab_name . '-tab">';

                    //build the tab content
                    preg_match_all("/\[tab_content\]\s*(.[\S\s]*?)\s*\[\/tab_content\]/", $tab, $tab_content_arr);
                    //there should only be 1 tab content per tab
                    if ($tab_content_arr)
                        $tab_content .= retrieve_blocks($tab_content_arr[1][0]);

                    //build the expand section
                    preg_match_all("/\[expand\]\s*(.[\S\s]*?)\s*\[\/expand\]/", $tab, $expand_content_arr);
                    
                    if ($expand_content_arr) {
                        $expand_return = '';
                        foreach($expand_content_arr[1] as $expand_content){
                            $expand_return .= retrieve_blocks($expand_content);
                        }
                        
                        if (!empty($expand_return)) {
                            $tab_content .=
                                '<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#' . $tab_name . '-expand" aria-expanded="false" aria-controls="' . $tab_name . '-expand">
                                    Expand
                                </button>
                                <div class="collapse" id="' . $tab_name . '-expand">
                                    <div class="card card-body">
                                        ' . $expand_return . '
                                    </div>
                                </div>';
                        }
                    }


                    $tab_content .= '</div>';
                    $tab_outer .= '</li>'; //close tab
                }

                //build the tabs and content
                ?>
                
                <ul class="nav nav-tabs" role="tablist">
                    <?php echo $tab_outer;?>
                </ul>   
                <div class="tab-content">
                    <?php echo $tab_content;?>
                </div>   
                
                <?php
            }

            /**
             * @action `gravityview_entry_after`
             * @action `gravityview/template/diy/entry/after`
             */
            $template::entry_after($entry, $gravityview);

            if ($wrap) {
                ?>

                </div>

    <?php }
        }
    } // End if has entries

    /**
     * @action `gravityview_diy_body_after` (deprecated)
     * @action `gravityview/template/diy/body/after`
     */
    $template::body_after($gravityview);

    gravityview_footer($gravityview); ?>

    <?php if ($container) { ?>
    </<?php echo $container; ?>>
<?php } ?>

<?php gravityview_after($gravityview);

function retrieve_blocks($content) {
    $return = '';

    if (empty($content)) {
        return 'no content submitted';
    }

    //find all blocks
    preg_match_all("/\[block\]\s*(.[\S\s]*?)\s*\[\/block\]/", $content, $blocks);
    foreach ($blocks[1] as $block) {
        $return .= '<div class="row">';

        preg_match_all("/\[column\]\s*(.[\S\s]*?)\s*\[\/column\]/", $block, $columns);
        foreach ($columns[1] as $column) {
            $return .= '<div class="col-sm">';
            $return .= $column;
            $return .= '</div>';
        }
        $return .= '</div>';
    }
    return $return;
}
?>