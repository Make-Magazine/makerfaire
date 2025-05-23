<?php

/**
 * Redirect old custom gravity form admin pages to the correct path
 */
function redirect_gf_admin_pages() {
    global $pagenow;

    /* Check current admin page. */
    if ($pagenow == 'admin.php') {
        if (isset($_GET['page']) && $_GET['page'] == 'mf_entries') {
            //include any parameters in the return URL
            $returnURL = '';
            foreach ($_GET as $key => $param) {
                if ($key != 'page') {
                    if ($key == 'view' && $param == 'mfentry')
                        $param = 'entry';
                    $returnURL .= '&' . $key . '=' . $param;
                }
            }
            wp_redirect(admin_url('admin.php') . "?page=gf_entries" . $returnURL);
            exit;
        }
    }
}

add_action('admin_menu', 'redirect_gf_admin_pages');

//add new submenu for our custom built list page
add_filter('gform_addon_navigation', 'add_menu_item');

function add_menu_item($menu_items) {
    
    $menu_items[] = array("name" => "mf_fsp_gsp", "label" => "FSP/GSP", "callback" => "build_fsp_gsp", "permission" => "edit_posts");
    $menu_items[] = array("name" => "mf_fairesign", "label" => "Faire Signs", "callback" => "build_faire_signs", "permission" => "edit_posts");

    // or add a page without it getting added to the menu
    add_submenu_page('', "Showcases", "Showcases", "edit_posts", "mf_showcase", "build_mf_showcase");
    //$menu_items[] = array("name" => "mf_showcase", "label" => "Showcases", "callback" => "build_mf_showcase", "permission" => "edit_posts");

    return $menu_items;
}

add_action('admin_bar_menu', 'toolbar_link_to_mypage', 50);

function toolbar_link_to_mypage($wp_admin_bar) {

    $user = wp_get_current_user();

    $locations = get_registered_nav_menus();
    $menus = wp_get_nav_menus();
    $menu_locations = get_nav_menu_locations();


    //create MF admin with information from the faire table
    $args = array(
        'id' => 'mf_admin_parent',
        'title' => 'MF Admin',
        'meta' => array('class' => 'my-toolbar-page'),
    );

    $wp_admin_bar->add_node($args);
    buildFaireDrop($wp_admin_bar);
    $faire = '';
    //add custom menu items
    $locations = array('mf-admin-bayarea-register-menu', 'mf-admin-newyork-register-menu');
    foreach ($locations as $location_id) {
        //is this a navigation menu?
        if (isset($menu_locations[$location_id])) {
            foreach ($menus as $menu) {
                // If the ID of this menu is the ID associated with the location we're searching for
                if ($menu->term_id == $menu_locations[$location_id]) {
                    // This is the correct menu
                    $menu_items = wp_get_nav_menu_items($menu);
                    //build faire specific admin
                    foreach ((array) $menu_items as $key => $menu_item) {

                        if ($menu_item->menu_item_parent == 0) {
                            // each MF Admin menu has a parent item set that will tell us which faire to add these menu item's too
                            $faire = $menu_item->attr_title;
                        } else {
                            $faire = $menu_item->menu_item_parent;
                            $args = array(
                                'id' => $menu_item->object_id,
                                'title' => $menu_item->title,
                                'href' => $menu_item->url,
                                'meta' => array('class' => 'my-toolbar-page'),
                                'parent' => 'mf_admin_parent_' . $faire
                            );
                            //error_log(print_r($args,TRUE));
                            $wp_admin_bar->add_node($args);
                        }
                    }
                }
            }
        }
    }

    //faire setup
    $location_id = 'mf-admin-fairesetup-register-menu';

    if (isset($menu_locations[$location_id])) {
        foreach ($menus as $menu) {
            // If the ID of this menu is the ID associated with the location we're searching for
            if ($menu->term_id == $menu_locations[$location_id]) {
                // This is the correct menu
                $menu_items = wp_get_nav_menu_items($menu);
                foreach ((array) $menu_items as $key => $menu_item) {

                    $args = array(
                        'id' => $menu_item->object_id,
                        'title' => $menu_item->title,
                        'href' => $menu_item->url,
                        'meta' => array('class' => 'my-toolbar-page'),
                        'parent' => 'mf_admin_parent_fairesetup'
                    );

                    $wp_admin_bar->add_node($args);
                }
            }
        }
    }


    //add new navigation node
    $args = [
        'id' => 'wp-submit-asana-bug',
        'title' => '<span class="wp-menu-image dashicons-before dashicons-buddicons-replies"></span>' . 'Report a Bug',
        'meta' => array('target' => '_blank'),
        'href' => 'https://form.asana.com/?hash=936d55d2283dea9fe2382a75e80722675681f3881416d93f7f75e8a4941c6d47&id=1149238253861292',
    ];

    $wp_admin_bar->add_menu($args);
}

function buildFaireDrop(&$wp_admin_bar, $faire_id = null) {
    $args = array();

    //add link to admin review
    $args[] = array(
        'id' => 'mf_admin_entry_review',
        'title' => 'Admin Entry Review',
        'href' => '/review/index.php',
        'meta' => array('class' => 'my-toolbar-page'),
        'target' => '_blank',
        'parent' => 'mf_admin_parent'
    );
    //build faire drop downs
    global $wpdb;

    $sql = (isset($faire_id)) ? "select *, count(*) as count from wp_mf_faire, wp_gf_entry
                where FIND_IN_SET (wp_gf_entry.form_id,wp_mf_faire.form_ids)> 0 and
                        wp_gf_entry.status = 'active' and faire='$faire_id'
                group by wp_mf_faire.faire
                ORDER BY `wp_mf_faire`.`start_dt` DESC" :
        "select *, count(*) as count from wp_mf_faire, wp_gf_entry
                where FIND_IN_SET (wp_gf_entry.form_id,wp_mf_faire.form_ids)> 0 and
                        wp_gf_entry.status = 'active'
                group by wp_mf_faire.faire
                ORDER BY `wp_mf_faire`.`faire_location` ASC, start_dt desc";

    $menu_array = array();
    foreach ($wpdb->get_results($sql) as $row) {
        //menu array
        $menu_array[$row->faire_location][$row->faire] = array(
            'count' => $row->count,
            'faire_name' => $row->faire_name,
            'form_ids' => $row->form_ids
        );
    }

    //buid the menu
    foreach ($menu_array as $key => $menu) {
        $menuID = strtolower(str_replace(' ', '_', $key ?? ''));

        //level 1 - Faire name
        array_push($args, array(
            'id' => 'mf_admin_parent_' . $menuID,
            'title' => $key,
            'meta' => array('class' => 'my-toolbar-page'),
            'parent' => 'mf_admin_parent'
        ));

        //level 2 - Faire year
        foreach ($menu as $faire => $faireInfo) {
            array_push($args, array(
                'id' => 'mf_admin_parent_' . $faire,
                'title' => $faireInfo['faire_name'] . ' (' . $faireInfo['count'] . ')',
                'meta' => array('class' => 'my-toolbar-page'),
                'parent' => 'mf_admin_parent_' . $menuID
            ));

            //Level 3 - Faire Form names
            $formSQL = "
            SELECT form_id,form.title,count(*) as count
                    FROM `wp_gf_entry` join wp_gf_form form
                    WHERE form.id = form_id and `form_id` IN (" . $faireInfo['form_ids'] . ") and status = 'active'
                    group by form_id
                    ORDER BY FIELD(form_id, " . $faireInfo['form_ids'] . ")";
            foreach ($wpdb->get_results($formSQL) as $formRow) {
                $adminURL = admin_url('admin.php') . "?page=gf_entries&view=entries&id=" . $formRow->form_id;

                array_push($args, array(
                    'id' => 'mf_admin_child_' . $formRow->form_id,
                    'title' => $formRow->title . ' (' . $formRow->count . ')',
                    'href' => $adminURL,
                    'meta' => array('class' => 'my-toolbar-page'),
                    'parent' => 'mf_admin_parent_' . $faire
                ));

                //Level 4 - entry status
                $statusSql = "SELECT wp_gf_entry_meta.id, meta_value, count(*) as count "
                    . " FROM `wp_gf_entry_meta` "
                    . " JOIN wp_gf_entry on wp_gf_entry.id = wp_gf_entry_meta.entry_id "
                    . "WHERE wp_gf_entry.form_id = " . $formRow->form_id
                    . "  AND wp_gf_entry_meta.meta_key = '303' and status = 'active' group by meta_value";

                foreach ($wpdb->get_results($statusSql) as $statusRow) {
                    array_push($args, array(
                        'id' => 'mf_admin_subchild_' . $statusRow->id,
                        'title' => $statusRow->meta_value . ' (' . $statusRow->count . ')',
                        'href' => $adminURL . '&sort=0&dir=DESC&' . urlencode('filterField[]') . '=303|is|' . str_replace(' ', '+', $statusRow->meta_value ?? ''),
                        'meta' => array('class' => 'my-toolbar-page'),
                        'parent' => 'mf_admin_child_' . $formRow->form_id
                    ));
                }
            }

            //add BA23 Main Entry View link
            if ((isset($faire) && $faire == 'BA23')) {
                array_unshift($args, array(
                    'id' => 'mf_admin_main_gv_review',
                    'title' => 'Main Entry View',
                    'href' => 'https://makerfaire.com/bay-area/main-entry-view-ba23/',
                    'meta' => array('class' => 'my-toolbar-page'),
                    'parent' => 'mf_admin_parent_' . $faire
                ));
            }

            //add BA23 interest form admin review link
            if ((isset($faire) && $faire == 'BA23')) {
                array_unshift($args, array(
                    'id' => 'mf_admin_child_gv_review',
                    'title' => 'Interest Form Entry Review',
                    'href' => 'https://makerfaire.com/ba23-admin-view/',
                    'meta' => array('class' => 'my-toolbar-page'),
                    'parent' => 'mf_admin_parent_' . $faire
                ));
            }        

            //add scheduling link
            if (!(isset($faire_id))) {
                $args[] = array(
                    'id' => 'mf_admin_scheduling_' . $faire,
                    'title' => 'Scheduling',
                    'href' => '/mfscheduler/' . $faire,
                    'meta' => array('class' => 'my-toolbar-page'),
                    'parent' => 'mf_admin_parent_' . $faire
                );
            }

            //add showcase link            
            if (!(isset($faire_id))) {
                 
                $args[] = array(
                    'id' => 'mf_admin_showcase_' . $faire,
                    'title' => 'Showcases',
                    'href' => '/wp-admin/admin.php?page=mf_showcase&formid=' . $faireInfo['form_ids'],
                    'meta' => array('class' => 'my-toolbar-page'),
                    'parent' => 'mf_admin_parent_' . $faire
                );
            }
        }
    }

    //add RMT link
    if (!(isset($faire_id))) {
        $args[] = array(
            'id' => 'mf_admin_parent_rmt',
            'title' => 'Reports',
            'href' => '/resource-mgmt/',
            'meta' => array('class' => 'my-toolbar-page'),
            'target' => '_blank',
            'parent' => 'mf_admin_parent'
        );
    }

    //add the items to the menu now
    foreach ($args as $arg) {
        $wp_admin_bar->add_node($arg);
    }
}

// add a custom menu item to the Form Settings page menu for Tasks
add_filter('gform_form_settings_menu', 'mf_tasks_settings_menu_item');

function mf_tasks_settings_menu_item($menu_items) {
    $menu_items[] = array(
        'name' => 'mf_tasks_settings_page',
        'label' => __('Tasks'),
        'query' => array('tid' => null)
    );
    return $menu_items;
}

// handle displaying content for tasks page
add_action('gform_form_settings_page_mf_tasks_settings_page', 'mf_tasks_settings_page');

function mf_tasks_settings_page() {
    require_once(get_template_directory() . '/classes/GFTask.php');
    //page header loaded in below function because admin messages were not yet available to the header to display
    GFTask::task_page();
}

/* Displays faire sign code */

function build_fsp_gsp() {
    require_once(get_template_directory() . '/adminPages/other_form_download.php');
}

function build_mf_showcase() {
    require_once(get_template_directory() . '/adminPages/build_mf_showcase.php');
}

add_filter( 'gform_toolbar_menu', 'my_custom_toolbar', 10, 2 );
 
function my_custom_toolbar( $menu_items, $form_id ) {
 
    $menu_items['my_custom_link'] = array(
        'label'       => 'Showcases', // the text to display on the menu for this link
        'title'       => 'Showcase Entries', // the text to be displayed in the title attribute for this link
        'url'         => self_admin_url( 'admin.php?page=mf_showcase&formid=' . $form_id ), // the URL this link should point to
        'menu_class'  => 'gf_form_toolbar_custom_link', // optional, class to apply to menu list item (useful for providing a custom icon)
        //'link_class'  => rgget( 'page' ) == 'my_custom_page' ? 'gf_toolbar_active' : '', // class to apply to link (useful for specifying an active style when this link is the current page)
        'capabilities'=> array( 'gravityforms_edit_forms' ), // the capabilities the user should possess in order to access this page
        'priority'    => 500 // optional, use this to specify the order in which this menu item should appear; if no priority is provided, the menu item will be appended to end
    );
 
    return $menu_items;
}