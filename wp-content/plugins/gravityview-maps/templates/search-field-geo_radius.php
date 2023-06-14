<?php
use \GravityKit\GravityMaps\Search_Filter;

$gravityview_view = GravityView_View::getInstance();
$search_field     = $gravityview_view->search_field;

if ( ! GravityKit\GravityMaps\Render_Map::get_instance()->can_view_use_rest( $gravityview_view->view_id ) ) {
	return;
}

$settings = \GravityKit\GravityMaps\Admin::get_map_settings( $gravityview_view->view_id, false );

/**
 * Allows changing of the default setting for the Current Location behavior.
 *
 * - True: Will instant search when the current location is allowed by the browser.
 * - False: Will only store the data from current location and allow users to search when clicking on the submit button.
 *
 * @since 2.0
 *
 * @param bool             $using_instant_search
 * @param GravityView_View $view
 */
$using_instant_search = apply_filters( 'gk/gravitymaps/current_location_instant_search', false, $gravityview_view );

$request_radius = \GV\Utils::_GET( 'filter_geolocation' );
$lat            = \GV\Utils::_GET( 'lat' );
$long           = \GV\Utils::_GET( 'long' );
$radius         = \GV\Utils::_GET( 'filter_geolocation', $settings['map_default_radius_search'] );
if ( empty( $radius ) ) {
	$radius = null;
}

$address_search = \GV\Utils::_GET( 'address_search', '' );
$has_search     = ! empty( $request_radius ) || ! empty( $address_search );
$unit           = \GV\Utils::_GET( 'unit', Search_Filter::get_instance()->get_default_radius_unit( $gravityview_view->view_id ) );
$accuracy       = \GV\Utils::_GET( 'accuracy' );
$is_current     = (bool) \GV\Utils::_GET( 'is_current', 0 );

if ( $lat ) {
	unset( $_GET['lat'] );
}

if ( $long ) {
	unset( $_GET['long'] );
}

if ( isset( $_GET['unit'] ) ) {
	unset( $_GET['unit'] );
}
if ( isset( $_GET['accuracy'] ) ) {
	unset( $_GET['accuracy'] );
}
if ( isset( $_GET['is_current'] ) ) {
	unset( $_GET['is_current'] );
}

$radius_options = Search_Filter::get_radius_select_options( $radius );
?>
<div class="gv-search-box gk-search-geolocation-field">
    <input
        type="hidden"
        class="gk-maps-search-current-geolocation-has-search"
        value="<?php echo esc_attr( $has_search ? 1 : 0 ); ?>"
    />
    <input
        type="hidden"
        class="gk-maps-current-view"
        value="<?php echo esc_attr( gravityview_get_view_id() ); ?>"
    />
    <input
        type="hidden"
        name="lat"
        class="gk-maps-search-geolocation-lat"
        value="<?php echo esc_attr( (float) $lat ); ?>"
    />
    <input
        type="hidden"
        name="long"
        class="gk-maps-search-geolocation-lng"
        value="<?php echo esc_attr( (float) $long ); ?>"
    />
    <input
        type="hidden"
        name="accuracy"
        class="gk-maps-search-current-geolocation-accuracy"
        value="<?php echo esc_attr( (int) $accuracy ); ?>"
    />
    <input
        type="hidden"
        name="is_current"
        class="gk-maps-search-current-geolocation-flag"
        value="<?php echo esc_attr( $is_current ? 1 : 0 ); ?>"
    />

	<?php if ( ! gv_empty( $search_field['label'], false, false ) ) { ?>
        <div class="gv-grid-col-1-1">
            <label for="search-box-<?php echo esc_attr( $search_field['name'] ); ?>"><?php echo esc_html( $search_field['label'] ); ?></label>
        </div>
	<?php } ?>
    <div class="gv-grid-col-1-1">
        <div class="gk-maps-search-geolocation-address-autocomplete-container">
            <input
                type="text"
                id="gk-autocomplete-<?php echo esc_attr( $search_field['name'] ); ?>"
                class="gk-maps-search-geolocation-address-autocomplete"
                name="address_search"
                value="<?php echo esc_attr( $address_search ); ?>"
				placeholder="<?php esc_attr_e( 'Enter a location', 'gk-gravitymaps' ); ?>"
                data-js-gk-autocomplete
				<?php if ( $has_search ) : ?>
					data-gk-auto-complete-prevent-clear="1"
				<?php endif; ?>
            />
            <button
				type="button"
                class="gk-maps-search-current-geolocation <?php echo $is_current ? 'gk-maps-search-current-geolocation-active' : ''; ?>"
                title="<?php esc_attr_e( 'Click to use current location', 'gk-gravitymaps' ); ?>"
				data-gk-current-location-instant-search="<?php echo esc_attr( $using_instant_search ); ?>"
            >
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
					<path fill-rule="evenodd" d="M11.54 22.351l.07.04.028.016a.76.76 0 00.723 0l.028-.015.071-.041a16.975 16.975 0 001.144-.742 19.58 19.58 0 002.683-2.282c1.944-1.99 3.963-4.98 3.963-8.827a8.25 8.25 0 00-16.5 0c0 3.846 2.02 6.837 3.963 8.827a19.58 19.58 0 002.682 2.282 16.975 16.975 0 001.145.742zM12 13.5a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
				</svg>
            </button>
        </div>
    </div>
    <div class="gv-grid-col-1-1">
		<?php if ( ! empty( $radius_options ) ) : ?>
			<select
				name="<?php echo esc_attr( $search_field['name'] ); ?>"
				id="search-box-<?php echo esc_attr( $search_field['name'] ); ?>"
				class="gk-maps-search-current-geolocation-radius"
			>
			<?php foreach ( $radius_options as $option ) : ?>
				<option
					value="<?php echo esc_attr( $option['value'] ); ?>"
					<?php selected( $option['selected'] ); ?>
				><?php echo esc_html( sprintf( $option['label'], $option['value'] ) ); ?></option>
			<?php endforeach; ?>
			</select>
		<?php else : ?>
			<input
				type="number"
				min="0"
				step="0.1"
				name="<?php echo esc_attr( $search_field['name'] ); ?>"
				id="search-box-<?php echo esc_attr( $search_field['name'] ); ?>"
				value="<?php echo esc_attr( ! empty( $search_field['value'] ) ? $search_field['value'] : ( empty( $radius ) ? '' : $radius ) ); ?>"
				class="gk-maps-search-current-geolocation-radius"
			/>
		<?php endif; ?>

        <select
            name="unit"
            class="gk-maps-search-current-geolocation-unit"
        >
            <option <?php selected( $unit, Search_Filter::KM ); ?> value="<?php echo Search_Filter::KM ?>">
				<?php esc_html_e( 'Kilometers', 'gk-gravitymaps' ); ?>
            </option>
            <option <?php selected( $unit, Search_Filter::MILES ); ?> value="<?php echo Search_Filter::MILES; ?>">
				<?php esc_html_e( 'Miles', 'gk-gravitymaps' ); ?>
            </option>
        </select>
    </div>
</div>
