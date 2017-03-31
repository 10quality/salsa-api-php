<?php

namespace Salsa;

use Salsa\Base\Response;

/**
 * Salsa's API main accessor.
 *
 * @author Alejandro Mostajo <info@10quality.com> 
 * @version 1.0.0
 * @package Salsa
 * @license MIT
 */
class Api
{
    /**
     * API instance.
     * @since 1.0.0
     * @var object this
     */
    protected static $instance;
    /**
     * API configuration settings.
     * @since 1.0.0
     * @var array
     */
    protected $settings = array();
    /**
     * Curl accessor.
     * @since 1.0.0
     * @var object
     */
    protected $curl;
    /**
     * Last response got from API.
     * RAW response
     * @since 1.0.0
     * @var string
     */
    protected $response;
    /**
     * List of possible usage environments.
     * @since 1.0.0
     * @var array
     */
    protected $envs = array(
        'live'      => 'https://api.salsalabs.org/',
        'sandbox'   => 'https://sandbox.salsalabs.com/',
    );
    /**
     * Default API class constructor.
     * @since 1.0.0
     *
     * @param array $settings API settings. 
     */
    public function __construct($settings = array())
    {
        $this->settings = $settings;
    }
    /**
     * Static constructor.
     * @since 1.0.0
     *
     * @param array $settings API settings. 
     */
    public static function instance($settings = array())
    {
        if (isset(static::$instance))
            return static::$instance;
        static::$instance = new self($settings);
        return static::$instance;
    }
    /**
     * Executes CURL call.
     * Returns API response.
     * @since 1.0.0
     *
     * @param string $endPoint API endpoint to call.
     * @param string $method   Request method.
     * @param array  $data     Request data.
     *
     * @return Response object
     */
    public function callCurl($endPoint, $method = 'GET', $data = array())
    {
        // Begin
        $this->setCurl();
        // Make call
        curl_setopt(
            $this->curl,
            CURLOPT_URL,
            (isset($this->settings['env']) && $this->settings['env'] === 'live'
                ? $this->envs['live']
                : (isset($this->settings['sandbox'])
                    ? $this->settings['sandbox']
                    : $this->envs['sandbox']
                )
            ).$endPoint
        );
        // Set method
        switch ($method) {
            case 'GET':
                curl_setopt($this->curl, CURLOPT_POST, 0);
                break;
            case 'POST':
                curl_setopt($this->curl, CURLOPT_POST, 1);
                if (count($data) > 0)
                    curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($data));
                break;
            case 'JPOST':
            case 'JPUT':
            case 'JGET':
            case 'JDELETE':
                $json = json_encode($data);                                     
                curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, preg_replace('/J/', '', $method, -1));
                curl_setopt($this->curl, CURLOPT_POSTFIELDS, $json);
                // Rewrite headers
                curl_setopt($this->curl, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: '.strlen($json),
                    'authToken: '.$this->settings['token'],
                ));     
                break;
        }
        // Get response
        $this->response = curl_exec($this->curl);
        curl_close($this->curl);
        return new Response($this->response);
    }
    /**
     * Sets curl property and its settings.
     * @since 1.0.0
     *
     * @see http://us3.php.net/manual/en/book.curl.php
     * @see https://gist.github.com/salsalabs/e24c2466496860975e8a
     */
    private function setCurl()
    {
        // Basic validations
        if (!isset($this->settings['token']))
            throw new Exception('Salsa API access token not defined.');
        // Init
        $this->curl = curl_init();
        // Sets basic parameters
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, isset($this->settings['timeout']) ? $this->settings['timeout'] : 100);
        // Set parameters to maintain cookies across sessions
        curl_setopt($this->curl, CURLOPT_COOKIESESSION, TRUE);
        curl_setopt($this->curl, CURLOPT_COOKIEFILE, '/tmp/cookies_file');
        curl_setopt($this->curl, CURLOPT_COOKIEJAR, '/tmp/cookies_file');
        curl_setopt($this->curl, CURLOPT_USERAGENT,
            'Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7'
        );
        // Set headers
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array(
            'authToken: '.$this->settings['token'],
        ));
    }
}