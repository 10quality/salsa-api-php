<?php

namespace Salsa\Base;

use Salsa\Api;

/**
 * Callable class interface.
 *
 * @author Alejandro Mostajo <info@10quality.com> 
 * @version 1.0.0
 * @package Salsa
 * @license MIT
 */
abstract class CallableEndpoint implements CallableInterface
{
    /**
     * Api instance accessor.
     * @since 1.0.0
     * @var Api object 
     */
    protected $api;
    /**
     * Default constructor should always require an API instace as parameter.
     * @since 1.0.0
     *
     * @param Api $api Api instance.
     */
    public function __construct(Api $api)
    {
        $this->api = $api;
    }
}