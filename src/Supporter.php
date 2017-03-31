<?php

namespace Salsa;

use Exception;
use Salsa\Base\CallableEndpoint;

/**
 * Supporter(S) endpoint accessor.
 *
 * @author Alejandro Mostajo <info@10quality.com> 
 * @version 1.0.0
 * @package Salsa
 * @license MIT
 */
class Supporter extends CallableEndpoint
{
    /**
     * List of available endpoits for supporter(s) calls.
     * @since 1.0.0
     * @var array 
     */
    protected $endpoints = array(
        'supporters'            => 'api/integration/ext/v1/supporters',
        'supporters_search'     => 'api/integration/ext/v1/supporters/search',
    );
    /**
     * Returns supporters found by email addresses.
     * @since 1.0.0
     *
     * @param array $email List of emails to search.
     *
     * @return mixed results
     */
    public function searchByEmails(array $emails)
    {
        return $this->api->callCurl(
            $this->endpoints['supporters_search'],
            'JPOST',
            [
                'payload' => [
                    'identifiers'       => $emails,
                    'identifierType'    => 'EMAIL_ADDRESS',
                ]
            ]
        );
    }
    /**
     * Returns supporters found by email address.
     * @since 1.0.0
     *
     * @param string $email Email to search.
     *
     * @return mixed results
     */
    public function searchByEmail($email)
    {
        if (!is_string($email))
            throw new Exception('Email parameter should be a string value.');
        return $this->searchByEmails([$email]);
    }
    /**
     * Returns supporters found by supporter ids.
     * @since 1.0.0
     *
     * @param array $ids List of supporter ids to search.
     *
     * @return mixed results
     */
    public function searchByIDs(array $ids)
    {
        return $this->api->callCurl(
            $this->endpoints['supporters_search'],
            'JPOST',
            [
                'payload' => [
                    'identifiers'       => $ids,
                    'identifierType'    => 'SUPPORTER_ID',
                ]
            ]
        );
    }
    /**
     * Returns supporters found by supporter id.
     * @since 1.0.0
     *
     * @param string $id Supporter ID to search.
     *
     * @return mixed results
     */
    public function searchByID($id)
    {
        if (!is_string($id))
            throw new Exception('Supporter ID parameter should be a string value.');
        return $this->searchByIDs([$id]);
    }
    /**
     * Returns supporters found by external ids.
     * @since 1.0.0
     *
     * @param array $ids List of supporter external ids to search.
     *
     * @return mixed results
     */
    public function searchByExternalIDs(array $ids)
    {
        return $this->api->callCurl(
            $this->endpoints['supporters_search'],
            'JPOST',
            [
                'payload' => [
                    'identifiers'       => $ids,
                    'identifierType'    => 'EXTERNAL_ID',
                ]
            ]
        );
    }
    /**
     * Returns supporters found by supporter external id.
     * @since 1.0.0
     *
     * @param string $id Supporter external ID to search.
     *
     * @return mixed results
     */
    public function searchByExternalID($id)
    {
        if (!is_string($id))
            throw new Exception('Supporter ID parameter should be a string value.');
        return $this->searchByExternalIDs([$id]);
    }
}