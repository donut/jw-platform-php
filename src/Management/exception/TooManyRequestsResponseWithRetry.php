<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\exception;


use Psr\Http\Message\ResponseInterface;
use RightThisMinute\JWPlatform\Management\response\SuccessBody;
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
   * @param object $json_body
   * @param callable $remake_request
   *
   * @throws DecodeError
   */
  public function __construct
    ( string $method
    , string $uri
    , ResponseInterface $response
    , object $json_body
    , callable $remake_request )
  {
    parent::__construct($method, $uri, $response, $json_body);
    $this->remakeRequest = $remake_request;
  }

  /**
   * Re-make the request that failed due to hitting the rate limit when the
   * rate limit resets, as identified in the response.
   *
   * @return SuccessBody|null
   *   See the original endpoint for details.
   */
  public function retryRequestWhenRateLimitResets () : ?SuccessBody
  {
    $wake_up_in = $this->body->rate_limit->reset - time();
    $wake_up_in = max($wake_up_in, 1);
    sleep($wake_up_in);

    $remake_request = $this->remakeRequest;
    return $remake_request();
  }
}
