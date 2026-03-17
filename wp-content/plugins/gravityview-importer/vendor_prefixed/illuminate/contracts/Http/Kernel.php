<?php
/**
 * @license MIT
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityImport\Foundation\ThirdParty\Illuminate\Contracts\Http;

interface Kernel
{
    /**
     * Bootstrap the application for HTTP requests.
     *
     * @return void
     */
    public function bootstrap();

    /**
     * Handle an incoming HTTP request.
     *
     * @param  \GravityKit\GravityImport\Symfony\Component\HttpFoundation\Request  $request
     * @return \GravityKit\GravityImport\Symfony\Component\HttpFoundation\Response
     */
    public function handle($request);

    /**
     * Perform any final actions for the request lifecycle.
     *
     * @param  \GravityKit\GravityImport\Symfony\Component\HttpFoundation\Request  $request
     * @param  \GravityKit\GravityImport\Symfony\Component\HttpFoundation\Response  $response
     * @return void
     */
    public function terminate($request, $response);

    /**
     * Get the Laravel application instance.
     *
     * @return \GravityKit\GravityImport\Foundation\ThirdParty\Illuminate\Contracts\Foundation\Application
     */
    public function getApplication();
}
