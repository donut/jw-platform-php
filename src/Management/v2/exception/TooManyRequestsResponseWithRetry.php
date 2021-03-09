<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\v2\exception;


use Psr\Http\Message\ResponseInterface;
use RightThisMinute\JWPlatform\Management\v2\response\Error;
use RightThisMinute\JWPlatform\Management\v2\response\Success;
use RightThisMinute\StructureDecoder\exceptions\DecodeError;


class TooManyRequestsResponseWithRetry extends TooManyRequestsResponse
{
  /**
   * @var callable
   */
  private $remakeRequest;

  /**
   * TooManyRequestsResponseWithRetry constructor.
   *
   * @param string $method
   * @param string $uri
   * @param ResponseInterface $response
   * @param Error $error
   * @param callable $remake_request
   *
   * @throws DecodeError
   */
  public function __construct
    ( string $method
    , string $uri
    , ResponseInterface $response
    , Error $error
    , callable $remake_request )
  {
    parent::__construct($method, $uri, $response, $error);
    $this->remakeRequest = $remake_request;
  }

  /**
   * Re-make the request that failed due to hitting the rate limit when the
   * rate limit resets, as identified in the response.
   *
   * @return Success|null
   *   See the original endpoint for details.
   */
  public function retryRequestWhenRateLimitResets () : ?Success
  {
    sleep(time() + 60);

    $remake_request = $this->remakeRequest;
    return $remake_request();
  }
}
