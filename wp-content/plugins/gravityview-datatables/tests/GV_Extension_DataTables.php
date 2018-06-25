<?php

defined( 'DOING_GRAVITYVIEW_TESTS' ) || exit;

class GV_Extension_DataTables_DataTest extends GV_UnitTestCase {

	/**
	 * Mock a $_POST request for AJAX purposes ;)
	 *
	 * @param array|null $params The $_POST parameters or null to reset.
	 *
	 * @return void
	 */
	private function _mock_post( $params = null ) {
		if ( ! is_array( $params ) ) {
			$_POST = array();
		} else {
			$_POST = wp_parse_args( $params, array(
				'action' => 'gv_datatables_data',
			) );
		}
		return $_POST;
	}

	public function test_get_datatables_data_empty() {
		$this->_mock_post();

		$data = new \GV_Extension_DataTables_Data();

		/** Just make sure we're good to go. */
		$this->assertNull( $data::do_ajax_headers( 'whatever' ) );

		/** Empty AJAX request? Meh. */
		$this->assertNull( $data->get_datatables_data() );

		$params = $this->_mock_post( array() );

		/** Invalid nonce? Meh. */
		$this->assertFalse( $data->get_datatables_data() );

		/** Check it. */
		$this->assertFalse( $data->check_ajax_nonce() );

		$params['nonce'] = wp_create_nonce( 'gravityview_datatables_data' );
		$params = $this->_mock_post( $params );

		/** The nonce is fine. */
		$this->assertTrue( $data->check_ajax_nonce() );

		/** No view_id. */
		$this->assertFalse( $data->get_datatables_data() );

		global $post;
		$form = $this->factory->form->import_and_get( 'complete.json' );
		$post = $this->factory->view->create_and_get( array(
			'form_id' => $form['id'],
			'template_id' => 'datatables_table',
		) );
		$view = \GV\View::from_post( $post );

		$params['view_id'] = $view->ID;
		$params = $this->_mock_post( $params );

		$this->assertEquals( '{"draw":-1,"recordsTotal":0,"recordsFiltered":0,"data":[]}', $data->get_datatables_data() );

		$this->_mock_post();
	}

	public function test_get_datatables_data_generic() {
		$this->_mock_post();

		$data = new \GV_Extension_DataTables_Data();

		global $post;
		$form = $this->factory->form->import_and_get( 'complete.json' );
		$post = $this->factory->view->create_and_get( array(
			'form_id' => $form['id'],
			'template_id' => 'datatables_table',
			'fields' => array(
				'directory_table-columns' => array(
					wp_generate_password( 4, false ) => array(
						'id' => 'id',
						'label' => 'Entry ID',
					),
					wp_generate_password( 4, false ) => array(
						'id' => '16',
						'label' => 'Textarea',
					),
				),
			),
			'settings' => array(
				'page_size' => 3,
			),
		) );
		$view = \GV\View::from_post( $post );

		$entry = $this->factory->entry->create_and_get( array(
			'form_id' => $form['id'],
			'status' => 'active',
			'16' => 'hello " :'
		) );

		$params = $this->_mock_post( array(
			'nonce' => wp_create_nonce( 'gravityview_datatables_data' ),
			'view_id' => $view->ID,
			'draw' => 6,
		) );

		$expected = array(
			'draw' => 6,
			'recordsTotal' => 1,
			'recordsFiltered' => 1,
			'data' => array( array( $entry['id'], "<p>hello \" :</p>\n" ) ),
		);
		$this->assertEquals( $expected, json_decode( $data->get_datatables_data(), true ) );

		$entry2 = $this->factory->entry->create_and_get( array(
			'form_id' => $form['id'],
			'status' => 'active',
			'16' => 'good'
		) );

		$expected = array(
			'draw' => 6,
			'recordsTotal' => 2,
			'recordsFiltered' => 2,
			'data' => array(
				array( $entry2['id'], "<p>good</p>\n" ),
				array( $entry['id'], "<p>hello \" :</p>\n" ),
			),
		);
		$this->assertEquals( $expected, json_decode( $data->get_datatables_data(), true ) );

		foreach ( range( 1, 10 ) as $i ) {
			$this->factory->entry->create_and_get( array(
				'form_id' => $form['id'],
				'status' => 'active',
				'16' => $i,
			) );
		}

		$result = json_decode( $data->get_datatables_data(), true );
		$this->assertEquals( 12, $result['recordsTotal'] );
		$this->assertEquals( 12, count( $result['data'] ) );

		/** Page limits and offsets */
		$params['length'] = 3;
		$params = $this->_mock_post( $params );

		$result = json_decode( $data->get_datatables_data(), true );
		$this->assertEquals( 12, $result['recordsTotal'] );
		$this->assertEquals( 3, count( $result['data'] ) );
		$this->assertContains( '10', $result['data'][0][1] );
		$this->assertContains( '9', $result['data'][1][1] );
		$this->assertContains( '8', $result['data'][2][1] );

		$params['start'] = 1;
		$params = $this->_mock_post( $params );

		$result = json_decode( $data->get_datatables_data(), true );
		$this->assertEquals( 12, $result['recordsTotal'] );
		$this->assertEquals( 3, count( $result['data'] ) );
		$this->assertContains( '9', $result['data'][0][1] );
		$this->assertContains( '8', $result['data'][1][1] );
		$this->assertContains( '7', $result['data'][2][1] );

		$params['order'] = array( array( 'column' => 1, 'dir' => 'desc' ) );
		$params['columns'] = array( null, array( 'name' => 'gv_16' ) );
		$params = $this->_mock_post( $params );
		$result = json_decode( $data->get_datatables_data(), true );
		$this->assertEquals( 12, $result['recordsTotal'] );
		$this->assertEquals( 3, count( $result['data'] ) );
		$this->assertContains( 'good', $result['data'][0][1] );
		$this->assertContains( '9', $result['data'][1][1] );
		$this->assertContains( '8', $result['data'][2][1] );

		$this->_mock_post();
	}

	public function test_get_datatables_data_search() {
		$this->_mock_post();

		$data = new \GV_Extension_DataTables_Data();

		global $post;
		$form = $this->factory->form->import_and_get( 'complete.json' );
		$post = $this->factory->view->create_and_get( array(
			'form_id' => $form['id'],
			'template_id' => 'datatables_table',
			'fields' => array(
				'directory_table-columns' => array(
					wp_generate_password( 4, false ) => array(
						'id' => 'id',
						'label' => 'Entry ID',
					),
					wp_generate_password( 4, false ) => array(
						'id' => '16',
						'label' => 'Textarea',
					),
				),
			),
			'settings' => array(
				'page_size' => 3,
			),
		) );
		$view = \GV\View::from_post( $post );

		$params = $this->_mock_post( array(
			'nonce' => wp_create_nonce( 'gravityview_datatables_data' ),
			'view_id' => $view->ID,
			'draw' => 9,
		) );

		foreach ( range( 1, 10 ) as $i ) {
			$this->factory->entry->create_and_get( array(
				'form_id' => $form['id'],
				'status' => 'active',
				'16' => "00$i",
			) );
		}

		$result = json_decode( $data->get_datatables_data(), true );
		$this->assertEquals( 10, $result['recordsTotal'] );
		$this->assertEquals( 10, $result['recordsFiltered'] );

		$params['search'] = array( 'value' => '001' );
		$params = $this->_mock_post( $params );

		$result = json_decode( $data->get_datatables_data(), true );

		$this->assertEquals( 2, $result['recordsFiltered'] );
		/**
		 * @todo Is this "Filtered" functionality worth a second SQL request?
		 *
		 * Just so we can have a nice "Showing 1 to 3 of 6 entries (filtered from 57 total entries)"?
		 *  Instead of "Showing 1 to 3 of 6 entries"?
		 *
		 * $this->assertEquals( 10, $result['recordsTotal'] );
		 */

		$params['search'] = array( 'value' => '010' );
		$params = $this->_mock_post( $params );

		$result = json_decode( $data->get_datatables_data(), true );

		$this->assertEquals( 1, $result['recordsFiltered'] );

		$this->_mock_post();
	}
}
