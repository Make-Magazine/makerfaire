<?php

/**
 * All registered action's handlers should be in app\Hooks\Handlers,
 * addAction is similar to add_action and addCustomAction is just a
 * wrapper over add_action which will add a prefix to the hook name
 * using the plugin slug to make it unique in all wordpress plugins,
 * ex: $app->addCustomAction('foo', ['FooHandler', 'handleFoo']) is
 * equivalent to add_action('slug-foo', ['FooHandler', 'handleFoo']).
 */

/**
 * @var $app NinjaTablesPro\App\Application
 */

use NinjaTablesPro\App\Hooks\Handlers\TableEditorHandler;
use NinjaTablesPro\App\Hooks\Handlers\TableHandler;
use NinjaTablesPro\App\Hooks\Handlers\DataProviderHandler;
use NinjaTablesPro\App\Hooks\Handlers\ExtraShortCodeHandler;
use NinjaTablesPro\App\Hooks\Handlers\CustomFilterHandler;
use NinjaTablesPro\App\Hooks\Handlers\CustomJsHandler;
use NinjaTablesPro\App\Hooks\Handlers\PositionHandler;

add_action('init', function () {
    (new DataProviderHandler())->handle();
    (new ExtraShortCodeHandler())->register();
    (new TableEditorHandler())->register();
    (new TableHandler())->register();
    (new CustomJsHandler())->register();
    (new CustomFilterHandler())->register();
    (new PositionHandler())->register();
});
