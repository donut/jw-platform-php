<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management;


use JsonException;
use Psr\Http\Message\ResponseInterface;
use RightThisMinute\JWPlatform\exception\URLTooLong;
use RightThisMinute\JWPlatform\Management\exception\BadRequestResponse;
use RightThisMinute\JWPlatform\Management\exception\ConflictResponse;
use RightThisMinute\JWPlatform\exception\InvalidResponseJSON;
use RightThisMinute\JWPlatform\Management\exception\MethodNotAllowedResponse;
use RightThisMinute\JWPlatform\Management\exception\NotFoundResponse;
use RightThisMinute\JWPlatform\Management\exception\TooManyRequestsResponse;
use RightThisMinute\JWPlatform\Management\exception\UnknownErrorResponse;
use RightThisMinute\JWPlatform\Management\response\SuccessJSONBody;
use RightThisMinute\StructureDecoder\exceptions\DecodeError;
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
   * @return SuccessJSONBody
   *   The parsed JSON response or null on 404.
   *
   * @throws BadRequestResponse
   * @throws ConflictResponse
   * @throws InvalidResponseJSON
   * @throws MethodNotAllowedResponse
   * @throws NotFoundResponse
   * @throws TooManyRequestsResponse
   * @throws UnknownErrorResponse
   * @throws URLTooLong
   * @throws DecodeError
   */
  public function get (string $endpoint, array $query=[]) : SuccessJSONBody
  {
    $signed_query = $this->addRequiredParametersToQuery($query);
    $uri = $this->buildURI($endpoint, $signed_query);

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
   * @return SuccessJSONBody
   *   The parsed JSON response.
   *
   * @throws BadRequestResponse
   * @throws ConflictResponse
   * @throws InvalidResponseJSON
   * @throws MethodNotAllowedResponse
   * @throws NotFoundResponse
   * @throws TooManyRequestsResponse
   * @throws UnknownErrorResponse
   * @throws URLTooLong
   * @throws DecodeError
   */
  public function post (string $endpoint, array $query=[], array $form_data=[])
    : SuccessJSONBody
  {
    $signed_query = $this->addRequiredParametersToQuery($query, $form_data);
    $uri = $this->buildURI($endpoint, $signed_query);

    $response = $this->guzzle->post($uri, ['form_params' => $form_data]);

    return $this->processResponse('POST', $uri, $response);
  }


  /**
   * @param string $method
   * @param string $uri
   * @param ResponseInterface $response
   *
   * @return SuccessJSONBody
   * @throws BadRequestResponse
   * @throws ConflictResponse
   * @throws InvalidResponseJSON
   * @throws MethodNotAllowedResponse
   * @throws NotFoundResponse
   * @throws TooManyRequestsResponse
   * @throws UnknownErrorResponse
   * @throws DecodeError
   */
  private function processResponse
    (string $method, string $uri, ResponseInterface $response) : SuccessJSONBody
  {
    try {
      $json = json_decode
        ( $response->getBody()->getContents()
        , false
        , 512 #default
        , JSON_THROW_ON_ERROR );
    }
    catch (JsonException $exn) {
      throw new InvalidResponseJSON($method, $uri, $response, $exn);
    }

    switch ($response->getStatusCode()) {
      case 200:
        return new SuccessJSONBody($json);

      case 400:
        throw new BadRequestResponse($method, $uri, $response, $json);

      case 404:
        throw new NotFoundResponse($method, $uri, $response, $json);

      case 405:
        throw new MethodNotAllowedResponse($method, $uri, $response, $json);

      case 409:
        throw new ConflictResponse($method, $uri, $response, $json);

      case 429:
        throw new TooManyRequestsResponse($method, $uri, $response, $json);

      default:
        throw new UnknownErrorResponse($method, $uri, $response, $json);
    }
  }


  private function buildURI (string $endpoint, array $signed_query) : string
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
    $nonce = str_pad
      ( (string)mt_rand(0, 99999999)
      , 8, ' ',  STR_PAD_LEFT );

    $required =
      [ 'api_nonce' => $nonce
      , 'api_timestamp' => time()
      , 'api_key' => $this->key
      , 'api_format' => 'json'
      , 'api_kit' => static::API_KIT ];
    $query = array_merge($required, $query);

    $query['api_signature'] = $this->generateSignature($query, $form_data);

    return $query;
  }


  private function generateSignature (array $query, array $form_data=[])
    : string
  {
    $parameters = array_merge($query, $form_data);
    ksort($parameters);
    $parameters = map($parameters, function ($value, $key) {
      $key = rawurlencode((string)$key);
      $value = rawurlencode((string)$value);
      return "$key=$value";
    });
    $parameters = implode('&', $parameters);

    return sha1($parameters . $this->secret);
  }
}
