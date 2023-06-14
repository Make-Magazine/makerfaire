<?php
/**
 * @license MIT
 *
 * Modified using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace GravityKit\GravityMaps\Foundation\ThirdParty\Illuminate\Validation;

use Exception;

class ValidationException extends Exception
{
    /**
     * The validator instance.
     *
     * @var \GravityKit\GravityMaps\Foundation\ThirdParty\Illuminate\Contracts\Validation\Validator
     */
    public $validator;

    /**
     * The recommended response to send to the client.
     *
     * @var \GravityKit\GravityMaps\Symfony\Component\HttpFoundation\Response|null
     */
    public $response;

    /**
     * Create a new exception instance.
     *
     * @param  \GravityKit\GravityMaps\Foundation\ThirdParty\Illuminate\Contracts\Validation\Validator  $validator
     * @param  \GravityKit\GravityMaps\Symfony\Component\HttpFoundation\Response  $response
     * @return void
     */
    public function __construct($validator, $response = null)
    {
        parent::__construct('The given data failed to pass validation.');

        $this->response = $response;
        $this->validator = $validator;
    }

    /**
     * Get the underlying response instance.
     *
     * @return \GravityKit\GravityMaps\Symfony\Component\HttpFoundation\Response|null
     */
    public function getResponse()
    {
        return $this->response;
    }
}
