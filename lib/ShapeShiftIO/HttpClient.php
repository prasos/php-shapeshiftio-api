<?php

namespace ShapeShiftIO;

use GuzzleHttp;
use Webmozart\Json\JsonEncoder;
use Webmozart\Json\JsonDecoder;

/**
 * PHP ShapeShift.io http client.
 *
 *
 * @author Sebastian Mäki <sebastian@tico.fi>
 *
 */
class HttpClient
{

    /**
     * @var array
     */
    private $options = array(
        'base_uri'    => 'https://shapeshift.io/',
        'user_agent'  => 'php-shapeshift-api (http://github.com/neatbasis/php-shapeshift-api)',
        'timeout'     => 10
    );

    /**
     * The Guzzle instance is used to communicate with ShapeShift.io.
     *
     * @var GuzzleHttp\Client
     */
    private $httpClient;
    
    /**
     * The JsonDecoder instance is used to decode json from ShapeShift.io
     *
     * @var Webmozart\Json\JsonDecoder
     */
    private $decoder;
    
    /**
     * The JsonEncoder instance is used to decode json to ShapeShift.io
     *
     * @var Webmozart\Json\JsonEncoder
     */
    private $encoder;

    /**
     * Instantiate a new ShapeShift.io client.
     *
     * @param null|Guzzle\Http\ClientInterface $httpClient http client
     */
    public function __construct(GuzzleHttp\ClientInterface $httpClient = null)
    {
        if (null === $this->httpClient) {
            $this->httpClient = new GuzzleHttp\Client($this->options);
            //$this->httpClient->setUserAgent($this->options['user_agent']);
        }
        $this->decoder = new JsonDecoder();
        $this->encoder = new JsonEncoder();
    }

    /**
     * @return GuzzleHttp\ClientInterface
     */
    public function getHttpClient()
    {

        return $this->httpClient;
    }

    /**
     * @param GuzzleHttp\ClientInterface $httpClient
     */
    public function setHttpClient(GuzzleHttp\ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }
    
    /**
     * 
     * @param string $path
     * @param array $query
     * @param array $headers
     * 
     * @return GuzzleHttp\RequestInterface
     */
    public function get($path, $query = array())
    {
        //$options = empty($query) ? [] : ['query' => $query];
        $response = $this->httpClient->request('GET', $path);
        
        if($response->getStatusCode() !== 200)
            throw new \UnexpectedValueException('HTTP Status: '.$response->getStatusCode());
        
        return $this->decoder->decode($response->getBody());
    }
    
    /**
     *
     * @param string $path
     * @param array $data
     *
     * @return GuzzleHttp\RequestInterface
     */
    public function post($path, $data = array())
    {
        $response = $this->httpClient->request('POST', $path, ['json' => $data]);
        if($response->getStatusCode() !== 200)
            throw new \UnexpectedValueException('HTTP Status: '.$response->getStatusCode());
        
        return $this->decoder->decode($response->getBody());
    }
}