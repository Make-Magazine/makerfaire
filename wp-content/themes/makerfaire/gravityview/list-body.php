<?php
/**
 * @file templates/list-body.php
 *
 * Display the entries loop when using a list layout
 *
 * @package GravityView
 * @subpackage GravityView/templates
 *
 * @global GravityView_View $this
 */

/*
 * Retrieve all entries for this user - created with user email as the contact email and created by this user id
 */
$gravityview_view = GravityView_View::getInstance();

// Get the settings for the View ID
$view_settings = gravityview_get_template_settings( $gravityview_view->getViewId() );
$view_settings['page_size'] = $gravityview_view->getCurrentFieldSetting('page_size');

$form_id = 0;

global $current_user;
get_currentuserinfo();
global $user_ID;global $user_email;

// Prepare paging criteria
$criteria['paging'] = array(
  'offset' => 0,
  'page_size' => $view_settings['page_size']
);

//pull by user id or user email
$criteria['search_criteria'] = array(
  'status'        => 'active',
  'field_filters' => array(
    'mode' => 'any',
    array(
      'key'   => '98',
      'value' => $user_email,
      'operator' => 'like'
    ),
    array(
      'key' => 'created_by',
      'value' => $user_ID,
      'operator' => 'is'
    )
  )
);

$entries = GFAPI::get_entries( $form_id, $criteria['search_criteria'] );

/**
 * @action `gravityview_list_body_before` Tap in before the entry loop has been displayed
 * @param GravityView_View $this The GravityView_View instance
 */
do_action( 'gravityview_list_body_before', $this );
$total = count($entries);

global $wpdb;
//find current active forms for the copy entry feature
$faireSQL = "SELECT form.id, form.title FROM wp_rg_form form, `wp_mf_faire` "
  . " WHERE start_dt <= CURDATE() and end_dt >= CURDATE() and "
  . " FIND_IN_SET (form.id, wp_mf_faire.form_ids)> 0";
$faires = $wpdb->get_results($faireSQL);
$formArr = array();
foreach($faires as $faire) {
  $formArr[] = array($faire->id,$faire->title);
}

// There are no entries.
if(!$total or !( is_user_logged_in() )):
?>
  <div class="gv-list-view gv-no-results">
    <div class="gv-list-view-title">
      <h3><?php echo gv_no_results(); ?></h3>
    </div>
  </div>
<?php
// There are entries. Loop through them.
else:
  foreach ($entries as $entry):
    $this->setCurrentEntry($entry);
    $form = GFAPI::get_form( $entry['form_id'] );
    $form_type = (isset($form['form_type'])?'<p>'.$form['form_type'].':&nbsp;</p>':'');
    // If "form_type"
    if($form_type != 'Other' && $form_type != ''):
    ?>
      <?php include TEMPLATEPATH. '/gravityview/manage-faire-entries/faire-entry.php'; ?>
    <?php
    endif; // End if "form_type"
  endforeach;
  ?>
<?php include_once TEMPLATEPATH. '/gravityview/manage-faire-entries/faire-entry-modals.php'; ?>

<?php
endif; // End if has entries

/**
 * @action `gravityview_list_body_after` Tap in after the entry loop has been displayed
 * @param GravityView_View $this The GravityView_View instance
 */
do_action('gravityview_list_body_after', $this);
