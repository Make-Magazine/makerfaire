<?php

namespace GravityKit\MultipleForms\Template;

use GravityKit\MultipleForms\AbstractSingleton;
use GV\Context;
use GV\Entry;
use GV\Multi_Entry;

/**
 * Class ComplexFieldModifier
 *
 * @since 0.3
 *
 * @package GravityKit\MultipleForms\Template
 */
class ComplexFieldEntryModifier extends AbstractSingleton {

	/**
	 * Registering happens after the singleton instance has been set up, which is after the extension was confirmed to have
	 * its requirements met and after `plugins_loaded@P20`
	 *
	 * @since 0.3
	 *
	 * @return void
	 */
	protected function register(): void  {
		add_filter( 'gravityview/template/field/context', [ $this, 'modify_entry_when_joined_forms' ], 20 );
	}

	/**
	 * Modifies the context when dealing with Union Forms to take the correct entry for all fields.
	 *
	 * @since 0.3
	 *
	 * @param Context $context
	 *
	 * @return Context
	 */
	public function modify_entry_when_joined_forms( Context $context ): Context {
		// only change anything when it's a multi entry view.
		if ( ! $context->entry instanceof Multi_Entry ) {
			return $context;
		}

		if ( empty( $context->source->ID ) || empty( $context->entry[ $context->source->ID ] ) ) {
			return $context;
		}

		$context->entry = $context->entry[ $context->source->ID ];

		return $context;
	}
}
