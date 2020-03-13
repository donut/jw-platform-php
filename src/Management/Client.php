<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management;


use Jwplayer\JwplatformAPI;
use Psr\Http\Message\ResponseInterface;
use RightThisMinute\JWPlatform\exception\UnexpectedResponse;
use RightThisMinute\JWPlatform\exception\URLTooLong;
use RightThisMinute\JWPlatform\Management\response\BadRequestBody;
use RightThisMinute\JWPlatform\Management\response\NotFoundBody;
use RightThisMinute\JWPlatform\Management\response\ResponseBody;
use RightThisMinute\JWPlatform\Management\response\SuccessJSONBody;
use RightThisMinute\JWPlatform\Management\response\TooManyRequestsBody;
use function Functional\map;
use const RightThisMinute\JWPlatform\common\MAX_REQUEST_URL_LENGTH;

class Client
{
  const BASE_URI = 'https://api.jwplatform.com/v1/';
  const API_KIT = 'RightThisMinute/jw-platform-php';

  /**
   * @var string
   */
  private $key;

  /**
   * @var string
   */
  private $secret;

  /**
   * @var \GuzzleHttp\Client
   */
  private $guzzle;


  /**
   * Client constructor.
   *
   * @param string $key
   *   JW Management API key.
   * @param string $secret
   *   JW Management API secret.
   */
  public function __construct (string $key, string $secret)
  {
    $this->key = $key;
    $this->secret = $secret;

    $this->guzzle = new \GuzzleHttp\Client
      ([ 'base_uri' => static::BASE_URI
       , 'http_errors' => false ]);
  }


  /**
   * Make an HTTP GET request to the JW Management API.
   *
   * @param string $endpoint
   *   The endpoint path without the host or version prefixes.
   * @param string[] $query
   *   An associative array of URL query string parameters.
   *
   * @return \RightThisMinute\JWPlatform\Management\response\ResponseBody The parsed JSON response or null on 404.
   *   The parsed JSON response or null on 404.
   *
   * @throws \RightThisMinute\JWPlatform\exception\URLTooLong
   * @throws \RightThisMinute\JWPlatform\exception\UnexpectedResponse
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  public function get (string $endpoint, array $query=[]) : ResponseBody
  {
    $signed_query = $this->addRequiredParametersToQuery($query);
    $uri = $this->build_uri($endpoint, $signed_query);

    $response = $this->guzzle->get($uri);

    return $this->processResponse('GET', $uri, $response);
  }


  /**
   * Make an HTTP POST request to the JW management API.
   *
   * @param string $endpoint
   *   The endpoint path without the host or version prefixes.
   * @param string[] $query
   *   An associative array of URL query string parameters.
   * @param string[] $form_data
   *   An associative array of form data parameters that will be URL encoded
   *   and sent in the request body
   *
   * @return \RightThisMinute\JWPlatform\Management\response\ResponseBody The parsed JSON response or null on 404.
   *   The parsed JSON response or null on 404.
   *
   * @throws \RightThisMinute\JWPlatform\exception\URLTooLong
   * @throws \RightThisMinute\JWPlatform\exception\UnexpectedResponse
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  public function post (string $endpoint, array $query=[], array $form_data=[])
    : ResponseBody
  {
    $signed_query = $this->addRequiredParametersToQuery($query, $form_data);
    $uri = $this->build_uri($endpoint, $signed_query);

    $response = $this->guzzle->post($uri, ['form_params' => $form_data]);

    return $this->processResponse('POST', $uri, $response);
  }


  /**
   * @param string $method
   * @param string $uri
   * @param \Psr\Http\Message\ResponseInterface $response
   *
   * @return \RightThisMinute\JWPlatform\Management\response\ResponseBody
   * @throws \RightThisMinute\JWPlatform\exception\UnexpectedResponse
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  private function processResponse
    (string $method, string $uri, ResponseInterface $response) : ResponseBody
  {
    try {
      $json = json_decode
        ( $response->getBody()->getContents()
        , false
        , 512 #default
        , JSON_THROW_ON_ERROR );
    }
    catch (\Exception $exn) {
      throw new UnexpectedResponse("[$method $uri]", $response);
    }

    switch ($response->getStatusCode()) {
      case 400:
        return new BadRequestBody($json);

      case 404:
        return new NotFoundBody($json);

      case 429:
        return new TooManyRequestsBody($json);

      default:
        return new SuccessJSONBody($json);
    }
  }


  private function build_uri (string $endpoint, array $signed_query) : string
  {
    $endpoint = trim($endpoint, '/');
    $signed_query = http_build_query($signed_query);

    $uri = "/$endpoint?$signed_query";

    $length = strlen($uri);
    if ($length > MAX_REQUEST_URL_LENGTH)
      throw new URLTooLong($length);

    return $uri;
  }


  private function addRequiredParametersToQuery
    (array $query, array $form_data=[]) : array
  {
    $nonce = str_pad(mt_rand(0, 99999999), 8, STR_PAD_LEFT);

    $required =
      [ 'api_nonce' => $nonce
      , 'api_timestamp' => time()
      , 'api_key' => $this->key
      , 'api_format' => 'json'
      , 'api_kit' => static::API_KIT ];
    $query = array_merge($required, $query);

    $query['signature'] = $this->generateSignature($query, $form_data);

    return $query;
  }


  private function generateSignature (array $query, array $form_data=[])
    : string
  {
    $parameters = array_merge($query, $form_data);
    ksort($parameters);
    $parameters = map($parameters, function ($value, $key) {
      $key = rawurlencode($key);
      $value = rawurlencode($value);
      return "$key=$value";
    });
    $parameters = implode('&', $parameters);

    return sha1($parameters . $this->secret);
  }
}
