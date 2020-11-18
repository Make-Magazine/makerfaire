<?php

class GV_Advanced_Filter_Tests extends GV_UnitTestCase {

	private function _reset_context() {

		\GV\Mocks\Legacy_Context::reset();
		gravityview()->request = new \GV\Frontend_Request();

		global $post;
		$post = null;

		\GV\View::_flush_cache();

		set_current_screen( 'front' );

		wp_set_current_user( 0 );
	}

	/**
	 * @since 2.0.1
	 */
	private function _reset_users() {

		global $wpdb;

		// Delete all users
		$wpdb->query( "DELETE FROM {$wpdb->users}" );
		$wpdb->query( "DELETE FROM {$wpdb->usermeta}" );
	}

	/**
	 * Make sure the entries in question are accessible or not.
	 *
	 * @param array  $entries A key-value of entry ID and true/false depending on accessibility.
	 * @param int    $view_id The View ID.
	 * @param array  $filters The filters to apply.
	 * @param bool   $ordered Ordered or not (optional). Default: false
	 * @param string $message Test failure message (optional). Default: null
	 *
	 * @return array $entries The entries returned.
	 */
	private function assertEntries( $entries, $view_id, $filters, $ordered = false, $message = '' ) {

		update_post_meta( $view_id, '_gravityview_filters', $filters );
		$view     = \GV\View::by_id( $view_id );
		$_entries = $view->get_entries()->all();

		if ( ! $ordered ) {
			$this->assertCount( count( array_filter( $entries ) ), $_entries, $message );
		} else {
			// @todo implement ordered when needed
			$this->assertFalse( $ordered, '$ordered parameter not implemented' );
		}

		gravityview()->request                     = new \GV\Mock_Request();
		gravityview()->request->returns['is_view'] = $view = \GV\View::by_id( $view_id );

		foreach ( $entries as $entry_id => $allowed ) {
			gravityview()->request->returns['is_entry'] = $entry = \GV\GF_Entry::by_id( $entry_id );

			if ( ! $allowed ) {
				$this->assertContains( 'not allowed to view', $view::content( '' ), $message );
			} else {
				$this->assertNotContains( 'not allowed to view', $view::content( '' ), $message );
				$this->assertContains( 'Entry ID</span></th><td>' . $entry_id . '</td>', $view::content( '' ), $message );
			}
		}

		return $_entries;
	}

	private function get_view( $form = 'complete.json', $overrides = array() ) {

		$form = $this->factory->form->import_and_get( $form );

		global $post;
		$post = $this->factory->view->create_and_get( array_merge( array(
			'form_id'     => $form['id'],
			'template_id' => 'table',
			'settings'    => array(
				'show_only_approved' => false,
			),
			'fields'      => array(
				'directory_table-columns' => array(
					wp_generate_password( 4, false ) => array(
						'id'    => 'id',
						'label' => 'Entry ID',
					),
				),
				'single_table-columns'    => array(
					wp_generate_password( 4, false ) => array(
						'id'    => 'id',
						'label' => 'Entry ID',
					),
				),
			),
		), $overrides ) );

		return \GV\View::from_post( $post );
	}

	public function test_text_filters() {

		$this->_reset_context();

		$view    = $this->get_view();
		$form_id = $view->form->ID;

		$entry = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'16'      => 'hello',
		) );
		$hello = $entry['id'];

		$entry       = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'16'      => 'hello world',
		) );
		$hello_world = $entry['id'];

		$entry = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'16'      => 'world',
		) );
		$world = $entry['id'];

		$this->assertEntries(
			array( $hello => true, $hello_world => true, $world => true ),
			$view->ID, array()
		);

		$this->assertEntries(
			array( $hello => true, $hello_world => true, $world => true ),
			$view->ID, array( 'mode' => 'all' )
		);

		$this->assertEntries(
			array( $hello => false, $hello_world => false, $world => false ),
			$view->ID, array( 'mode' => 'all', array( 'key' => '16', 'operator' => 'is', 'value' => 'not hello' ) )
		);

		$this->assertEntries(
			array( $hello => true, $hello_world => false, $world => false ),
			$view->ID, array( 'mode' => 'all', array( 'key' => '16', 'operator' => 'is', 'value' => 'hello' ) )
		);

		$this->assertEntries(
			array( $hello => true, $hello_world => true, $world => false ),
			$view->ID, array( 'mode' => 'all', array( 'key' => '16', 'operator' => 'contains', 'value' => 'hello' ) )
		);

		$this->_reset_context();
	}

	public function test_number_filters() {

		$this->_reset_context();

		$view    = $this->get_view();
		$form_id = $view->form->ID;

		$entry      = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'9'       => 9.3,
		) );
		$nine_three = $entry['id'];

		$entry    = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'9'       => 9.1,
		) );
		$nine_one = $entry['id'];

		$entry  = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'9'       => 12,
		) );
		$twelve = $entry['id'];

		$this->assertEntries(
			array( $nine_three => true, $nine_one => true, $twelve => true ),
			$view->ID, array()
		);

		$this->assertEntries(
			array( $nine_three => true, $nine_one => true, $twelve => true ),
			$view->ID, array( 'mode' => 'all' )
		);

		$this->assertEntries(
			array( $nine_three => false, $nine_one => false, $twelve => false ),
			$view->ID, array( 'mode' => 'all', array( 'key' => '9', 'operator' => 'is', 'value' => '9' ) )
		);

		$this->assertEntries(
			array( $nine_three => false, $nine_one => true, $twelve => false ),
			$view->ID, array( 'mode' => 'all', array( 'key' => '9', 'operator' => 'is', 'value' => '9.1' ) )
		);

		$this->assertEntries(
			array( $nine_three => true, $nine_one => true, $twelve => false ),
			$view->ID, array( 'mode' => 'all', array( 'key' => '9', 'operator' => 'contains', 'value' => '9' ) )
		);

		$this->assertEntries(
			array( $nine_three => true, $nine_one => true, $twelve => true ),
			$view->ID, array( 'mode' => 'all', array( 'key' => '9', 'operator' => '>', 'value' => '9' ) )
		);

		$this->assertEntries(
			array( $nine_three => false, $nine_one => true, $twelve => false ),
			$view->ID, array( 'mode' => 'all', array( 'key' => '9', 'operator' => '<', 'value' => '9.2' ) )
		);

		$this->assertEntries(
			array( $nine_three => true, $nine_one => false, $twelve => true ),
			$view->ID, array( 'mode' => 'all', array( 'key' => '9', 'operator' => '>', 'value' => '9.1' ) )
		);

		$this->_reset_context();
	}

	public function test_date_filters() {

		$this->_reset_context();

		$view    = $this->get_view();
		$form_id = $view->form->ID;

		$entry = $this->factory->entry->create_and_get( array(
			'form_id'      => $form_id,
			'status'       => 'active',
			'date_created' => date( 'Y-m-d 00:00:01', strtotime( 'today' ) ),
			'date_updated' => date( 'Y-m-d 00:00:01', strtotime( 'today' ) ),
			'payment_date' => date( 'Y-m-d 00:00:01', strtotime( 'today' ) ),
			'3'            => date( 'Y-m-d', strtotime( 'today' ) ),
		) );
		$today = $entry['id'];

		$entry    = $this->factory->entry->create_and_get( array(
			'form_id'      => $form_id,
			'status'       => 'active',
			'date_created' => date( 'Y-m-d 00:00:01', strtotime( 'tomorrow' ) ),
			'date_updated' => date( 'Y-m-d 00:00:01', strtotime( 'tomorrow' ) ),
			'payment_date' => date( 'Y-m-d 00:00:01', strtotime( 'tomorrow' ) ),
			'3'            => date( 'Y-m-d', strtotime( 'tomorrow' ) ),
		) );
		$tomorrow = $entry['id'];

		$entry = $this->factory->entry->create_and_get( array(
			'form_id'      => $form_id,
			'status'       => 'active',
			'date_created' => date( 'Y-m-d 00:00:01', strtotime( '-1 year' ) ),
			'date_updated' => date( 'Y-m-d 00:00:01', strtotime( '-1 year' ) ),
			'payment_date' => date( 'Y-m-d 00:00:01', strtotime( '-1 year' ) ),
			'3'            => date( 'Y-m-d', strtotime( '-1 year' ) ),
		) );
		$ago   = $entry['id'];

		if ( $entry['date_updated'] ) {
			$test_date_updated = true; // 2.4+ only
		}

		$this->assertEntries(
			array( $today => true, $tomorrow => true, $ago => true ),
			$view->ID, array()
		);

		$this->assertEntries(
			array( $today => true, $tomorrow => true, $ago => true ),
			$view->ID, array( 'mode' => 'all' )
		);

		$this->assertEntries(
			array( $today => true, $tomorrow => false, $ago => false ),
			$view->ID, array(
				'mode' => 'all',
				array(
					'key'      => '3',
					'operator' => 'is',
					'value'    => date( 'Y-m-d', strtotime( 'today' ) ),
				),
			)
		);

		$this->assertEntries(
			array( $today => false, $tomorrow => true, $ago => true ),
			$view->ID, array(
				'mode' => 'all',
				array(
					'key'      => '3',
					'operator' => 'isnot',
					'value'    => date( 'Y-m-d', strtotime( 'today' ) ),
				),
			)
		);

		$this->assertEntries(
			array( $today => true, $tomorrow => true, $ago => false ),
			$view->ID, array(
				'mode' => 'all',
				array(
					'key'      => '3',
					'operator' => '>',
					'value'    => date( 'Y-m-d', strtotime( 'yesterday' ) ),
				),
			)
		);

		$this->assertEntries(
			array( $today => true, $tomorrow => true, $ago => true ),
			$view->ID, array(
				'mode' => 'all',
				array(
					'key'      => '3',
					'operator' => '>',
					'value'    => date( 'Y-m-d', strtotime( '-2 years' ) ),
				),
			)
		);

		$this->assertEntries(
			array( $today => false, $tomorrow => false, $ago => false ),
			$view->ID, array(
				'mode' => 'all',
				array(
					'key'      => '3',
					'operator' => '>',
					'value'    => date( 'Y-m-d', strtotime( '+1 year' ) ),
				),
			)
		);

		$this->assertEntries(
			array( $today => false, $tomorrow => false, $ago => true ),
			$view->ID, array(
				'mode' => 'all',
				array( 'key' => '3', 'operator' => '<', 'value' => date( 'Y-m-d', strtotime( 'today' ) ) ),
			)
		);

		foreach ( array( 'date_created', 'date_updated', 'payment_date' ) as $key ) {
			if ( empty( $test_date_updated ) ) {
				continue;
			}

			$this->assertEntries(
				array( $today => false, $tomorrow => false, $ago => true ),
				$view->ID, array( 'mode' => 'all', array( 'key' => $key, 'operator' => '<', 'value' => 'yesterday' ) )
			);

			$this->assertEntries(
				array( $today => false, $tomorrow => false, $ago => false ),
				$view->ID, array( 'mode' => 'all', array( 'key' => $key, 'operator' => '>', 'value' => '+2 days' ) )
			);

			$this->assertEntries(
				array( $today => true, $tomorrow => true, $ago => true ),
				$view->ID, array( 'mode' => 'all', array( 'key' => $key, 'operator' => '>', 'value' => '-2 years' ) )
			);
		}
	}

	public function test_created_by_filters() {

		$this->_reset_context();

		global $wpdb;
		$wpdb->query( "TRUNCATE $wpdb->users;" );

		$view    = $this->get_view();
		$form_id = $view->form->ID;

		$admin = $this->factory->user->create( array(
				'user_login' => md5( microtime() ),
				'user_email' => md5( microtime() ) . '@gravityview.tests',
				'role'       => 'administrator',
			)
		);

		$zack = $this->factory->user->create( array(
				'user_login' => md5( microtime() ),
				'user_email' => md5( microtime() ) . '@gravityview.tests',
				'role'       => 'subscriber',
			)
		);

		$vlad = $this->factory->user->create( array(
				'user_login' => md5( microtime() ),
				'user_email' => md5( microtime() ) . '@gravityview.tests',
				'role'       => 'subscriber',
			)
		);

		$entry    = $this->factory->entry->create_and_get( array(
			'form_id'    => $form_id,
			'status'     => 'active',
			'created_by' => $admin,
		) );
		$by_admin = $entry['id'];

		$entry   = $this->factory->entry->create_and_get( array(
			'form_id'    => $form_id,
			'status'     => 'active',
			'created_by' => $zack,
		) );
		$by_zack = $entry['id'];

		$entry   = $this->factory->entry->create_and_get( array(
			'form_id'    => $form_id,
			'status'     => 'active',
			'created_by' => $vlad,
		) );
		$by_vlad = $entry['id'];

		$this->assertEntries(
			array( $by_admin => true, $by_zack => true, $by_vlad => true ),
			$view->ID, array()
		);

		$this->assertEntries(
			array( $by_admin => true, $by_zack => true, $by_vlad => true ),
			$view->ID, array( 'mode' => 'all' )
		);

		$this->assertEntries(
			array( $by_admin => false, $by_zack => false, $by_vlad => false ),
			$view->ID, array(
				'mode' => 'all',
				array(
					'key'      => 'created_by',
					'operator' => 'is',
					'value'    => $zack + 100
					/** non-existent */
				),
			)
		);

		$this->assertEntries(
			array( $by_admin => true, $by_zack => true, $by_vlad => true ),
			$view->ID, array(
				'mode' => 'all',
				array(
					'key'      => 'created_by',
					'operator' => 'isnot',
					'value'    => $zack + 100
					/** non-existent */
				),
			)
		);

		$this->assertEntries(
			array( $by_admin => false, $by_zack => true, $by_vlad => false ),
			$view->ID, array( 'mode' => 'all', array( 'key' => 'created_by', 'operator' => 'is', 'value' => $zack ) )
		);

		$this->assertEntries(
			array( $by_admin => true, $by_zack => false, $by_vlad => true ),
			$view->ID, array( 'mode' => 'all', array( 'key' => 'created_by', 'operator' => 'isnot', 'value' => $zack ) )
		);

		$this->assertEntries(
			array( $by_admin => true, $by_zack => false, $by_vlad => false ),
			$view->ID, array( 'mode' => 'all', array( 'key' => 'created_by', 'operator' => 'is', 'value' => $admin ) )
		);

		$this->assertEntries(
			array( $by_admin => false, $by_zack => false, $by_vlad => false ),
			$view->ID, array(
				'mode' => 'all',
				array( 'key' => 'created_by', 'operator' => 'is', 'value' => 'created_by' ),
			)
		);

		$this->assertEntries(
			array( $by_admin => false, $by_zack => false, $by_vlad => false ),
			$view->ID, array(
				'mode' => 'all',
				array( 'key' => 'created_by', 'operator' => 'is', 'value' => 'created_by_or_admin' ),
			)
		);

		wp_set_current_user( $zack );

		$this->assertEntries(
			array( $by_admin => false, $by_zack => true, $by_vlad => false ),
			$view->ID, array(
				'mode' => 'all',
				array( 'key' => 'created_by', 'operator' => 'is', 'value' => 'created_by' ),
			)
		);

		$this->assertEntries(
			array( $by_admin => true, $by_zack => false, $by_vlad => true ),
			$view->ID, array(
				'mode' => 'all',
				array( 'key' => 'created_by', 'operator' => 'isnot', 'value' => 'created_by' ),
			)
		);

		wp_set_current_user( $admin );

		$this->assertEntries(
			array( $by_admin => false, $by_zack => true, $by_vlad => true ),
			$view->ID, array(
				'mode' => 'all',
				array( 'key' => 'created_by', 'operator' => 'isnot', 'value' => 'created_by' ),
			)
		);

		$this->assertEntries(
			array( $by_admin => true, $by_zack => true, $by_vlad => true ),
			$view->ID, array(
				'mode' => 'all',
				array( 'key' => 'created_by', 'operator' => 'is', 'value' => 'created_by_or_admin' ),
			)
		);

		$this->_reset_context();

		$this->assertEntries(
			array( $by_admin => false, $by_zack => false, $by_vlad => false ),
			$view->ID, array(
				'mode' => 'all',
				array( 'key' => 'created_by_user_role', 'operator' => 'is', 'value' => 'nobody' ),
			)
		);

		$this->assertEntries(
			array( $by_admin => true, $by_zack => true, $by_vlad => true ),
			$view->ID, array(
				'mode' => 'all',
				array( 'key' => 'created_by_user_role', 'operator' => 'isnot', 'value' => 'nobody' ),
			)
		);

		$this->assertEntries(
			array( $by_admin => false, $by_zack => false, $by_vlad => false ),
			$view->ID, array(
				'mode' => 'all',
				array( 'key' => 'created_by_user_role', 'operator' => 'is', 'value' => 'current_user' ),
			)
		);

		$this->assertEntries(
			array( $by_admin => true, $by_zack => true, $by_vlad => true ),
			$view->ID, array(
				'mode' => 'all',
				array( 'key' => 'created_by_user_role', 'operator' => 'isnot', 'value' => 'current_user' ),
			)
		);

		$this->assertEntries(
			array( $by_admin => false, $by_zack => true, $by_vlad => true ),
			$view->ID, array(
				'mode' => 'all',
				array( 'key' => 'created_by_user_role', 'operator' => 'is', 'value' => 'subscriber' ),
			)
		);

		$this->assertEntries(
			array( $by_admin => true, $by_zack => false, $by_vlad => false ),
			$view->ID, array(
				'mode' => 'all',
				array( 'key' => 'created_by_user_role', 'operator' => 'isnot', 'value' => 'subscriber' ),
			)
		);

		$this->assertEntries(
			array( $by_admin => true, $by_zack => false, $by_vlad => false ),
			$view->ID, array(
				'mode' => 'all',
				array( 'key' => 'created_by_user_role', 'operator' => 'is', 'value' => 'administrator' ),
			)
		);

		wp_set_current_user( $zack );

		$this->assertEntries(
			array( $by_admin => false, $by_zack => true, $by_vlad => true ),
			$view->ID, array(
				'mode' => 'all',
				array( 'key' => 'created_by_user_role', 'operator' => 'is', 'value' => 'current_user' ),
			)
		);

		$this->assertEntries(
			array( $by_admin => true, $by_zack => false, $by_vlad => false ),
			$view->ID, array(
				'mode' => 'all',
				array( 'key' => 'created_by_user_role', 'operator' => 'isnot', 'value' => 'current_user' ),
			)
		);

		wp_set_current_user( $admin );

		$this->assertEntries(
			array( $by_admin => true, $by_zack => false, $by_vlad => false ),
			$view->ID, array(
				'mode' => 'all',
				array( 'key' => 'created_by_user_role', 'operator' => 'is', 'value' => 'current_user' ),
			)
		);

		$this->_reset_context();
	}

	public function test_any_field_filters() {

		$this->_reset_context();

		$view    = $this->get_view( 'simple.json' );
		$form_id = $view->form->ID;

		$entry = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'1'       => 'hello',
			'2'       => '',
		) );
		$hello = $entry['id'];

		$entry       = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'1'       => 'hello world',
			'2'       => '40',
		) );
		$hello_world = $entry['id'];

		$entry = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'1'       => 'world',
			'2'       => '50',
		) );
		$world = $entry['id'];

		$entry      = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'1'       => 'WORLD',
			'2'       => '50',
		) );
		$world_caps = $entry['id'];

		$this->assertEntries(
			array( $hello => true, $hello_world => true, $world => true, $world_caps => true ),
			$view->ID, array()
		);

		$this->assertEntries(
			array( $hello => true, $hello_world => true, $world => true, $world_caps => true ),
			$view->ID, array( 'mode' => 'all' )
		);

		$this->assertEntries(
			array( $hello => false, $hello_world => false, $world => false, $world_caps => false ),
			$view->ID, array( 'mode' => 'all', array( 'key' => '0', 'operator' => 'is', 'value' => 'not hello' ) )
		);

		$this->assertEntries(
			array( $hello => true, $hello_world => false, $world => false, $world_caps => false ),
			$view->ID, array( 'mode' => 'all', array( 'key' => '0', 'operator' => 'is', 'value' => 'hello' ) )
		);

		$this->assertEntries(
			array( $hello => false, $hello_world => false, $world => true, $world_caps => true ),
			$view->ID, array( 'mode' => 'all', array( 'key' => '0', 'operator' => 'is', 'value' => 'WORLD' ) ),
			false,
			'Gravity Forms search is expected to be case INsensitive.'
		);

		$this->assertEntries(
			array( $hello => true, $hello_world => true, $world => false, $world_caps => false ),
			$view->ID, array( 'mode' => 'all', array( 'key' => '0', 'operator' => 'contains', 'value' => 'hello' ) )
		);

		$this->assertEntries(
			array( $hello => false, $hello_world => true, $world => true, $world_caps => true ),
			$view->ID, array( 'mode' => 'all', array( 'key' => '0', 'operator' => 'contains', 'value' => '0' ) )
		);

		$this->assertEntries(
			array( $hello => false, $hello_world => false, $world => true, $world_caps => true ),
			$view->ID, array( 'mode' => 'all', array( 'key' => '0', 'operator' => 'is', 'value' => '50' ) )
		);

		$this->_reset_context();
	}

	public function test_any_all_modes() {

		$this->_reset_context();

		$view    = $this->get_view( 'simple.json' );
		$form_id = $view->form->ID;

		$entry = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'1'       => 'hello',
			'2'       => '',
		) );
		$hello = $entry['id'];

		$entry       = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'1'       => 'hello world',
			'2'       => '40',
		) );
		$hello_world = $entry['id'];

		$entry = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'1'       => 'world',
			'2'       => '50',
		) );
		$world = $entry['id'];

		$this->assertEntries(
			array( $hello => true, $hello_world => true, $world => true ),
			$view->ID, array( 'mode' => 'any' )
		);

		$this->assertEntries(
			array( $hello => false, $hello_world => false, $world => false ),
			$view->ID, array(
				'mode' => 'all',
				array( 'key' => '1', 'operator' => 'is', 'value' => 'hello' ),
				array( 'key' => '2', 'operator' => 'is', 'value' => '40' ),
			)
		);

		$this->assertEntries(
			array( $hello => true, $hello_world => false, $world => false ),
			$view->ID, array(
				'mode' => 'any',
				array( 'key' => '1', 'operator' => 'is', 'value' => 'hello' ),
				array( 'key' => '2', 'operator' => 'is', 'value' => '70' ),
			)
		);

		$this->assertEntries(
			array( $hello => false, $hello_world => true, $world => false ),
			$view->ID, array(
				'mode' => 'all',
				array( 'key' => '1', 'operator' => 'contains', 'value' => 'hello' ),
				array( 'key' => '2', 'operator' => 'contains', 'value' => '40' ),
			)
		);

		$this->assertEntries(
			array( $hello => true, $hello_world => true, $world => false ),
			$view->ID, array(
				'mode' => 'any',
				array( 'key' => '1', 'operator' => 'contains', 'value' => 'hello' ),
				array( 'key' => '2', 'operator' => 'contains', 'value' => '40' ),
			)
		);

		$this->assertEntries(
			array( $hello => true, $hello_world => true, $world => true ),
			$view->ID, array(
				'mode' => 'any',
				array( 'key' => '1', 'operator' => 'contains', 'value' => 'hello' ),
				array( 'key' => '2', 'operator' => 'contains', 'value' => '50' ),
			)
		);

		$this->_reset_context();
	}

	public function test_cross_approval_filters() {

		$this->_reset_context();

		$view    = $this->get_view( 'simple.json' );
		$form_id = $view->form->ID;

		$view->settings->update( array( 'show_only_approved' => true ) );

		$entry = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'1'       => 'hello',
			'2'       => '',
		) );
		$hello = $entry['id'];

		$entry       = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'1'       => 'hello world',
			'2'       => '40',
		) );
		$hello_world = $entry['id'];

		$entry = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'1'       => 'world',
			'2'       => '50',
		) );
		$world = $entry['id'];

		$this->assertEntries(
			array( $hello => false, $hello_world => false, $world => false ),
			$view->ID, array( 'mode' => 'all' )
		);

		$this->assertEntries(
			array( $hello => false, $hello_world => false, $world => false ),
			$view->ID, array(
				'mode' => 'any',
				array( 'key' => '1', 'operator' => 'contains', 'value' => 'hello' ),
				array( 'key' => '2', 'operator' => 'contains', 'value' => '0' ),
			)
		);

		gform_update_meta( $hello, \GravityView_Entry_Approval::meta_key, \GravityView_Entry_Approval_Status::APPROVED );

		$this->assertEntries(
			array( $hello => true, $hello_world => false, $world => false ),
			$view->ID, array(
				'mode' => 'any',
				array( 'key' => '1', 'operator' => 'contains', 'value' => 'hello' ),
				array( 'key' => '2', 'operator' => 'contains', 'value' => '0' ),
			)
		);

		gform_update_meta( $world, \GravityView_Entry_Approval::meta_key, \GravityView_Entry_Approval_Status::APPROVED );

		$this->assertEntries(
			array( $hello => false, $hello_world => false, $world => false ),
			$view->ID, array(
				'mode' => 'all',
				array( 'key' => '1', 'operator' => 'contains', 'value' => 'hello' ),
				array( 'key' => '2', 'operator' => 'contains', 'value' => '0' ),
			)
		);

		gform_update_meta( $hello_world, \GravityView_Entry_Approval::meta_key, \GravityView_Entry_Approval_Status::APPROVED );

		$this->assertEntries(
			array( $hello => false, $hello_world => true, $world => false ),
			$view->ID, array(
				'mode' => 'all',
				array( 'key' => '1', 'operator' => 'contains', 'value' => 'hello' ),
				array( 'key' => '2', 'operator' => 'contains', 'value' => '0' ),
			)
		);

		$this->assertEntries(
			array( $hello => true, $hello_world => true, $world => true ),
			$view->ID, array( 'mode' => 'all' )
		);

		$this->_reset_context();
	}

	public function test_cross_search_filters() {

		$this->_reset_context();

		$view    = $this->get_view( 'simple.json', array(
			'widgets' => array(
				'header_top' => array(
					wp_generate_password( 4, false ) => array(
						'id'            => 'search_bar',
						'search_fields' => '[{"field":"1","input":"input_text"}]',
					),
				),
			),
		) );
		$form_id = $view->form->ID;

		$entry = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'1'       => 'hello',
			'2'       => '',
		) );
		$hello = $entry['id'];

		$entry       = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'1'       => 'hello world',
			'2'       => '40',
		) );
		$hello_world = $entry['id'];

		$entry = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'1'       => 'world',
			'2'       => '50',
		) );
		$world = $entry['id'];

		update_post_meta( $view->ID, '_gravityview_filters', array(
			'mode' => 'any',
			array( 'key' => '1', 'operator' => 'contains', 'value' => 'hello' ),
			array( 'key' => '2', 'operator' => 'contains', 'value' => '0' ),
		) );

		$this->assertCount( 3, $view->get_entries()->all() );

		$_GET['filter_1'] = 'world';

		$this->assertCount( 2, $view->get_entries()->all() );

		update_post_meta( $view->ID, '_gravityview_filters', array(
			'mode' => 'all',
			array( 'key' => '1', 'operator' => 'contains', 'value' => 'l' ),
			array( 'key' => '2', 'operator' => 'contains', 'value' => '0' ),
		) );

		$this->assertCount( 2, $view->get_entries()->all() );

		unset( $_GET['filter_1'] );

		$this->_reset_context();
	}

	public function test_nested_conditionals() {

		$this->_reset_context();

		$view    = $this->get_view( 'simple.json' );
		$form_id = $view->form->ID;

		$entry = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'1'       => 'hello',
			'2'       => '',
		) );
		$hello = $entry['id'];

		$entry       = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'1'       => 'hello world',
			'2'       => '40',
		) );
		$hello_world = $entry['id'];

		$entry = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'1'       => 'world',
			'2'       => '50',
		) );
		$world = $entry['id'];

		$this->assertEntries(
			array( $hello => true, $hello_world => false, $world => false ),
			$view->ID, array(
				'version'    => 2,
				'mode'       => 'and',
				'conditions' => array(
					array(
						'mode'       => 'or',
						'conditions' => array(
							array( 'key' => '1', 'operator' => 'is', 'value' => 'hello' ),
							array( 'key' => '1', 'operator' => 'is', 'value' => 'goodbye' ),

							array(
								'mode'       => 'or',
								'conditions' => array(
									array( 'key' => '1', 'operator' => 'is', 'value' => 'welcome' ),
									array( 'key' => '1', 'operator' => 'is', 'value' => 'belgium' ),
								),
							),
						),
					),
					array(
						'mode'       => 'and',
						'conditions' => array(
							array( 'key' => '2', 'operator' => 'isnot', 'value' => '4000' ),
							array( 'key' => '2', 'operator' => 'isnot', 'value' => '40' ),
							array( 'key' => '2', 'operator' => 'isnot', 'value' => '50' ),

							array(
								'mode'       => 'and',
								'conditions' => array(
									array( 'key' => '2', 'operator' => 'isnot', 'value' => '6' ),
									array( 'key' => '2', 'operator' => 'isnot', 'value' => '7' ),
								),
							),
						),
					),
				),
			)
		);
	}

	public function test_empty_string_condition() {

		$this->_reset_context();

		$view    = $this->get_view( 'simple.json' );
		$form_id = $view->form->ID;

		$entry = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'1'       => '',
		) );
		$hello = $entry['id'];

		$entry       = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'1'       => 'hello world',
		) );
		$hello_world = $entry['id'];

		$entry = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'1'       => 'world',
		) );
		$world = $entry['id'];

		$this->assertEntries(
			array( $hello => true, $hello_world => false, $world => false ),
			$view->ID, array(
				'version'    => 2,
				'mode'       => 'and',
				'conditions' => array(
					array(
						'mode'       => 'or',
						'conditions' => array(
							array( 'key' => '1', 'operator' => 'isempty', 'value' => '' ),
						),
					),
				),
			)
		);
	}

	public function test_not_empty_string_condition() {

		$this->_reset_context();

		$view    = $this->get_view( 'simple.json' );
		$form_id = $view->form->ID;

		$entry = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'1'       => '',
		) );
		$hello = $entry['id'];

		$entry       = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'1'       => 'hello world',
		) );
		$hello_world = $entry['id'];

		$entry = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'1'       => 'world',
		) );
		$world = $entry['id'];

		$this->assertEntries(
			array( $hello => false, $hello_world => true, $world => true ),
			$view->ID, array(
				'version'    => 2,
				'mode'       => 'and',
				'conditions' => array(
					array(
						'mode'       => 'or',
						'conditions' => array(
							array( 'key' => '1', 'operator' => 'isnotempty', 'value' => '' ),
						),
					),
				),
			)
		);
	}

	/**
	 * @dataProvider get_test_convert_filters_to_nested
	 */
	public function test_convert_filters_to_nested( $filters, $expected ) {

		$result = GravityView_Advanced_Filtering::convert_filters_to_nested( $filters );

		// Stub out the unique IDs
		$result = json_decode( preg_replace( '#"_id":".+?"#', '"_id":"#"', json_encode( $result ) ), true );

		$this->assertEquals( $expected, $result );
	}

	/**
	 * @dataProvider get_test_convert_filters_to_nested
	 */
	public function test_convert_filters_to_nested_unique( $filters, $_ ) {

		$result = GravityView_Advanced_Filtering::convert_filters_to_nested( $filters );
		if ( preg_match_all( '#"id":".+?"#', json_encode( $result ), $matches ) ) {
			$this->assertCount( count( $matches[0] ), array_unique( $matches[0] ) );
		}
	}

	public function get_test_convert_filters_to_nested() {

		return array(
			array(
				/** $filters */
				array( 'version' => 2, 'mode' => 'and', 'conditions' => array() )
			,
				/** $expected */
				array( 'version' => 2, 'mode' => 'and', 'conditions' => array() ),
			),

			array(
				/** $filters */
				array( 'mode' => 'all', array( 'key' => '1' ), array( 'key' => '2' ) )
			,
				/** $expected */
				array(
					'_id'        => '#',
					'version'    => 2,
					'mode'       => 'and',
					'conditions' => array(
						array(
							'_id'        => '#',
							'mode'       => 'or',
							'conditions' => array( array( 'key' => '1', '_id' => '#' ) ),
						),
						array(
							'_id'        => '#',
							'mode'       => 'or',
							'conditions' => array( array( 'key' => '2', '_id' => '#' ) ),
						),
					),
				),
			),

			array(
				/** $filters */
				array( /** missing mode! */ array( 'key' => '1' ), array( 'key' => '2' ) )
			,
				/** $expected */
				array(
					'_id'        => '#',
					'version'    => 2,
					'mode'       => 'and',
					'conditions' => array(
						array(
							'_id'        => '#',
							'mode'       => 'or',
							'conditions' => array( array( 'key' => '1', '_id' => '#' ) ),
						),
						array(
							'_id'        => '#',
							'mode'       => 'or',
							'conditions' => array( array( 'key' => '2', '_id' => '#' ) ),
						),
					),
				),
			),

			array(
				/** $filters */
				array( 'mode' => 'any', array( 'key' => '1' ), array( 'key' => '2' ) )
			,
				/** $expected */
				array(
					'_id'        => '#',
					'version'    => 2,
					'mode'       => 'and',
					'conditions' => array(
						array(
							'_id'        => '#',
							'mode'       => 'or',
							'conditions' => array(
								array( '_id' => '#', 'key' => '1' ),
								array( '_id' => '#', 'key' => '2' ),
							),
						),
					),
				),
			),
		);
	}

	public function test_augment_prune_filters() {

		$this->_reset_context();

		$admins = array(
			$this->factory->user->create( array(
					'user_login' => md5( microtime() ),
					'user_email' => md5( microtime() ) . '@gravityview.tests',
					'role'       => 'administrator',
				)
			),
			$this->factory->user->create( array(
					'user_login' => md5( microtime() ),
					'user_email' => md5( microtime() ) . '@gravityview.tests',
					'role'       => 'administrator',
				)
			),
		);

		$filters = array(
			'mode'       => 'and',
			'conditions' => array(
				array(
					'mode'       => 'or',
					'conditions' => array(
						array(
							'mode'       => 'and',
							'conditions' => array(
								array(
									'key'   => 'date_created',
									'value' => '10 days ago', // How far does this go!?
								),

								array(
									'key'   => '0',
									'value' => '', // Search any in all?
								),
							),
						),

						array(
							'mode'       => 'and',
							'conditions' => array(
								array(
									'value' => '', // So lonely...
								),
							),
						),

					),
				),

				array(
					'mode'       => 'and',
					'conditions' => array(
						array(
							'key'   => 'created_by_user_role',
							'value' => 'administrator', // God
						),

						array(
							'key'   => 'created_by',
							'value' => '', // Lock me up
						),

						array(
							'key'   => 'entry_id', // Wrong key, mate!
							'value' => '{get:param}', // _GET it?
						),
					),
				),
			),
		);

		$_GET['param'] = 'one';

		GravityView_Advanced_Filtering::augment_filters( $filters, 0 );
		GravityView_Advanced_Filtering::prune_filters( $filters );

		$expected = strtotime( '10 days ago' );
		$result   = strtotime( $filters['conditions'][0]['conditions'][0]['conditions'][0]['value'] );
		$this->assertEquals( $expected, $result, '', 5 );

		$this->assertCount( 2, $filters['conditions'][0]['conditions'][0]['conditions'] );
		$this->assertCount( 1, $filters['conditions'][0]['conditions'] );

		$this->assertEquals( 'created_by', $filters['conditions'][1]['conditions'][0]['key'] );
		$this->assertEquals( 'in', $filters['conditions'][1]['conditions'][0]['operator'] );
		$this->assertEquals( get_users( array(
			'role'   => 'administrator',
			'fields' => 'ID',
		) ), $filters['conditions'][1]['conditions'][0]['value'] );
		$this->assertEquals( GravityView_Advanced_Filtering::get_lock_filter(), $filters['conditions'][1]['conditions'][1] );

		$this->assertEquals( 'id', $filters['conditions'][1]['conditions'][2]['key'] );
		$this->assertEquals( 'one', $filters['conditions'][1]['conditions'][2]['value'] );

		array_map( 'wp_delete_user', $admins );
	}

	public function test_get_field_filters() {

		$this->_reset_context();

		$admin = $this->factory->user->create( array(
				'user_login' => $login = md5( microtime() ),
				'user_email' => md5( microtime() ) . '@gravityview.tests',
				'role'       => 'administrator',
			)
		);

		$created_by_array = array(
			array(
				'text'  => 'Currently Logged-in User',
				'value' => 'created_by',
			),
			array(
				'text'  => 'Currently Logged-in User (Disabled for Administrators)',
				'value' => 'created_by_or_admin',
			),
		);

		$users = get_users();

		/** @var WP_User $user */
		foreach ( $users as $user ) {
			$created_by_array[] = array(
				'text' => $user->user_login,
				'value' => $user->ID,
			);
		}

		$view = $this->get_view( 'simple.json' );

		update_post_meta( $view->ID, '_gravityview_filters', array(
			'mode' => 'all',
			array( 'key' => '1', 'operator' => 'contains', 'value' => 'l' ),
			array( 'key' => '2', 'operator' => 'contains', 'value' => '0' ),
		) );

		$filters = GravityView_Advanced_Filtering::get_field_filters( $view->ID );

		$this->assertCount( 3, $filters );

		$filter_fields = $filters['field_filters_complete'];
		$filter_vars   = $filters['init_filter_vars'];

		$filter_vars = json_decode( preg_replace( '#"_id":".+?"#', '"_id":"#"', json_encode( $filter_vars ) ), true );

		$this->assertEquals( array(
			'_id'        => '#',
			'version'    => 2,
			'mode'       => 'and',
			'conditions' => array(
				array(
					'_id'        => '#',
					'mode'       => 'or',
					'conditions' => array(
						array( 'key' => '1', 'operator' => 'contains', 'value' => 'l', '_id' => '#' ),
					),
				),
				array(
					'_id'        => '#',
					'mode'       => 'or',
					'conditions' => array(
						array( 'key' => '2', 'operator' => 'contains', 'value' => '0', '_id' => '#' ),
					),
				),
			),
		), $filter_vars );

		$this->assertEquals( array(
			array(
				'key'             => '0',
				'text'            => 'Any form field',
				'operators'       => array(
					'contains',
					'is',
					// 'isempty', // TODO: https://github.com/gravityview/Advanced-Filter/issues/91
				),
				'preventMultiple' => false,
			),
			array(
				'key'             => '1',
				'text'            => 'A Text Field',
				'operators'       => array( 'is', 'isnot', '>', '<', 'contains', 'isempty', 'isnotempty' ),
				'preventMultiple' => false,
			),
			array(
				'key'             => '2',
				'text'            => 'A Number Field',
				'operators'       => array( 'is', 'isnot', '>', '<', 'isempty', 'isnotempty' ),
				'preventMultiple' => false,
			),
			array(
				'key'             => 'entry_id',
				'text'            => 'Entry ID',
				'operators'       => array( 'is', 'isnot', '>', '<' ),
				'preventMultiple' => false,
			),
			array(
				'key'             => 'date_created',
				'text'            => 'Entry Date',
				'operators'       => array( 'is', '>', '<', 'isempty' ),
				'preventMultiple' => false,
				'placeholder'     => 'yyyy-mm-dd',
				'cssClass'        => 'datepicker ymd_dash',
			),
			array(
				'key'             => 'is_starred',
				'text'            => 'Starred',
				'operators'       => array( 'is', 'isnot' ),
				'values'          => array(
					array( 'text' => 'Yes', 'value' => '1' ),
					array( 'text' => 'No', 'value' => '0' ),
				),
				'preventMultiple' => false,
			),
			array(
				'key'             => 'ip',
				'text'            => 'IP Address',
				'operators'       => array( 'is', 'isnot', '>', '<', 'contains', 'isempty', 'isnotempty' ),
				'preventMultiple' => false,
			),
			array(
				'key'             => 'source_url',
				'text'            => 'Source URL',
				'operators'       => array( 'is', 'isnot', '>', '<', 'contains', 'isempty', 'isnotempty' ),
				'preventMultiple' => false,
			),
			array(
				'key'             => 'payment_status',
				'text'            => 'Payment Status',
				'operators'       => array( 'is', 'isnot' ),
				'values'          => array(
					array( 'text' => 'Authorized', 'value' => 'Authorized' ),
					array( 'text' => 'Paid', 'value' => 'Paid' ),
					array( 'text' => 'Processing', 'value' => 'Processing' ),
					array( 'text' => 'Failed', 'value' => 'Failed' ),
					array( 'text' => 'Active', 'value' => 'Active' ),
					array( 'text' => 'Cancelled', 'value' => 'Cancelled' ),
					array( 'text' => 'Pending', 'value' => 'Pending' ),
					array( 'text' => 'Refunded', 'value' => 'Refunded' ),
					array( 'text' => 'Voided', 'value' => 'Voided' ),
				),
				'preventMultiple' => false,
			),
			array(
				'key'             => 'payment_date',
				'text'            => 'Payment Date',
				'operators'       => array( 'is', 'isnot', '>', '<', 'isempty', 'isnotempty' ),
				'preventMultiple' => false,
				'placeholder'     => 'yyyy-mm-dd',
				'cssClass'        => 'datepicker ymd_dash',
			),
			array(
				'key'             => 'payment_amount',
				'text'            => 'Payment Amount',
				'operators'       => array( 'is', 'isnot', '>', '<', 'contains', 'isempty', 'isnotempty' ),
				'preventMultiple' => false,
			),
			array(
				'key'             => 'transaction_id',
				'text'            => 'Transaction ID',
				'operators'       => array( 'is', 'isnot', '>', '<', 'contains', 'isempty', 'isnotempty' ),
				'preventMultiple' => false,
			),
			array(
				'key'             => 'created_by',
				'text'            => 'Created By',
				'operators'       => array( 'is', 'isnot' ),
				'values'          => $created_by_array,
				'preventMultiple' => false,
			),
			array(
				'key'       => 'created_by_user_role',
				'text'      => 'Created By User Role',
				'operators' => array( 'is', 'isnot' ),
				'values'    => array(
					array( 'text' => 'Any Role of Current User', 'value' => 'current_user' ),
					array( 'text' => 'No Capabilities', 'value' => 'zero' ),
					array( 'text' => 'Subscriber', 'value' => 'subscriber' ),
					array( 'text' => 'Contributor', 'value' => 'contributor' ),
					array( 'text' => 'Author', 'value' => 'author' ),
					array( 'text' => 'Editor', 'value' => 'editor' ),
					array( 'text' => 'Administrator', 'value' => 'administrator' ),
				),
			),
			array(
				'key'       => 'date_updated',
				'text'      => 'Date Updated',
				'operators' => array( 'is', '>', '<', 'isempty' ),
				'cssClass'  => 'datepicker ymd_dash',
			),
			array(
				'key'       => 'is_approved',
				'text'      => 'Entry Approval Status',
				'operators' => array( 'is', 'isnot' ),
				'values'    => array(
					array( 'text' => 'Disapproved', 'value' => 2 ),
					array( 'text' => 'Approved', 'value' => 1 ),
					array( 'text' => 'Unapproved', 'value' => 3 ),
				),
			),
		), $filter_fields );

		$this->_reset_users();
	}

	public function test_merge_tag_support() {

		$this->_reset_context();

		$view    = $this->get_view( 'simple.json' );
		$form_id = $view->form->ID;

		$entry = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'1'       => 'one',
		) );
		$one   = $entry['id'];

		$entry = $this->factory->entry->create_and_get( array(
			'form_id' => $form_id,
			'status'  => 'active',
			'1'       => 'two',
		) );
		$two   = $entry['id'];

		$get  = $_GET;
		$_GET = array();

		$this->assertEntries(
			array( $one => false, $two => false ),
			$view->ID, array(
				'mode' => 'all',
				array( 'key' => '1', 'operator' => 'is', 'value' => '{get:param}' ),
			)
		);

		$_GET['param'] = 'one';

		$this->assertEntries(
			array( $one => true, $two => false ),
			$view->ID, array(
				'mode' => 'all',
				array( 'key' => '1', 'operator' => 'is', 'value' => '{get:param}' ),
			)
		);

		$_GET['param'] = 'two';

		$this->assertEntries(
			array( $one => false, $two => true ),
			$view->ID, array(
				'mode' => 'all',
				array( 'key' => '1', 'operator' => 'is', 'value' => '{get:param}' ),
			)
		);

		$_GET = $get;
	}
}
