<?php

//Remove edit forms
add_action('admin_menu', 'national_remove_admin_submenus', 999);

function national_remove_admin_submenus() {
  $user = wp_get_current_user();
  $is_national = ( in_array('national', (array) $user->roles) );
  if ($is_national) {
    remove_menu_page('gf_edit_forms');
  }
}

//Remove edit forms
add_action('admin_menu', 'barnesandnoble_remove_admin_submenus', 999);

function barnesandnoble_remove_admin_submenus() {
  $user = wp_get_current_user();
  $is_barnesandnoble = ( in_array('barnes__noble', (array) $user->roles) );
  if ($is_barnesandnoble) {
    remove_menu_page('gf_edit_forms');
    remove_menu_page( 'index.php' );
    remove_menu_page( 'profile.php' );

  }
}

function remove_admin_bar_links() {
    global $wp_admin_bar;
    $user = wp_get_current_user();
    $is_barnesandnoble = ( in_array('barnes__noble', (array) $user->roles) );
    if ($is_barnesandnoble) {
      $wp_admin_bar->remove_menu('wp-logo');
      $wp_admin_bar->remove_menu('my-account');       // Remove the user details tab
      $wp_admin_bar->remove_menu('comments');         // Remove the comments link
      $wp_admin_bar->remove_menu('notes');         // Remove the comments link
    }
}
add_action( 'wp_before_admin_bar_render', 'remove_admin_bar_links' );

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

  return $menu_items;
}

add_action('admin_bar_menu', 'toolbar_link_to_mypage', 999);

function toolbar_link_to_mypage($wp_admin_bar) {

  $user              = wp_get_current_user();
  $is_national       = ( in_array('national', (array) $user->roles) );
  $is_barnesandnoble = ( in_array('barnes__noble', (array) $user->roles) );
  $locations         = get_registered_nav_menus();
  $menus             = wp_get_nav_menus();
  $menu_locations    = get_nav_menu_locations();

  if ($is_national) {
    $location_id = 'mf-admin-national-register-menu';
    if (isset($menu_locations[$location_id])) {
      foreach ($menus as $menu) {
        // If the ID of this menu is the ID associated with the location we're searching for
        if ($menu->term_id == $menu_locations[$location_id]) {
          // This is the correct menu
          $menu_items = wp_get_nav_menu_items($menu);

          $args = array(
              'id' => 'mf_admin_parent_national',
              'title' => 'National Admin',
              'meta' => array('class' => 'my-toolbar-page'),
          );

          $wp_admin_bar->add_node($args);
          buildFaireDrop($wp_admin_bar, 'NMF16');

          //build faire specific admin
          foreach ((array) $menu_items as $key => $menu_item) {
            if ($menu_item->menu_item_parent == 0) {
              // each MF Admin menu has a parent item set that will tell us which faire to add these menu item's too
              $faire = $menu_item->attr_title;
            } else {
              $args = array(
                  'id' => $menu_item->object_id,
                  'title' => $menu_item->title,
                  'href' => $menu_item->url,
                  'meta' => array('class' => 'my-toolbar-page'),
                  'parent' => 'mf_admin_parent_national'
              );

              $wp_admin_bar->add_node($args);
            }
          }
        }
      }
    }
  } elseif ($is_barnesandnoble) {

    $location_id = 'mf-admin-barnesandnoble-register-menu';
    if (isset($menu_locations[$location_id])) {
      foreach ($menus as $menu) {
        // If the ID of this menu is the ID associated with the location we're searching for
        if ($menu->term_id == $menu_locations[$location_id]) {
          // This is the correct menu
          $menu_items = wp_get_nav_menu_items($menu);
          $args = array(
              'id' => 'mf_admin_parent_barnesandnoble',
              'title' => 'Barnes And Noble Admin',
              'meta' => array('class' => 'my-toolbar-page'),
          );
          $wp_admin_bar->add_node($args);
          foreach ((array) $menu_items as $key => $menu_item) {

            $args = array(
                  'id' => $menu_item->object_id,
                  'title' => $menu_item->title,
                  'href' => $menu_item->url,
                  'meta' => array('class' => 'my-toolbar-page'),
                  'parent' => 'mf_admin_parent_barnesandnoble'
              );

              $wp_admin_bar->add_node($args);

          }

        }
      }
    }
  }
  else {
    //create MF admin with information from the faire table
    $args = array(
              'id' => 'mf_admin_parent',
              'title' => 'MF Admin',
              'meta' => array('class' => 'my-toolbar-page'),
          );

    $wp_admin_bar->add_node($args);
    buildFaireDrop($wp_admin_bar);

    //add custom menu items
    $locations = array('mf-admin-bayarea-register-menu','mf-admin-newyork-register-menu','mf-admin-chicago-register-menu');
    foreach($locations as $location_id) {
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
                $args = array(
                    'id' => $menu_item->object_id,
                    'title' => $menu_item->title,
                    'href' => $menu_item->url,
                    'meta' => array('class' => 'my-toolbar-page'),
                    'parent' => 'mf_admin_parent_' . $faire
                );

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
  }
}

function buildFaireDrop(&$wp_admin_bar, $faire_id = null) {
  $args = array();
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
        'count'=>$row->count,
        'faire_name' => $row->faire_name,
        'form_ids'   => $row->form_ids
    );
  }

  //buid the menu
  foreach($menu_array as $key=>$menu) {
    $menuID = strtolower(str_replace(' ', '_', $key));

    //level 1 - Faire name
    array_push($args,array(
        'id' => 'mf_admin_parent_' . $menuID,
        'title' => $key,
        'meta' => array('class' => 'my-toolbar-page'),
        'parent' => 'mf_admin_parent'
    ));

    //level 2 - Faire year
    foreach($menu as $faire => $faireInfo) {
      array_push($args,array(
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

        array_push($args,array(
            'id' => 'mf_admin_child_' . $formRow->form_id,
            'title' => $formRow->title . ' (' . $formRow->count . ')',
            'href' => $adminURL,
            'meta' => array('class' => 'my-toolbar-page'),
            'parent' => 'mf_admin_parent_' . $faire));

        //Level 4 - entry status
        $statusSql = "SELECT wp_rg_lead_detail.id,value,count(*)as count FROM `wp_rg_lead_detail` join wp_gf_entry on wp_gf_entry.id = lead_id "
                . " WHERE wp_gf_entry.form_id = " . $formRow->form_id . "    AND wp_rg_lead_detail.field_number = 303 and status = 'active' group by value";

        foreach ($wpdb->get_results($statusSql) as $statusRow) {
          array_push($args,array(
              'id'      => 'mf_admin_subchild_' . $statusRow->id,
              'title'   => $statusRow->value . ' (' . $statusRow->count . ')',
              'href'    => $adminURL . '&sort=0&dir=DESC&' . urlencode('filterField[]') . '=303|is|' . str_replace(' ', '+', $statusRow->value),
              'meta'    => array('class' => 'my-toolbar-page'),
              'parent' => 'mf_admin_child_' . $formRow->form_id));
        }
      }

      //add scheduling link
      if (!(isset($faire_id))) {
        $args[] = array(
            'id' => 'mf_admin_scheduling_' . $faire,
            'title' => 'Scheduling',
            'href' => '/mfscheduler/' . $faire,
            'meta' => array('class' => 'my-toolbar-page'),
            'parent' => 'mf_admin_parent_' . $faire);
      }
    }
  }

  //add RMT link
  if (!(isset($faire_id))) {
    $args[] = array(
        'id' => 'mf_admin_parent_rmt',
        'title' => 'RMT',
        'href' => 'https://makerfaire.com/resource-mgmt/',
        'meta' => array('class' => 'my-toolbar-page'),
        'target' => '_blank',
        'parent' => 'mf_admin_parent'
    );
  }

  //add the items to the menu now
  foreach($args as $arg){
    $wp_admin_bar->add_node($arg);
  }
}

// add a custom menu item to the Form Settings page menu for Tasks
add_filter( 'gform_form_settings_menu', 'mf_tasks_settings_menu_item' );
function mf_tasks_settings_menu_item( $menu_items ) {
  $menu_items[] = array(
    'name' => 'mf_tasks_settings_page',
    'label' => __( 'Tasks' ),
    'query' => array( 'tid' => null )
  );
  return $menu_items;
}

// handle displaying content for tasks page
add_action( 'gform_form_settings_page_mf_tasks_settings_page', 'mf_tasks_settings_page' );
function mf_tasks_settings_page() {
  require_once( TEMPLATEPATH.'/classes/GFTask.php' );
  //page header loaded in below function because admin messages were not yet available to the header to display
	GFTask::task_page();
}

/* Displays faire sign code */
function build_fsp_gsp(){
  require_once( TEMPLATEPATH.'/adminPages/other_form_download.php' );
}