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
foreach($faires as $faire){
    $formArr[] = array($faire->id,$faire->title);
}

// There are no entries.
if( ! $total or !( is_user_logged_in() )) {

	?>
	<div class="gv-list-view gv-no-results">
		<div class="gv-list-view-title">
			<h3><?php echo gv_no_results(); ?></h3>
		</div>
	</div>
	<?php

} else {

	// There are entries. Loop through them.
	foreach ( $entries as $entry ) {
		$this->setCurrentEntry( $entry );
                
                $form = GFAPI::get_form( $entry['form_id'] );                                    
                $form_type = (isset($form['form_type'])?'<p>'.$form['form_type'].':&nbsp;</p>':'');             
                if($form_type != 'Other' && $form_type != ''){

                ?>
                    <hr/>
                    <div id="gv_list_<?php echo $entry['id']; ?>" class="maker-admin">

                    <?php

                    /**
                     * @action `gravityview_entry_before` Tap in before the the entry is displayed, inside the entry container
                     * @param array $entry Gravity Forms Entry array
                     * @param GravityView_View $this The GravityView_View instance
                     */
                    do_action( 'gravityview_entry_before', $entry, $this );

                    ?>

		<?php if ( $this->getField('directory_list-title') || $this->getField('directory_list-subtitle') ): ?>

			<?php

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
                                if ( $this->getField('directory_list-title') ) {
                                    $i          = 0;
                                    $title_args = array(
                                            'entry'      => $entry,
                                            'form'       => $this->getForm(),
                                            'hide_empty' => $this->getAtts( 'hide_empty' ),
                                    );
                                                                                                                                   
                                    
                                    //set status color
                                    if($entry['303']=='Accepted'){
                                        $statusBlock = 'greenStatus';
                                    }else{
                                        $statusBlock = 'greyStatus';
                                    }
                                        
                                    foreach ( $this->getField( 'directory_list-title' ) as $field ) {                                               
                                            $title_args['field'] = $field;                                                   
                                            
                                            switch ($field['id']){                                                
                                                case '22':     
                                                    $title_args['wpautop'] = true;
                                                    break;                                                
                                                case 'delete_entry':    
                                                    if($entry['303']=='Proposed' || $entry['303']=='In Progress'){
                                                        $title_args['markup'] = '<span class="edit"><i class="fa fa-trash-o"></i>{{value}}</span>';
                                                        $links .=  gravityview_field_output( $title_args );                                                                                                        
                                                    }
                                                    break;
                                                case 'edit_link':                                                    
                                                    //do not display if entry is cancelled
                                                    if($entry['303']!='Cancelled'){
                                                        $title_args['markup'] = '<span class="edit"><i class="fa fa-pencil-square-o"></i>{{value}}</span>';
                                                        $links .=  gravityview_field_output( $title_args );                                                                                                        
                                                    }
                                                    break;
                                                case 'cancel_link':
                                                    //do not display if entry is already cancelled
                                                    if($entry['303']!='Cancelled'){
                                                        $title_args['markup'] = '<span class="edit"><i class="fa fa-ban"></i>{{value}}</span>';
                                                        $links .=  gravityview_field_output( $title_args );                                                                                                        
                                                    }
                                                    break;   
                                                case 'copy_entry':                                                    
                                                    $title_args['markup'] = '<span class="edit"><i class="fa fa-files-o"></i>{{value}}</span>';
                                                    $links .=  gravityview_field_output( $title_args );                                                                                                        
                                                    break;
                                                case 'entry_link':
                                                    $title_args['markup'] = '<span class="edit"><i class="fa fa-eye"></i>{{value}}</span>';
                                                    $links .=  gravityview_field_output( $title_args );                                                                                                        
                                                    break;
                                                default:                                                    
                                                    $title_args['markup'] = '{{label}} {{value}}';                                                    
                                            }
                                            $entryData[$field['id']] = gravityview_field_output( $title_args );
                                            unset( $title_args['markup'] );
                                    }
				}
                             
                            if(!empty($entryData)){
                                ?>
                            
                            <div class="entryImg"><?php echo (isset($entry['22'])&& $entry['22']!=''?$entryData['22']:'<img src="/wp-content/uploads/2015/12/no-image.png" />');?></div>
                            
                            <div class="entryData">
                                <div class="statusBox <?php echo $statusBlock;?>">
                                    <div class="fleft"> <?php echo $entryData['faire_name'];?></div>
                                    <div class="fright statusText"><?php echo $entryData['303'];?></div>      
                                </div>
                                <h3 class="title"><?php echo $entryData['151'];?></h3>                                
                                <div class="clear fleft entryID latReg"><?php echo $form_type.' '.$entryData['id'];?></div>                               
                                <div class="clear links latReg">
                                    <div class="fleft"><?php echo $entryData['date_created'];?></div>
                                    <div class="fright"><?php echo $links;?></div>
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

			?>

		<?php endif; ?>


		<?php

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

		} // End if footer is configured


		/**
		 * @action `gravityview_entry_after` Tap in after the entry has been displayed, but before the container is closed
		 * @param array $entry Gravity Forms Entry array
		 * @param GravityView_View $this The GravityView_View instance
		 */
		do_action( 'gravityview_entry_after', $entry, $this );

		?>

		</div>   
        <?php } ?>
	<?php } ?>
                <div class="modal" id="cancelEntry">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <h4 class="modal-title">Cancel <span id="projName"></span>, Exhibit ID: <span id="cancelEntryID" name="entryID"></span></h4>
                        </div>
                        <div class="modal-body">
                            <div id="cancelText">
                                <p>Sorry you can't make it. Why are you canceling?</p><br/>
                                <textarea rows="4" cols="50" name="cancelReason"></textarea>
                            </div>                    
                        <span id="cancelResponse"></span><br/>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-default" id="submitCancel">Submit</button>
                          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                      </div>
                    </div>
                </div>
                
                <!-- Modal to copy entry to a new form -->
                <div class="modal" id="copy_entry">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <h4 class="modal-title">Copy Exhibit ID: <span id="copyEntryID" name="entryID"></span></h4>
                        </div>
                        <div class="modal-body">
                  <?php if(!empty($formArr)){ ?>
                            <p>Please choose from the options below:</p><br/>                  
                  <select id="copy2Form">
                      <?php
                      
                      foreach($formArr as $availForm){
                          echo '<option value='.$availForm[0].'>'.$availForm[1].'</option>';
                      }
                      ?>
                  </select>
                  <?php }else{
                      echo 'No Open faires at the moment';
                  }
?>
                    <br/><span id="copyResponse"></span><br/>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-default" id="submitCopy">Submit</button>
                          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                      </div>
                    </div>
                  </div>
                <!--Modal to delete entry-->
                <div class="modal" id="deleteEntry">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <h4 class="modal-title">Delete <span id="delProjName"></span>, Exhibit ID: <span id="deleteEntryID" name="entryID"></span></h4>
                        </div>
                        <div class="modal-body">
                            <div id="deleteText">
                            <p>Are you sure you want to trash this entry? You can not reverse this action.</p> 
                            </div>                    
                        <span id="deleteResponse"></span><br/>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-default" id="submitDelete">Yes, delete it</button>
                          <button type="button" class="btn btn-default" id="cancelDelete" data-dismiss="modal">No, I'll keep it</button>
                          <button type="button" class="btn btn-default" id="closeDelete" data-dismiss="modal">Close</button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <?php
} // End if has entries

/**
 * @action `gravityview_list_body_after` Tap in after the entry loop has been displayed
 * @param GravityView_View $this The GravityView_View instance
 */
do_action( 'gravityview_list_body_after', $this );
