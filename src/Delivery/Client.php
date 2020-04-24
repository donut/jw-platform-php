<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Delivery;


use RightThisMinute\JWPlatform\Delivery\exception\BadRequestResponse;
use RightThisMinute\JWPlatform\Delivery\exception\ErrorResponse;
use RightThisMinute\JWPlatform\Delivery\exception\NotFoundResponse;
use RightThisMinute\JWPlatform\exception\InvalidResponseJSON;
use RightThisMinute\JWPlatform\exception\UnexpectedResponse;
use Psr\Http\Message\ResponseInterface;
use function Functional\some;


class Client
{
  protected const BASE_URI = 'https://cdn.jwplayer.com/v2/';

  static public function singleton () : self
  {
    static $client;

    if (!isset($client))
      $client = new static();

    return $client;
  }

  /**
   * @var \GuzzleHttp\Client
   */
  private $guzzle;


  public function __construct ()
  {
    $this->guzzle = new \GuzzleHttp\Client(
      [ 'base_uri' => static::BASE_URI
      , 'http_errors' => false ]
    );
  }


  /**
   * @param string $endpoint
   * @param array $query
   *   [ 'format' => ['json', 'mrss', 'googledfp', 'legacyrss'][$any] ]
   *
   * @param \Psr\Http\Message\ResponseInterface|null $response
   *
   * @return object|ResponseInterface
   *   If $query['format'] is unset or set to 'json', this will be the decoded
   *   JSON object. Otherwise, it will be the ResponseInterface instance.
   *
   * @throws \RightThisMinute\JWPlatform\Delivery\exception\BadRequestResponse
   * @throws \RightThisMinute\JWPlatform\Delivery\exception\ErrorResponse
   * @throws \RightThisMinute\JWPlatform\Delivery\exception\NotFoundResponse
   * @throws \RightThisMinute\JWPlatform\exception\InvalidResponseJSON
   * @throws \RightThisMinute\JWPlatform\exception\UnexpectedResponse
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  public function get
    (string $endpoint, array $query=[], ?ResponseInterface &$response=null)
    : object
  {
    if (!isset($query['format']))
      $query['format'] = 'json';

    $expect_json = strtolower($query['format']) === 'json';

    $query = count($query) > 0 ? '?' . http_build_query($query) : '';
    $uri = "$endpoint$query";
    $response = $this->guzzle->get($uri);

    if ($expect_json) {
      $content_type = $response->getHeader('content-type');
      $probably_json = some($content_type, function ($type) {
        return $type === 'application/json'
          || strpos($type, 'application/json;') === 0;
      });
      if (!$probably_json) {
        $content_type = implode(', ', $content_type);
        throw new UnexpectedResponse
          ( 'GET', $uri
          , "Expected JSON, got $content_type."
          , $response );
      }

      return $this->processJSONResponse('GET', $uri, $response);
    }

    return $response;
  }


  /**
   * @param string $method
   * @param string $uri
   * @param \Psr\Http\Message\ResponseInterface $response
   *
   * @return object
   * @throws \RightThisMinute\JWPlatform\Delivery\exception\BadRequestResponse
   * @throws \RightThisMinute\JWPlatform\Delivery\exception\ErrorResponse
   * @throws \RightThisMinute\JWPlatform\Delivery\exception\NotFoundResponse
   * @throws \RightThisMinute\JWPlatform\exception\InvalidResponseJSON
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  protected function processJSONResponse
    (string $method, string $uri, ResponseInterface $response) : object
  {
    try {
      $json = json_decode
        ( $response->getBody()->getContents()
        , false
        , 512 #default
        , JSON_THROW_ON_ERROR );
    }
    catch (\JsonException $exn) {
      throw new InvalidResponseJSON($method, $uri, $response, $exn);
    }

    switch ($response->getStatusCode()) {
      case 200:
        return $json;

      case 400:
        throw new BadRequestResponse($method, $uri, $response, $json);

      case 404:
        throw new NotFoundResponse($method, $uri, $response, $json);

      default:
        throw new ErrorResponse($method, $uri, $response, $json);
    }
  }
}
