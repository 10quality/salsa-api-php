<?php

namespace Salsa;

use Salsa\Base\CallableEndpoint;

/**
 * Metrics endpoint accessor.
 *
 * @author Alejandro Mostajo <info@10quality.com> 
 * @version 1.0.0
 * @package Salsa
 * @license MIT
 */
class Metrics extends CallableEndpoint
{
    /**
     * Returns API metrics.
     * @since 1.0.0
     * @return mixed results
     */
    public function get()
    {
        return $this->api->callCurl('api/integration/ext/v1/metrics');
    }
}