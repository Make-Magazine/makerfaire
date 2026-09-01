<?php
/**
 * Scheduler Job class.
 * */

namespace GravityKit\GravityView\Foundation\Scheduler\Models;

use GravityKit\GravityView\Foundation\Scheduler\Contracts\Schedulable;

abstract class AbstractAction implements Schedulable {

	/**
	 * @var string
	 */
	protected $name = '';

	/**
	 * Human-readable label for display in the admin UI.
	 *
	 * @since 1.12.0
	 *
	 * @var string
	 */
	protected $label = '';

	/**
	 * Text domain of the product that created this action.
	 *
	 * @since 1.12.0
	 *
	 * @var string
	 */
	protected $product = '';

	/**
	 * If unique, multiple pending actions with the same tasks and arguments are not allowed.
	 *
	 * @since 1.12.0
	 *
	 * @var bool
	 */
	protected $unique = true;

	/**
	 * Priority for the add_action() function.
	 *
	 * @since 1.12.0
	 *
	 * @var int
	 */
	protected $priority = 10;

	/**
	 * Job constructor.
	 *
	 * @param string $job_name Job name.
	 */
	public function __construct( string $job_name ) {
		$this->name = $job_name;
	}

	/**
	 * Gets the job name.
	 *
	 * @return string
	 */
	public function name(): string {
		return $this->name;
	}

	/**
	 * Gets the human-readable label.
	 *
	 * @since 1.12.0
	 *
	 * @return string The label, or empty string if not set.
	 */
	public function label(): string {
		return $this->label;
	}

	/**
	 * Sets the human-readable label for display in the admin UI.
	 *
	 * @since 1.12.0
	 *
	 * @param string $label Human-readable label.
	 *
	 * @return AbstractAction
	 */
	public function set_label( string $label ): AbstractAction {
		$this->label = $label;

		return $this;
	}

	/**
	 * Gets the product text domain.
	 *
	 * @since 1.12.0
	 *
	 * @return string The product text domain, or empty string if not set.
	 */
	public function product(): string {
		return $this->product;
	}

	/**
	 * Resolves product information from the stored text domain.
	 *
	 * @since 1.12.0
	 *
	 * @return array Product data from the registry, or empty array.
	 */
	public function product_info(): array {
		return self::resolve_product( $this->product );
	}

	/**
	 * Resolves product information from a text domain.
	 *
	 * Looks up the product registry via ProductManager. Returns an empty
	 * array when the text domain is empty or unrecognized.
	 *
	 * @since 1.12.0
	 *
	 * @param string $text_domain Product text domain.
	 *
	 * @return array Product data from the registry, or empty array.
	 */
	public static function resolve_product( string $text_domain ): array {
		if ( '' === $text_domain ) {
			return [];
		}

		try {
			$products = \GravityKit\GravityView\Foundation\Licenses\ProductManager::get_instance()->get_products_data();
		} catch ( \Throwable $e ) {
			return [];
		}

		return $products[ $text_domain ] ?? [];
	}

	/**
	 * Sets the product text domain that owns this action.
	 *
	 * @since 1.12.0
	 *
	 * @param string $text_domain Product text domain.
	 *
	 * @return AbstractAction
	 */
	public function set_product( string $text_domain ): AbstractAction {
		$this->product = $text_domain;

		return $this;
	}

	/**
	 * Sets the action priority.
	 *
	 * @since 1.12.0
	 *
	 * @param int $priority Action priority.
	 *
	 * @return AbstractAction
	 */
	public function set_priority( int $priority ): AbstractAction {
		$this->priority = $priority;

		return $this;
	}

	/**
	 * Sets the action unique property.
	 *
	 * @since 1.12.0
	 *
	 * @param bool $unique If the action is unique.
	 *
	 * @return AbstractAction
	 */
	public function set_unique( bool $unique ): AbstractAction {
		$this->unique = $unique;

		return $this;
	}
}
