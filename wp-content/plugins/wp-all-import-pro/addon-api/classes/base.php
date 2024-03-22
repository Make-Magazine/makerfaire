<?php

namespace Wpai\AddonAPI;

abstract class PMXI_Addon_Base {
    use HasError, HasRegistration;

    public PMXI_Addon_Importer $importer;

    public $slug = 'not-implemented'; // Must be implemented by end-user
    public $version = '0.0.0'; // Must be implemented by end-user
    public $rootDir = ''; // Must be implemented by end-user

    // Extra fields created by the addon
    public $fields = [];

    // Cast values to something else without having to create a custom field
    public $casts = [];

    // Add tooltips to fields
    public $hints = [];

    // Getters
    abstract public function name(): string;

    abstract public function description(): string;

    public function __construct() {
        $this->preflight();
        $this->registerAsAddon();

        $this->importer = PMXI_Addon_Importer::from( $this );
        $this->initEed();

        $this->hints = [
            'time'        => __( 'Use any format supported by the PHP strtotime function.', 'wp_all_import_plugin' ),
            'date'        => __( 'Use any format supported by the PHP strtotime function.', 'wp_all_import_plugin' ),
            'datetime'    => __( 'Use any format supported by the PHP strtotime function.', 'wp_all_import_plugin' ),
            'iconpicker'  => __( 'Specify the icon class name - e.g. fa-user.', 'wp_all_import_plugin' ),
            'colorpicker' => __( 'Specify the hex code the color preceded with a # - e.g. #ea5f1a.', 'wp_all_import_plugin' ),
            'media'       => __( 'Specify the URL to the image or file.', 'wp_all_import_plugin' ),
            'post'        => __( 'Enter the ID, slug, or Title. Separate multiple entries with separator character.', 'wp_all_import_plugin' ),
            'user'        => __( 'Enter the ID, username, or email for the existing user.', 'wp_all_import_plugin' )
        ];

        add_filter( 'wp_all_import_addon_parse', [ $this, 'registerParseFunction' ] );
        add_filter( 'wp_all_import_addon_import', [ $this, 'registerImportFunction' ] );
        add_filter( 'wp_all_import_addon_saved_post', [ $this, 'registerPostSavedFunction' ] );
        add_filter( 'pmxi_options_options', [ $this, 'registerDefaultOptions' ] );
        add_filter( 'pmxi_save_options', [ $this, 'updateOptions' ] );
    }

    /**
     * Path to the plugin file relative to the plugins directory.
     */
    public function getPluginPath() {
        return $this->rootDir . '/plugin.php';
    }

    /**
     * Do stuff before the plugin is activated
     * @return void
     */
    public function preflight() {
        $results = $this->canRun();

        if ( is_wp_error( $results ) ) {
            $this->showErrorAndDeactivate( $results->get_error_message() );

            return;
        }
    }

    public function isAvailableForType( string $importType, $data ) {
        $types           = $this->availableForTypes();
        $unprefixedTypes = array_values( array_filter( $types, fn( $type ) => $type[0] !== '-' ) );
        // If the type is prefixed with a dash, it means the addon not available for it
        $shouldSkip = in_array( '-' . $importType, $types );

        if ( $importType === 'taxonomies' ) {
            $taxonomy = $data['taxonomy_type'];

            return count( $unprefixedTypes ) === 0 || in_array( 'taxonomy:' . $taxonomy, $types ) || in_array( 'taxonomies', $types );
        }

        if ( $shouldSkip ) {
            return false;
        }

        return count( $unprefixedTypes ) === 0 || in_array( $importType, $types );
    }

    /**
     * Get the post types the plugin is available for.
     * Leave empty to make it available for all post types.
     */
    public function availableForTypes() {
        return [];
    }

    public function initEed() {
    }

    /**
     * Determine if the plugin can run on the current site otherwise disable it.
     * @return bool|\WP_Error
     */
    abstract public function canRun();

    /**
     * Get fields by import type (post, term, user, etc.) and taxonomy (if applicable)
     *
     * @param string $type
     * @param string|null $subtype
     *
     * @return mixed
     */
    abstract public static function fields( string $type, string $subtype = null );

    /**
     * Get groups by import type (post, term, user, etc.) and taxonomy (if applicable)
     *
     * @param string $type
     * @param string|null $subtype
     *
     * @return mixed
     */
    abstract public static function groups( string $type, string $subtype = null );

    /**
     * Import fields to the database
     */
    abstract public static function import(
        int $postId,
        array $fields,
        array $data,
        \PMXI_Import_Record $record,
        array $post,
        $logger
    );

    /**
     * Potentially change the class of a field at runtime
     *
     * @param array $field
     * @param class-string<PMXI_Addon_Field> $class
     */
    public function resolveFieldClass( $field, $class ) {
        return $class;
    }

    /**
     * Internal method to simplify the import function for end-users.
     *
     * @param array $importData
     * @param array $parsedData
     */
    public function transformImport( array $importData, array $parsedData ) {
        $params = $this->importer->simplify( $importData, $parsedData );
        if ( ! $params ) {
            return;
        }
        call_user_func_array( [ $this, 'import' ], $params );
    }

    /**
     * Parse the data from the XML file
     *
     * @param array $data
     *
     * @return array
     */
    public function parse( array $data ) {
        $type     = $data['import']->options['custom_type'];
        $subtype  = $data['import']->options['taxonomy_type'];
        $defaults = $this->importer->defaults( $type, $subtype );

        return PMXI_Addon_Parser::from( $this, $data, $defaults );
    }

    /**
     * Called after the post has been saved
     */
    public function postSaved( array $importData ) {
    }

    public function defaultOptions( string $type, string $subtype = null ) {
        return $this->importer->defaults( $type, $subtype );
    }

    public function defaultUpdateOptions() {
        return [
            'is_update'          => true,
            'update_logic'       => 'full_update',
            'fields_list'        => [],
            'fields_only_list'   => [],
            'fields_except_list' => [],
        ];
    }

    public function updateOptions( $options ) {
        if ( ! isset( $options['update_addons'][ $this->slug ] ) ) {
            return $options;
        }

        $post = $options['update_addons'][ $this->slug ];

        if ( $post['update_logic'] === 'only' && ! empty( $post['fields_only_list'] ) ) {
            $post['fields_list'] = explode( ",", $post['fields_only_list'] );
        } elseif ( $post['update_logic'] == 'all_except' && ! empty( $post['fields_except_list'] ) ) {
            $post['fields_list'] = explode( ",", $post['fields_except_list'] );
        }

        $options['update_addons'][ $this->slug ] = $post;

        return $options;
    }

    // Fields and groups helpers

    /**
     * @param string $groupId
     * @param string $type
     * @param string|null $subtype
     *
     * @return array
     */
    public static function getFieldsByGroup( string $groupId, string $type, string $subtype = null ) {
        return pipe( static::fields( $type, $subtype ), [
            fn( $fields ) => array_filter( $fields, fn( $field ) => $field['group'] === $groupId ),
            fn( $fields ) => array_values( $fields )
        ] );
    }

    public static function getGroupById( string $groupId, string $type, string $subtype = null ) {
        return pipe( static::groups( $type, $subtype ), [
            fn( $groups ) => array_filter( $groups, fn( $group ) => $group['id'] === $groupId ),
            fn( $groups ) => array_values( $groups )[0]
        ] );
    }
}

trait HasRegistration {
    // Todo: Maybe refactor this by using the PMXI_Admin_Addons class
    public function registerAsAddon() {
        add_filter(
            'pmxi_new_addons',
            function ( $addons ) {
                $addons[ $this->slug ] = $this;

                return $addons;
            }
        );

        add_filter( 'pmxi_addons', function ( $addons ) {
            if ( empty( $addons[ $this->slug ] ) ) {
                $addons[ $this->slug ] = 1;
            }

            return $addons;
        } );
    }

    public function registerParseFunction( $functions ) {
        $functions[ $this->slug ] = [ $this, 'parse' ];

        return $functions;
    }

    public function registerImportFunction( $functions ) {
        $functions[ $this->slug ] = [ $this, 'transformImport' ];

        return $functions;
    }

    public function registerPostSavedFunction( $functions ) {
        $functions[ $this->slug ] = [ $this, 'postSaved' ];

        return $functions;
    }

    public function registerDefaultOptions( $options ) {
        $options = $options + $this->defaultOptions( $options['custom_type'], $options['taxonomy_type'] );

        return $options;
    }
}

trait HasError {
    public function showErrorAndDeactivate( string $msg ) {
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $notice = new \Wpai\WordPress\AdminErrorNotice( $msg );
        $notice->render();

        deactivate_plugins( $this->getPluginPath() );
    }

    public function getMissingDependencyError( $pluginName, $pluginUrl ) {
        return new \WP_Error( 'missing_dependency', __(
            sprintf( "<b>%s Plugin</b>: <a target=\"_blank\" href=\"%s\">%s</a> must be installed", $this->name(), $pluginUrl, $pluginName ),
            'wp_all_import_plugin'
        ) );
    }
}
