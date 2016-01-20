<!-- Single faire entry for "list-body.php" -->
<hr/>
<div id="gv_list_<?php echo $entry['id']; ?>" class="maker-admin">
  <?php
  /**
   * @action `gravityview_entry_before` Tap in before the the entry is displayed, inside the entry container
   * @param array $entry Gravity Forms Entry array
   * @param GravityView_View $this The GravityView_View instance
   */
  do_action( 'gravityview_entry_before', $entry, $this );
  
  // "If directory_list-title or directory_list-subtitle"
  if ( $this->getField('directory_list-title') || $this->getField('directory_list-subtitle') ):
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
            case 'delete_entry':
              if ($entry['303'] == 'Proposed' || $entry['303'] == 'In Progress') {
                $title_args['markup'] = '{{value}}';
                $links.=gravityview_field_output($title_args);
              }
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
          <h3 class="title"><?php echo $entryData['151'];?></h3>
          <div class="clear pull-left entryID latReg"><?php echo $form_type.' '.$entryData['id'];?></div>
          <div class="clear links latReg">
            <div class="pull-left"><?php echo $entryData['date_created'];?></div>
            <button type="button" class="btn btn-default pull-right"
              data-toggle="popover"
              data-placement="bottom" data-trigger="focus"
              data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">
              popover
            </button>
            <div class="pull-right"><?php echo $links;?></div>
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
  endif; // End "If directory_list-title or directory_list-subtitle"

  // Is the footer configured?
  if ( $this->getField('directory_list-footer-left') || $this->getField('directory_list-footer-right') ):
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
  endif; // End if footer is configured

  /**
   * @action `gravityview_entry_after` Tap in after the entry has been displayed, but before the container is closed
   * @param array $entry Gravity Forms Entry array
   * @param GravityView_View $this The GravityView_View instance
   */
  do_action( 'gravityview_entry_after', $entry, $this );
  ?>
</div>
