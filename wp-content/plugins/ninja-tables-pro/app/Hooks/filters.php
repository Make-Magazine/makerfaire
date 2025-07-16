<?php

/**
 * All registered filter's handlers should be in app\Hooks\Handlers,
 * addFilter is similar to add_filter and addCustomFlter is just a
 * wrapper over add_filter which will add a prefix to the hook name
 * using the plugin slug to make it unique in all wordpress plugins,
 * ex: $app->addCustomFilter('foo', ['FooHandler', 'handleFoo']) is
 * equivalent to add_filter('slug-foo', ['FooHandler', 'handleFoo']).
 */

/**
 * @var $app NinjaTablesPro\App\Application
 */

use NinjaTablesPro\App\Hooks\Handlers\PlaceholderParserHandler;

$app->addFilter('ninja_parse_placeholder', [PlaceholderParserHandler::class, 'parse']);

add_filter('ninja_table_activated_features', function ($features) {
    $features['ninja_table_front_editor'] = true;

    return $features;
});
