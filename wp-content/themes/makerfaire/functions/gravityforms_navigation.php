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
  $is_barnesandnoble = ( in_array('barnesandnoble', (array) $user->roles) );
  if ($is_barnesandnoble) {
    remove_menu_page('gf_edit_forms');
  }
}

//add new submenu for our custom built list page
add_filter('gform_addon_navigation', 'add_menu_item');

function add_menu_item($menu_items) {
  $menu_items[] = array("name" => "mf_entries", "label" => "Entries", "callback" => "entries_list", "permission" => "edit_posts");
  $menu_items[] = array("name" => "mf_fsp", "label" => "Download FSP", "callback" => "build_pdf_fsp", "permission" => "edit_posts");
  $menu_items[] = array("name" => "mf_gsp", "label" => "Download GSP", "callback" => "build_pdf_gsp", "permission" => "edit_posts");
  $menu_items[] = array("name" => "mf_fairesign", "label" => "Faire Signs", "callback" => "build_faire_signs", "permission" => "edit_posts");

  return $menu_items;
}

//add new entries list for navigation 
function entries_list() {
  $view = rgget('view');
  $lead_id = rgget('lid');

  if ($view == 'mfentry' && ( rgget('lid') || !rgblank(rgget('pos')) )) {
    //require_once( GFCommon::get_base_path() . '/entry_detail.php' );
    include_once TEMPLATEPATH . '/classes/entry_detail_makerfaire.php';
    GFEntryDetail::lead_detail_page();
  } else if ($view == 'entries' || empty($view)) {
    include_once TEMPLATEPATH . '/classes/entry_list_makerfaire.php';
    if (!class_exists('GFEntryList')) {
      require_once(GFCommon::get_base_path() . "/entry_list.php");
    }
    GFEntryList::all_leads_page();
  } else {
    $form_id = rgget('id');
    do_action('gform_entries_view', $view, $form_id, $lead_id);
  }
}

//remove old entries navigation
function remove_menu_links() {
  global $submenu;
  if (isset($submenu['gf_edit_forms'])) {
    foreach ($submenu['gf_edit_forms'] as $key => $item) {
      if (in_array('gf_entries', $item)) {
        unset($submenu['gf_edit_forms'][$key]);
      }
    }
  }
}

add_action('admin_menu', 'remove_menu_links', 9999);

/**
 * Redirect gravity form admin pages to the new makerfaire specific admin pages
 */
function redirect_gf_admin_pages() {
  global $pagenow;

  /* Check current admin page. */
  if ($pagenow == 'admin.php') {
    if (isset($_GET['page']) && $_GET['page'] == 'gf_entries') {
      //include any parameters in the return URL
      $returnURL = '';
      foreach ($_GET as $key => $param) {
        if ($key != 'page') {
          if ($key == 'view' && $param == 'entry')
            $param = 'mfentry';
          $returnURL .= '&' . $key . '=' . $param;
        }
      }
      wp_redirect(admin_url('admin.php') . "?page=mf_entries" . $returnURL);
      exit;
    }
  }
}

add_action('admin_menu', 'redirect_gf_admin_pages');


/* Styles to adjust admin screen go here */
add_action('admin_head', 'remove_gf_form_toolbar');

function remove_gf_form_toolbar() {
  ?>
  <style>
    #gf_form_toolbar li.gf_form_toolbar_editor {
      display:none;
    }
    #gf_form_toolbar li.gf_form_toolbar_settings {
      display:none;
    }
    #notifications_container {
      /*display:none;*/
    }

    #entry_form div#submitdiv {
      display:none;
    }
    .detail-view-print {
      margin-bottom: 20px;
    }
  </style>
  <?php

}

add_action('admin_bar_menu', 'toolbar_link_to_mypage', 999);

function toolbar_link_to_mypage($wp_admin_bar) {

  $user = wp_get_current_user();
  $is_national = ( in_array('national', (array) $user->roles) );
  $is_barnesandnoble = ( in_array('barnesandnoble', (array) $user->roles) );
  $locations = get_registered_nav_menus();
  $menus = wp_get_nav_menus();
  $menu_locations = get_nav_menu_locations();

  if ($is_national) {

    $location_id = 'mf-admin-national-register-menu';
    if (isset($menu_locations[$location_id])) {
      foreach ($menus as $menu) {
        // If the ID of this menu is the ID associated with the location we're searching for
        if ($menu->term_id == $menu_locations[$location_id]) {
          // This is the correct menu
          $menu_items = wp_get_nav_menu_items($menu);

          $args = array(
              'id' => 'mf_admin_parent',
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
                  'parent' => 'mf_admin_parent_' . $faire
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
              'id' => 'mf_admin_parent',
              'title' => 'Barnes And Noble Admin',
              'meta' => array('class' => 'my-toolbar-page'),
          );

          $wp_admin_bar->add_node($args);
          buildFaireDrop($wp_admin_bar, 'BN16');

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
  else {
    // bay area
    $location_id = 'mf-admin-bayarea-register-menu';
    if (isset($menu_locations[$location_id])) {
      foreach ($menus as $menu) {
        // If the ID of this menu is the ID associated with the location we're searching for
        if ($menu->term_id == $menu_locations[$location_id]) {
          // This is the correct menu
          $menu_items = wp_get_nav_menu_items($menu);

          $args = array(
              'id' => 'mf_admin_parent',
              'title' => 'MF Admin',
              'meta' => array('class' => 'my-toolbar-page'),
          );

          $wp_admin_bar->add_node($args);
          buildFaireDrop($wp_admin_bar);

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
    //new york
    $location_id = 'mf-admin-newyork-register-menu';
    if (isset($menu_locations[$location_id])) {
      foreach ($menus as $menu) {
        // If the ID of this menu is the ID associated with the location we're searching for
        if ($menu->term_id == $menu_locations[$location_id]) {
          // This is the correct menu
          $menu_items = wp_get_nav_menu_items($menu);
          $wp_admin_bar->add_node($args);

          foreach ((array) $menu_items as $key => $menu_item) {
            if ($menu_item->menu_item_parent == 0) {
              //build faire specific admin
              $faire = $menu_item->attr_title;
            } else {
              $args = array(
                  'id' => $menu_item->object_id,
                  'title' => $menu_item->title,
                  'href' => $menu_item->url,
                  'meta' => array('class' => 'my-toolbar-page'),
                  'parent' => 'mf_admin_parent_' . $faire
              );
            }
            $wp_admin_bar->add_node($args);
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

function buildFaireDrop($wp_admin_bar, $faire_id = null) {
  //build faire drop downs
  global $wpdb;

  $sql = (isset($faire_id)) ? "select *, count(*) as count from wp_mf_faire, wp_rg_lead
                where FIND_IN_SET (wp_rg_lead.form_id,wp_mf_faire.form_ids)> 0 and
                        wp_rg_lead.status = 'active' and faire='$faire_id'
                group by wp_mf_faire.faire
                ORDER BY `wp_mf_faire`.`start_dt` DESC" :
          "select *, count(*) as count from wp_mf_faire, wp_rg_lead
                where FIND_IN_SET (wp_rg_lead.form_id,wp_mf_faire.form_ids)> 0 and
                        wp_rg_lead.status = 'active'
                group by wp_mf_faire.faire
                ORDER BY `wp_mf_faire`.`start_dt` DESC";


  foreach ($wpdb->get_results($sql) as $row) {
    //parent menu
    $args = array(
        'id' => 'mf_admin_parent_' . $row->faire,
        'title' => $row->faire_name . ' (' . $row->count . ')',
        'meta' => array('class' => 'my-toolbar-page'),
        'href' => admin_url('admin.php') . '?page=mf_entries&faire=' . $row->faire,
        'parent' => 'mf_admin_parent'
    );
    $wp_admin_bar->add_node($args);

    //build submenu, with form names
    $formSQL = "
            SELECT form_id,form.title,count(*) as count
                    FROM `wp_rg_lead` join wp_rg_form form
                    WHERE form.id = form_id and `form_id` IN (" . $row->form_ids . ") and status = 'active'
                    group by form_id
                    ORDER BY FIELD(form_id, " . $row->form_ids . ")";

    foreach ($wpdb->get_results($formSQL) as $formRow) {
      $adminURL = admin_url('admin.php') . "?page=mf_entries&view=entries&id=" . $formRow->form_id;

      $args = array(
          'id' => 'mf_admin_child_' . $formRow->form_id,
          'title' => $formRow->title . ' (' . $formRow->count . ')',
          'href' => $adminURL,
          'meta' => array('class' => 'my-toolbar-page'),
          'parent' => 'mf_admin_parent_' . $row->faire);
      $wp_admin_bar->add_node($args);

      //build submenu of entry status
      $statusSql = "SELECT wp_rg_lead_detail.id,value,count(*)as count FROM `wp_rg_lead_detail` join wp_rg_lead on wp_rg_lead.id = lead_id WHERE wp_rg_lead.form_id = " . $formRow->form_id . "    AND wp_rg_lead_detail.field_number = 303 and status = 'active' group by value";

      foreach ($wpdb->get_results($statusSql) as $statusRow) {
        $args = array(
            'id' => 'mf_admin_subchild_' . $statusRow->id,
            'title' => $statusRow->value . ' (' . $statusRow->count . ')',
            'href' => $adminURL . '&sort=0&dir=DESC&' . urlencode('filterField[]') . '=303|is|' . str_replace(' ', '+', $statusRow->value),
            'meta' => array('class' => 'my-toolbar-page'),
            'parent' => 'mf_admin_child_' . $formRow->form_id);
        $wp_admin_bar->add_node($args);
      }
    }
  }
  //add scheduling link
  if (!(isset($faire_id))) {
    $args = array(
        'id' => 'mf_admin_subchild_' . $statusRow->id,
        'title' => 'Scheduling',
        'href' => 'http://makerfaire.com/wp-content/applications/scheduler/makerfaire-scheduling.php?faire_id=' . $row->faire,
        'meta' => array('class' => 'my-toolbar-page'),
        'parent' => 'mf_admin_parent_' . $row->faire);
    $wp_admin_bar->add_node($args);


    $args = array(
        'id' => 'mf_admin_parent_rmt',
        'title' => 'RMT',
        'href' => 'http://makerfaire.com/resource-mgmt/',
        'meta' => array('class' => 'my-toolbar-page'),
        'target' => '_blank',
        'parent' => 'mf_admin_parent'
    );

    $wp_admin_bar->add_node($args);
  }

  return $wp_admin_bar;
}
