<?php


namespace RightThisMinute\JWPlatform\Management\v2;


use GuzzleHttp\Exception\GuzzleException;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use RightThisMinute\JWPlatform\exception\InvalidResponseJSON;
use RightThisMinute\JWPlatform\Management\v2\exception\BadRequestResponse;
use RightThisMinute\JWPlatform\Management\v2\exception\ConflictResponse;
use RightThisMinute\JWPlatform\Management\v2\exception\DefaultSiteIDNotSet;
use RightThisMinute\JWPlatform\Management\v2\exception\MethodNotAllowedResponse;
use RightThisMinute\JWPlatform\Management\v2\exception\NotFoundResponse;
use RightThisMinute\JWPlatform\Management\v2\exception\PreconditionFailedResponse;
use RightThisMinute\JWPlatform\Management\v2\exception\TooManyRequestsResponse;
use RightThisMinute\JWPlatform\Management\v2\exception\UnknownErrorResponse;
use RightThisMinute\JWPlatform\Management\v2\response;
use RightThisMinute\StructureDecoder\exceptions\DecodeError;


class Client
{
  const BASE_URI = 'https://api.jwplayer.com/v2/';

  /**
   * Unique user credential
   *
   * 1. From your API Credentials page, scroll down to the V2 API Credentials
   *    section.
   * 2. Click Show Credentials in the row of the relevant API key name.
   *    NOTE: If no API key names exist, type a new API key name, select a
   *    permission level, and click Add New API Key. Your account must have the
   *    Admin permission to create a new API key.
   * 3. Copy the Secret.
   *
   * @var string
   */
  private $secret;

  /**
   * The site ID is a unique identifier for an account property. This value is
   * sometimes referred to as the Property ID.
   *
   * You can retrieve the site ID from your dashboard:
   *
   * 1. Go to the Properties page.
   * 2. In the Property Name column, locate the name of a property.
   * 3. Copy the Property ID value associated with the property.
   *
   * @note Check the requirements for each API route. Not all routes require
   *       the site_id to be defined.
   *
   * @see https://developer.jwplayer.com/jwplayer/reference#section-site-id
   *
   * @var string|null
   */
  private $site_id;

  public function defaultSiteID () : ?string { return $this->site_id; }

  /**
   * @var \GuzzleHttp\Client
   */
  private $guzzle;

  /**
   * Client constructor.
   *
   * @param string $secret
   *   The v2 API secret.
   * @param string|null $site_id
   *   The default site ID to use for requests that require a site ID. If
   *   `null`, then a site ID must be specified with each request that requires
   *   a site ID.
   *
   */
  public function __construct (string $secret, ?string $site_id)
  {
    $this->secret = $secret;
    $this->site_id = $site_id;

    $this->guzzle = new \GuzzleHttp\Client
      ([ 'base_uri' => static::BASE_URI
       , 'http_errors' => false
       , 'headers' => ['Authorization' => $secret] ]);
  }


  /**
   * Make a request to the v2 Management API.
   *
   * @param bool|string $to_site
   *   Whether or not the request should be made to a specific site. If value is
   *   a string, that will be used for the site ID instead of the default set
   *   on class construction (if any was set).
   * @param string $method
   *   The HTTP method to use.
   * @param string $endpoint
   *   The v2 Management API to send the request to. This is the path after the
   *   version and without a query string. If the request requires a site ID,
   *   you don't need to include the `/site/{site_Id/` prefix, it will be added.
   * @param array $query
   *   Any query variables. In the format of `[{key} => {value}]`.
   * @param string|object|null $body
   *   A request body, if necessary. If value is an object, it will be converted
   *   to a JSON string.
   *
   * @return response\Success
   *
   * @throws BadRequestResponse
   * @throws ConflictResponse
   * @throws DecodeError
   * @throws DefaultSiteIDNotSet
   * @throws InvalidResponseJSON
   * @throws MethodNotAllowedResponse
   * @throws NotFoundResponse
   * @throws PreconditionFailedResponse
   * @throws TooManyRequestsResponse
   * @throws UnknownErrorResponse
   */
  private function makeRequest
    ( $to_site
    , string $method
    , string $endpoint
    , array $query=[]
    , $body=null )
    : response\Success
  {
    $endpoint = trim($endpoint, '/');

    if ($to_site === true) {
      if (!isset($this->site_id))
        throw new DefaultSiteIDNotSet();
      else
        $site_id = $this->site_id;
    }
    elseif ($to_site !== false)
      $site_id = $to_site;

    if (isset($site_id))
      $endpoint = "sites/$this->site_id/$endpoint";

    $query = count($query) > 0 ? '?' . http_build_query($query) : '';
    $uri = self::BASE_URI . $endpoint . $query;
    $options = [];

    if (isset($body)) {
      $body = is_object($body) ? json_encode($body) : $body;
      $options['body'] = $body;
    }

    # Instead of using the `Guzzle\Client::request()` method, which allows us
    # to specify an arbitrary HTTP method, we do this. The `request()` method
    # will throw an exception if `$method` is unsupported. Since this code is
    # private and should never be run by an end user of this library, if
    # `$method` is invalid, we done screwed up real good and clearly didn't
    # test our code. To avoid end users being nagged by their IDEs to catch an
    # exception they should never run into, we do this. The individual HTTP
    # method based methods on `Guzzle\Client` don't throw exceptions.
    $method = strtolower($method);
    switch ($method) {
      case 'get':
        $response = $this->guzzle->get($uri, $options);
        break;
      case 'post':
        $response = $this->guzzle->post($uri, $options);
        break;
      case 'put':
        $response = $this->guzzle->put($uri, $options);
        break;
      case 'patch':
        $response = $this->guzzle->patch($uri, $options);
        break;
      case 'delete':
        $response = $this->guzzle->delete($uri, $options);
        break;
      default:
        # We've really screwed up. Let PHP error out instead of throwing an
        # exception that users of this library might try to handle pointlessly.
        /** @var ResponseInterface $response */
        $response = $this->guzzle->$method($uri, $options);
        break;
    }

    return $this->processResponse($method, $uri, $response);
  }


  /**
   * @param string $method
   * @param string $uri
   * @param ResponseInterface $response
   *
   * @return response\Success
   *
   * @throws BadRequestResponse
   * @throws ConflictResponse
   * @throws InvalidResponseJSON
   * @throws MethodNotAllowedResponse
   * @throws NotFoundResponse
   * @throws PreconditionFailedResponse
   * @throws TooManyRequestsResponse
   * @throws UnknownErrorResponse
   * @throws DecodeError
   */
  private function processResponse
    (string $method, string $uri, ResponseInterface $response)
    : response\Success
  {
    if ($response->getStatusCode() !== 204) {
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
    }

    switch ($response->getStatusCode()) {
      case 200:
      case 201:
        return new response\SuccessWithBody($response->getHeaders(), $json);

      case 204:
        return new response\Success($response->getHeaders());

      case 400:
        throw new BadRequestResponse($method, $uri, $response, $json);

      case 404:
        throw new NotFoundResponse($method, $uri, $response, $json);

      case 405:
        throw new MethodNotAllowedResponse($method, $uri, $response, $json);

      case 409:
        throw new ConflictResponse($method, $uri, $response, $json);

      case 412:
        throw new PreconditionFailedResponse($method, $uri, $response, $json);

      case 429:
        throw new TooManyRequestsResponse($method, $uri, $response, $json);

      default:
        throw new UnknownErrorResponse($method, $uri, $response, $json);
    }
  }


  /**
   * Send a GET request to the v2 Management API.
   *
   * @param bool|string $to_site
   *   Whether or not the request should be made to a specific site. If value is
   *   a string, that will be used for the site ID instead of the default set
   *   on class construction (if any was set).
   * @param string $endpoint
   *   The v2 Management API to send the request to. This is the path after the
   *   version and without a query string. If the request requires a site ID,
   *   you don't need to include the `/site/{site_Id/` prefix, it will be added.
   * @param array $query
   *   Any query variables. In the format of `[{key} => {value}]`.
   *
   * @return response\Success
   *
   * @throws BadRequestResponse
   * @throws ConflictResponse
   * @throws DecodeError
   * @throws DefaultSiteIDNotSet
   * @throws InvalidResponseJSON
   * @throws MethodNotAllowedResponse
   * @throws NotFoundResponse
   * @throws PreconditionFailedResponse
   * @throws TooManyRequestsResponse
   * @throws UnknownErrorResponse
   */
  public function get ($to_site, string $endpoint, array $query=[])
    : response\Success
  {
    return $this->makeRequest($to_site, 'get', $endpoint, $query);
  }


  /**
   * Send a POST request to the v2 Management API.
   *
   * @param bool|string $to_site
   *   Whether or not the request should be made to a specific site. If value is
   *   a string, that will be used for the site ID instead of the default set
   *   on class construction (if any was set).
   * @param string $endpoint
   *   The v2 Management API to send the request to. This is the path after the
   *   version and without a query string. If the request requires a site ID,
   *   you don't need to include the `/site/{site_Id/` prefix, it will be added.
   * @param array $query
   *   Any query variables. In the format of `[{key} => {value}]`.
   * @param string|object|null $body
   *   A request body, if necessary. If value is an object, it will be converted
   *   to a JSON string.
   *
   * @return response\Success
   *
   * @throws BadRequestResponse
   * @throws ConflictResponse
   * @throws DecodeError
   * @throws DefaultSiteIDNotSet
   * @throws InvalidResponseJSON
   * @throws MethodNotAllowedResponse
   * @throws NotFoundResponse
   * @throws PreconditionFailedResponse
   * @throws TooManyRequestsResponse
   * @throws UnknownErrorResponse
   */
  public function post
    ($to_site, string $endpoint, array $query=[], $body=null)
    : response\Success
  {
    return $this->makeRequest($to_site, 'post', $endpoint, $query, $body);
  }


  /**
   * Send a PUT request to the v2 Management API.
   *
   * @param bool|string $to_site
   *   Whether or not the request should be made to a specific site. If value is
   *   a string, that will be used for the site ID instead of the default set
   *   on class construction (if any was set).
   * @param string $endpoint
   *   The v2 Management API to send the request to. This is the path after the
   *   version and without a query string. If the request requires a site ID,
   *   you don't need to include the `/site/{site_Id/` prefix, it will be added.
   * @param array $query
   *   Any query variables. In the format of `[{key} => {value}]`.
   * @param string|object|null $body
   *   A request body, if necessary. If value is an object, it will be converted
   *   to a JSON string.
   *
   * @return response\Success
   *
   * @throws BadRequestResponse
   * @throws ConflictResponse
   * @throws DecodeError
   * @throws DefaultSiteIDNotSet
   * @throws InvalidResponseJSON
   * @throws MethodNotAllowedResponse
   * @throws NotFoundResponse
   * @throws PreconditionFailedResponse
   * @throws TooManyRequestsResponse
   * @throws UnknownErrorResponse
   */
  public function put
    ($to_site, string $endpoint, array $query=[], $body=null)
    : response\Success
  {
    return $this->makeRequest
      ($to_site, 'put', $endpoint, $query, $body);
  }


  /**
   * Send a PATCH request to the v2 Management API.
   *
   * @param bool|string $to_site
   *   Whether or not the request should be made to a specific site. If value is
   *   a string, that will be used for the site ID instead of the default set
   *   on class construction (if any was set).
   * @param string $endpoint
   *   The v2 Management API to send the request to. This is the path after the
   *   version and without a query string. If the request requires a site ID,
   *   you don't need to include the `/site/{site_Id/` prefix, it will be added.
   * @param array $query
   *   Any query variables. In the format of `[{key} => {value}]`.
   * @param string|object|null $body
   *   A request body, if necessary. If value is an object, it will be converted
   *   to a JSON string.
   *
   * @return response\Success
   *
   * @throws BadRequestResponse
   * @throws ConflictResponse
   * @throws DecodeError
   * @throws DefaultSiteIDNotSet
   * @throws InvalidResponseJSON
   * @throws MethodNotAllowedResponse
   * @throws NotFoundResponse
   * @throws PreconditionFailedResponse
   * @throws TooManyRequestsResponse
   * @throws UnknownErrorResponse
   */
  public function patch
    ($to_site, string $endpoint, array $query=[], $body=null)
    : response\Success
  {
    return $this->makeRequest
      ($to_site, 'patch', $endpoint, $query, $body);
  }


  /**
   * Send a DELETE request to the v2 Management API.
   *
   * @param bool|string $to_site
   *   Whether or not the request should be made to a specific site. If value is
   *   a string, that will be used for the site ID instead of the default set
   *   on class construction (if any was set).
   * @param string $endpoint
   *   The v2 Management API to send the request to. This is the path after the
   *   version and without a query string. If the request requires a site ID,
   *   you don't need to include the `/site/{site_Id/` prefix, it will be added.
   * @param array $query
   *   Any query variables. In the format of `[{key} => {value}]`.
   * @param string|object|null $body
   *   A request body, if necessary. If value is an object, it will be converted
   *   to a JSON string.
   *
   * @return response\Success
   *
   * @throws BadRequestResponse
   * @throws ConflictResponse
   * @throws DecodeError
   * @throws DefaultSiteIDNotSet
   * @throws InvalidResponseJSON
   * @throws MethodNotAllowedResponse
   * @throws NotFoundResponse
   * @throws PreconditionFailedResponse
   * @throws TooManyRequestsResponse
   * @throws UnknownErrorResponse
   */
  public function delete
    ($to_site, string $endpoint, array $query=[], $body=null)
    : response\Success
  {
    return $this->makeRequest
      ($to_site, 'delete', $endpoint, $query, $body);
  }
}
