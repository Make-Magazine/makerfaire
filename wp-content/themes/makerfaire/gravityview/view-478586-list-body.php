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

/**
 * @action `gravityview_list_body_before` Tap in before the entry loop has been displayed
 * @param GravityView_View $this The GravityView_View instance
 */
do_action( 'gravityview_list_body_before', $this );

//set form id to search all forms for maker entries, not just the form defined in the view
$form_id = 0;

//get current logged in user information
global $current_user;
get_currentuserinfo();
global $user_ID;global $user_email;

/*
 * Retrieve all entries for this user - created with user email as the contact email and created by this user id
 */
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
$total = count($entries);

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
    ?>
    <div id="gv_list_<?php echo $entry['id']; ?>" class="maker-admin">
    <?php
    $form = GFAPI::get_form( $entry['form_id'] );
    $form_type = (isset($form['form_type'])?'<p>'.$form['form_type'].':&nbsp;</p>':'');
    if($form_type != 'Other' && $form_type != ''){
      //skip this entry
    }
		$this->setCurrentEntry( $entry );
    //set status color
    if($entry['303']=='Accepted'){
        $statusBlock = 'greenStatus';
    }else{
        $statusBlock = 'greyStatus';
    }
    ?>
		<div id="gv_list_<?php echo $entry['id']; ?>" class="<?php echo esc_attr( apply_filters( 'gravityview_entry_class', 'gv-list-view', $entry, $this ) ); ?>">

		<?php

		/**
		 * @action `gravityview_entry_before` Tap in before the the entry is displayed, inside the entry container
		 * @param array $entry Gravity Forms Entry array
		 * @param GravityView_View $this The GravityView_View instance
		 */
		do_action( 'gravityview_entry_before', $entry, $this );
		?>

		<?php if ( $this->getField('directory_list-title') || $this->getField('directory_list-subtitle') ) { ?>
			<?php
			/**
			 * @action `gravityview_entry_title_before` Tap in before the the entry title is displayed
			 * @param array $entry Gravity Forms Entry array
			 * @param GravityView_View $this The GravityView_View instance
			 */
			do_action( 'gravityview_entry_title_before', $entry, $this );

			?>
			<div class="gv-list-view-title">
        <div class="statusBox <?php echo $statusBlock;?>">

				<?php if ( $this->getField('directory_list-title') ) {
					$i          = 0;
					$title_args = array(
						'entry'      => $entry,
						'form'       => $this->getForm(),
						'hide_empty' => $this->getAtts( 'hide_empty' ),
					);

					foreach ( $this->getField( 'directory_list-title' ) as $field ) {
						$title_args['field'] = $field;

						// The first field in the title zone is the main
						if ( $i == 0 ) {
							$title_args['markup'] = '<h3 id="{{ field_id }}" class="{{class}}">{{label}}{{value}}</h3>';
							echo gravityview_field_output( $title_args );
							unset( $title_args['markup'] );
						} else {
							$title_args['wpautop'] = true;
							echo gravityview_field_output( $title_args );
						}

						$i ++;
					}
				}
        ?>
        </div> <!--end status box-->
        <?php
				$this->renderZone('subtitle', array(
					'markup' => '<h4 id="{{ field_id }}" class="{{class}}">{{label}}{{value}}</h4>',
					'wrapper_class' => 'gv-list-view-subtitle',
				));
			?>
			</div>

			<?php

			/**
			 * @action `gravityview_entry_title_after` Tap in after the title block
			 * @param array $entry Gravity Forms Entry array
			 * @param GravityView_View $this The GravityView_View instance
			 */
			do_action( 'gravityview_entry_title_after', $entry, $this );

			?>

		<?php } ?>

		<div class="gv-grid gv-list-view-content">

			<?php

				/**
				 * @action `gravityview_entry_content_before` Tap in inside the View Content wrapper <div>
				 * @param array $entry Gravity Forms Entry array
				 * @param GravityView_View $this The GravityView_View instance
				 */
				do_action( 'gravityview_entry_content_before', $entry, $this );

				$this->renderZone('image', 'wrapper_class="gv-grid-col-1-3 gv-list-view-content-image"');

				$this->renderZone('description', array(
					'wrapper_class' => 'gv-grid-col-2-3 gv-list-view-content-description',
					'label_markup' => '<h4>{{label}}</h4>',
					'wpautop'      => true
				));

				$this->renderZone('content-attributes', array(
					'wrapper_class' => 'gv-list-view-content-attributes',
					'markup'     => '<p id="{{ field_id }}" class="{{class}}">{{label}}{{value}}</p>'
				));

				/**
				 * @action `gravityview_entry_content_after` Tap in at the end of the View Content wrapper <div>
				 * @param array $entry Gravity Forms Entry array
				 * @param GravityView_View $this The GravityView_View instance
				 */
				do_action( 'gravityview_entry_content_after', $entry, $this );

			?>

		</div>

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
    </div>
	<?php }

} // End if has entries

/**
 * @action `gravityview_list_body_after` Tap in after the entry loop has been displayed
 * @param GravityView_View $this The GravityView_View instance
 */
do_action( 'gravityview_list_body_after', $this );
