<?php include_once TEMPLATEPATH. '/gravityview/manage-faire-entries/faire-entry-js.php'; ?>
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
if(!$total or !( is_user_logged_in() )) {
?>
  <div class="gv-list-view gv-no-results">
    <div class="gv-list-view-title">
      <h3><?php echo gv_no_results(); ?></h3>
    </div>
  </div>
<?php
// There are entries. Loop through them.
} else {
  include_once TEMPLATEPATH. '/gravityview/manage-faire-entries/header.php';
  foreach ($entries as $entry) {
    $this->setCurrentEntry($entry);
    $form = GFAPI::get_form( $entry['form_id'] );
    $form_type = (isset($form['form_type'])?$form['form_type'].':':'');
    // If "form_type"
    if($form_type != 'Other' && $form_type != '') {
    ?>
      <div id="gv_list_<?php echo $entry['id']; ?>" class="maker-admin-list-wrp">
        <?php
        /**
         * @action `gravityview_entry_before` Tap in before the the entry is displayed, inside the entry container
         * @param array $entry Gravity Forms Entry array
         * @param GravityView_View $this The GravityView_View instance
         */
        do_action( 'gravityview_entry_before', $entry, $this );
        
        // "If directory_list-title or directory_list-subtitle"
        if ( $this->getField('directory_list-title') || $this->getField('directory_list-subtitle') ){
          /**
           * @action `gravityview_entry_title_before` Tap in before the the entry title is displayed
           * @param array $entry Gravity Forms Entry array
           * @param GravityView_View $this The GravityView_View instance
           */
          do_action( 'gravityview_entry_title_before', $entry, $this );
          ?>
          <div class="gv-list-view-title-maker-entry">
            <?php
            $entryData = array();
            $links = '';
            if ($this->getField('directory_list-title')) {
              $i = 0;
              $title_args = array(
                'entry' => $entry,
                'form' => $this->getForm(),
                'hide_empty' => $this->getAtts('hide_empty'),
              );

              //set status color
              if ($entry['303'] == 'Accepted') {
                $statusBlock = 'greenStatus';
              } else {
                $statusBlock = 'greyStatus';
              }

              foreach($this->getField('directory_list-title') as $field) {
                $title_args['field'] = $field;
                switch ($field['id']) {
                  case '22':
                    $title_args['wpautop'] = false;
                    break;
                  case 'edit_link':
                  case 'cancel_link':
                    //do not display if entry is cancelled
                    if ($entry['303'] != 'Cancelled') {
                      $title_args['markup'] = '{{value}}';
                      $links.=gravityview_field_output($title_args);
                    }
                    break;
                  case 'copy_entry':
                  case 'entry_link':
                    $title_args['markup'] = '{{value}}';
                    $links.=gravityview_field_output($title_args);
                    break;
                  case 'delete_entry':
                    if ($entry['303'] == 'Proposed' || $entry['303'] == 'In Progress') {
                      $title_args['markup'] = '{{value}}';
                      $links.=gravityview_field_output($title_args);
                    }
                    break;
                  default:
                    $title_args['markup'] = '{{label}} {{value}}';
                }
                $entryData[$field['id']] = gravityview_field_output($title_args);
                unset($title_args['markup']);
              }
            }
            if(!empty($entryData)) {
            ?>
              <div class="entryImg">
                <?php echo (isset($entry['22'])&& $entry['22']!=''?$entryData['22']:'<img src="/wp-content/uploads/2015/12/no-image.png" />');?>
              </div>
              <div class="entryData">
                <div class="statusBox <?php echo $statusBlock;?>">
                  <div class="pull-left"><?php echo $entryData['faire_name'];?></div>
                  <div class="pull-right statusText"><?php echo $entryData['303'];?></div>
                </div>
                <h3 class="entry-title"><?php echo $entryData['151'];?></h3>
                <div class="clear pull-left entryID latReg">
                  <?php echo $form_type.' '.$entryData['id'];?>
                </div>
                <div class="clear links latReg">
                  <div class="submit-date"><?php echo $entryData['date_created'];?></div>
                  <div class="entry-action-buttons">
                    <?php if (true) { ?>
                      <button type="button" class="btn btn-default btn-no-border notifications-button"
                        data-toggle="popover" data-html="true"
                        data-placement="bottom" data-trigger="focus"
                        data-content='<div class="manage-entry-popover"><?php echo '<a>Pay Commercial Maker Fee etc</a>';?></div>'>
                        NOTIFICATIONS
                        <span class="fa-stack fa-lg">
                          <i class="fa fa-circle"></i>
                          <span class="notification-counter"><?php echo '3';?></span>
                        </span>
                      </button>
                    <?php } ?>
                    <button type="button" class="btn btn-default btn-no-border manage-button"
                      data-toggle="popover" data-html="true"
                      data-placement="bottom" data-trigger="focus"
                      data-content='<div class="manage-entry-popover"><?php echo $links;?></div>'>
                      MANAGE
                      <i class="fa fa-cog"></i>
                    </button>
                  </div>
                </div>
              </div>
            <?php
            }
            $this->renderZone('subtitle', array(
              'markup' => '<h4 id="{{ field_id }}" class="{{class}}">{{label}}{{value}}</h4>',
              'wrapper_class' => 'gv-list-view-subtitle',
            ));
            ?>
          </div>
          <div class="clear"></div>
          <?php
          /**
           * @action `gravityview_entry_title_after` Tap in after the title block
           * @param array $entry Gravity Forms Entry array
           * @param GravityView_View $this The GravityView_View instance
           */
          do_action( 'gravityview_entry_title_after', $entry, $this );
        }; // End "If directory_list-title or directory_list-subtitle"

        // Is the footer configured?
        if ( $this->getField('directory_list-footer-left') || $this->getField('directory_list-footer-right') ) {
          /**
           * @action `gravityview_entry_footer_before` Tap in before the footer wrapper
           * @param array $entry Gravity Forms Entry array
           * @param GravityView_View $this The GravityView_View instance
           */
          do_action( 'gravityview_entry_footer_before', $entry, $this );
          ?>
          <div class="gv-grid gv-list-view-footer">
            <div class="gv-grid-col-1-2 gv-left">
              <?php $this->renderZone('footer-left'); ?>
            </div>
            <div class="gv-grid-col-1-2 gv-right">
              <?php $this->renderZone('footer-right'); ?>
            </div>
          </div>
          <?php
          /**
           * @action `gravityview_entry_footer_after` Tap in after the footer wrapper
           * @param array $entry Gravity Forms Entry array
           * @param GravityView_View $this The GravityView_View instance
           */
          do_action( 'gravityview_entry_footer_after', $entry, $this );
        }; // End if footer is configured

        /**
         * @action `gravityview_entry_after` Tap in after the entry has been displayed, but before the container is closed
         * @param array $entry Gravity Forms Entry array
         * @param GravityView_View $this The GravityView_View instance
         */
        do_action( 'gravityview_entry_after', $entry, $this );
        ?>
      </div>
      <hr/>
    <?php
    }; // End if "form_type"
  };
  ?>
<?php include_once TEMPLATEPATH. '/gravityview/manage-faire-entries/faire-entry-modals.php'; ?>

<?php
}; // End if has entries

/**
 * @action `gravityview_list_body_after` Tap in after the entry loop has been displayed
 * @param GravityView_View $this The GravityView_View instance
 */
do_action('gravityview_list_body_after', $this);
