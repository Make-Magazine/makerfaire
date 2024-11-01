<?php

namespace GV;

class DIY_Layout extends Extension {
	/**
	 * @var string Minimum version of GravityView the Extension requires
	 */
	protected $_min_gravityview_version = '2.0-dev';

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Add hooks where needed
	 *
	 * @since 0.1
	 */
	public function add_hooks() {
		add_action( 'init', [ $this, 'load_template' ], 30 );
		add_action( 'admin_head', [ $this, 'admin_head' ] );
	}

	/**
	 * Load the DIY_Template subclass
	 *
	 * @since 0.1
	 */
	public function load_template() {
		include_once plugin_dir_path( __FILE__ ) . 'class-gravityview-diy-template.php';
	}

	/**
	 * Add scripts & styles to display the wrapper tags
	 *
	 * @todo  Convert to external code
	 * @since 0.1
	 *
	 */
	function admin_head() {
		if ( ! gravityview()->request->is_admin( 'single' ) ) {
			return;
		}

		?>
		<style>
            .gv-field-container-label {
                display: inline-block;
                margin-left: .5em;
                padding-top: 2px;
                color: #666;
                padding-bottom: 1px;
                border-radius: 3px;
            }

            .gv-field-container-label:empty {
                display: none;
            }
		</style>
		<script>
			jQuery( document ).ready( function ( $ ) {
				function gv_diy_add_container_label( e ) {
					var parent = $( e.target ).parents( '.gv-fields' ),
						label = parent.find( '.gv-field-label' ),
						value = $( e.target ).val();

					var container_label = label.find( '.gv-field-container-label' );

					if ( !container_label.length ) {
						container_label = $( '<code class="gv-field-container-label" />' );
						label.append( container_label );
					}

					container_label.text( value.toUpperCase() );
				}

				$( 'body' )
					.on( 'gvdiy-ready change', '.gv-fields .gv-dialog-options .gv-setting-container-container input[name*=container]', gv_diy_add_container_label )
					.on( 'gvdiy-init gravityview/field-added gravityview/dialog-closed', function () {
						$( '#gv-view-configuration-tabs' ).find( '.gv-setting-container-container input[name*=container]:checked' ).trigger( 'gvdiy-ready' );
					} )
					.trigger( 'gvdiy-init' );
			} );
		</script>
		<?php
	}
}

/** Initialize */
new DIY_Layout();
