<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\v1\exception;


use RightThisMinute\StructureDecoder\exceptions\DecodeError;


class TooManyRequestsResponse extends ErrorWithRateLimitResponse
{
  /**
   * Promote this exception to an instance of TooManyRequestsResponseWithRetry,
   * adding the option to retry the request.
   *
   * @param callable $remake_request
   *   A function that takes no arguments and repeats the failed request when
   *   called.
   *
   * @return TooManyRequestsResponseWithRetry
   *
   * @throws DecodeError
   */
  public function addRetry (callable $remake_request)
    : TooManyRequestsResponseWithRetry
  {
    return new TooManyRequestsResponseWithRetry
      ($this->method, $this->uri, $this->response, $this->body, $remake_request);
  }
}
