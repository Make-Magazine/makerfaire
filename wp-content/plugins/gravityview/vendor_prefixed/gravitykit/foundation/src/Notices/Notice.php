<?php
/**
 * @license GPL-2.0-or-later
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

namespace GravityKit\GravityView\Foundation\Notices;

use GravityKit\GravityView\Foundation\Licenses\ProductManager;
use GravityKit\GravityView\Foundation\Helpers\Core as CoreHelpers;

/**
 * Base immutable notice implementation shared by runtime and stored notices.
 *
 * @since 1.3.0
 */
abstract class Notice implements NoticeInterface {
	/**
	 * Default product name.
	 *
	 * @since 1.3.0
	 *
	 * @var string
	 */
	public const DEFAULT_PRODUCT_NAME = 'GravityKit';

	/**
	 * Filename of the default product icon; converted to full URL at runtime where required.
	 *
	 * @since 1.3.0
	 *
	 * @var string
	 */
	public const DEFAULT_PRODUCT_ICON_FILE = 'gravitykit-icon.png';

	/**
	 * Default sorting order when none provided.
	 *
	 * @since 1.3.0
	 *
	 * @var int
	 */
	private const DEFAULT_ORDER = 10;

	/**
	 * Default severity level.
	 *
	 * @since 1.3.0
	 *
	 * @var string
	 */
	private const DEFAULT_SEVERITY = 'info';

	/**
	 * Whether notices are dismissible by default.
	 *
	 * @since 1.3.0
	 *
	 * @var bool
	 */
	private const DEFAULT_DISMISSIBLE = true;

	/**
	 * Whether notices are sticky by default.
	 *
	 * @since 1.3.0
	 *
	 * @var bool
	 */
	private const DEFAULT_STICKY = false;

	/**
	 * Default snooze options (none).
	 *
	 * @since 1.3.0
	 *
	 * @var array
	 */
	private const DEFAULT_SNOOZE = [];

	/**
	 * Default screen rules (global).
	 *
	 * @since 1.3.0
	 *
	 * @var array
	 */
	private const DEFAULT_SCREENS = [ 'dashboard' ];

	/**
	 * Default capability list (public).
	 *
	 * @since 1.3.0
	 *
	 * @var array
	 */
	private const DEFAULT_CAPABILITIES = [];

	/**
	 * Default context (site admin only).
	 *
	 * @since 1.3.0
	 *
	 * @var array
	 */
	private const DEFAULT_CONTEXT = [ 'site', 'ms_subsite' ];

	/**
	 * Valid context values.
	 *
	 * @since 1.3.0
	 *
	 * @var array
	 */
	private const VALID_CONTEXTS = [ 'ms_network', 'ms_main', 'ms_subsite', 'site', 'user' ];

	/**
	 * Default start timestamp (immediate).
	 *
	 * @since 1.3.0
	 *
	 * @var int
	 */
	private const DEFAULT_STARTS = 0;

	/**
	 * Default expiry timestamp (never).
	 *
	 * @since 1.3.0
	 *
	 * @var int
	 */
	private const DEFAULT_EXPIRES = 0;


	/**
	 * Live notice – generic error code.
	 */
	public const LIVE_ERROR_CODE = 'live_callback_error';

	/**
	 * Raw notice data object.
	 *
	 * @since 1.3.0
	 *
	 * @var array<string,mixed>
	 */
	protected $data = [];


	/**
	 * Constructor.
	 *
	 * @since 1.3.0
	 *
	 * @param array<string,mixed> $data Notice definition.
	 */
	protected function __construct( array $data ) {
		$this->data = $data;
	}

	/**
	 * Static factory method to create new Notice instances.
	 *
	 * @since 1.3.0
	 *
	 * @param array<string,mixed> $data Notice definition.
	 *
	 * @return static New notice instance.
	 */
	public static function create( array $data ): self {
		/** @phpstan-ignore-next-line new static() is safe here for factory method */
		return new static( $data );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.3.0
	 */
	public function get_slug(): string {
		return (string) ( $this->data['slug'] ?? '' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.3.0
	 */
	public function get_namespace(): string {
		return (string) ( $this->data['namespace'] ?? '' );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.3.0
	 */
	public function get_message(): string {
		$message = (string) ( $this->data['message'] ?? '' );

		return self::sanitize_message( $message );
	}

	/**
	 * Sanitizes notice message content to prevent XSS while allowing safe HTML formatting.
	 *
	 * @since 1.3.0
	 *
	 * @param string $message Raw message content.
	 *
	 * @return string Sanitized message with only allowed HTML tags.
	 */
	public static function sanitize_message( string $message ): string {
		$tags = [
			'a'      => [
				'href'   => [],
				'title'  => [],
				'target' => [],
			],
			'strong' => [],
			'em'     => [],
			'br'     => [],
			'code'   => [],
			'span'   => [ 'class' => [] ],
			'p'      => [],
			'ul'     => [],
			'ol'     => [],
			'li'     => [],
			'b'      => [],
			'i'      => [],
			'img'    => [
				'src'    => [],
				'alt'    => [],
				'title'  => [],
				'width'  => [],
				'height' => [],
				'class'  => [],
			],
		];

		/**
		 * Filters the allowed HTML tags for notice messages.
		 *
		 * @filter `gk/foundation/notices/content/allowed-tags`
		 *
		 * @since 1.3.0
		 *
		 * @param array $tags Allowed HTML tags and attributes.
		 */
		$tags = apply_filters( 'gk/foundation/notices/content/allowed-tags', $tags );

		return wp_kses( $message, $tags );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.3.0
	 */
	public function get_order(): int {
		return (int) ( $this->data['order'] ?? self::DEFAULT_ORDER );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.3.0
	 */
	public function get_severity(): string {
		return (string) ( $this->data['severity'] ?? self::DEFAULT_SEVERITY );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.3.0
	 */
	public function get_screens(): array {
		// Can be string, callable, or array of those; normalise to array for evaluator.
		$value = $this->data['screens'] ?? self::DEFAULT_SCREENS;

		if ( is_string( $value ) || is_callable( $value ) ) {
			return [ $value ];
		}

		return is_array( $value ) ? $value : [];
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.3.0
	 */
	public function get_capabilities(): array {
		$caps = $this->data['capabilities'] ?? self::DEFAULT_CAPABILITIES;

		// Cast single value to array and filter out empty strings.

		/** @phpstan-ignore-next-line */
		return array_values( array_filter( (array) $caps, 'strlen' ) );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.3.0
	 */
	public function is_dismissible(): bool {
		return (bool) ( $this->data['dismissible'] ?? self::DEFAULT_DISMISSIBLE );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.3.0
	 */
	public function is_snoozable(): bool {
		if ( $this->is_flash() ) {
			return false;
		}

		return ! empty( $this->get_snooze_options() );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.3.0
	 */
	public function get_snooze_options(): array {
		if ( $this->is_flash() ) {
			return [];
		}

		return (array) ( $this->data['snooze'] ?? self::DEFAULT_SNOOZE );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.3.0
	 */
	public function is_sticky(): bool {
		return (bool) ( $this->data['sticky'] ?? self::DEFAULT_STICKY );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.3.0
	 */
	public function get_id(): string {
		return $this->get_namespace() . '/' . $this->get_slug();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.3.0
	 *
	 * @param ProductManager|null $product_manager Optional ProductManager instance for dependency injection.
	 */
	public function get_product( ?ProductManager $product_manager = null ): array {
		$product_manager = $product_manager ?? ProductManager::get_instance();
		$products        = $product_manager->get_products_data();

		return [
			'name' => $this->data['product_name'] ?? ( $products[ $this->get_namespace() ]['name'] ?? self::default_product()['name'] ),
			'icon' => $this->data['product_icon'] ?? ( $products[ $this->get_namespace() ]['icon'] ?? self::default_product()['icon'] ),
		];
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.3.0
	 *
	 * @param ProductManager|null $product_manager Optional ProductManager instance for dependency injection.
	 */
	public function as_payload( ?ProductManager $product_manager = null ): array {
		$payload = [
			'id'          => $this->get_id(),
			'namespace'   => $this->get_namespace(),
			'slug'        => $this->get_slug(),
			'message'     => $this->get_message(),
			'severity'    => $this->get_severity(),
			'dismissible' => $this->is_dismissible(),
			'sticky'      => $this->is_sticky(),
			'snooze'      => $this->get_snooze_options(),
			'extra'       => $this->get_extra(),
			'_product'    => $this->get_product( $product_manager ),
		];

		$start_time = $this->get_start_time();

		if ( $start_time > 0 ) {
			$payload['starts'] = $start_time;
		}

		$expires = $this->get_expiration();

		if ( $expires > 0 ) {
			$payload['expires'] = $expires;
		}

		return $payload;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.3.0
	 */
	public function as_definition(): array {
		return $this->data;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.3.0
	 */
	public function is_flash(): bool {
		return ! empty( $this->data['flash'] );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.3.0
	 */
	public function get_condition() {
		$value = $this->data['condition'] ?? null;

		return is_callable( $value ) ? $value : null;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.3.0
	 */
	public function get_start_time(): int {
		return (int) ( $this->data['starts'] ?? self::DEFAULT_STARTS );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.3.0
	 */
	public function is_scheduled(): bool {
		$start_time = $this->get_start_time();

		return $start_time && time() < $start_time;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.3.0
	 */
	public function get_expiration(): int {
		return self::DEFAULT_EXPIRES;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.3.0
	 */
	public function get_extra(): array {
		return (array) ( $this->data['extra'] ?? [] );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 1.3.0
	 */
	public function get_context(): array {
		$context = $this->data['context'] ?? self::DEFAULT_CONTEXT;

		// Normalize to array.
		if ( is_string( $context ) ) {
			// Handle 'all' as a special case.
			if ( 'all' === $context ) {
				return self::VALID_CONTEXTS;
			}

			$context = [ $context ];
		}

		// Ensure valid contexts.
		$context = array_intersect( (array) $context, self::VALID_CONTEXTS );

		return ! empty( $context ) ? array_values( $context ) : self::DEFAULT_CONTEXT;
	}

	/**
	 * Returns the default product data (name + icon URL).
	 *
	 * @since 1.3.0
	 *
	 * @return array{name:string,icon:string}
	 */
	public static function default_product(): array {
		return [
			'name' => self::DEFAULT_PRODUCT_NAME,
			'icon' => CoreHelpers::get_assets_url( self::DEFAULT_PRODUCT_ICON_FILE ),
		];
	}
}
