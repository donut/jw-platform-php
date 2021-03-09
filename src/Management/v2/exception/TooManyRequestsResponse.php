<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\v2\exception;


use RightThisMinute\StructureDecoder\exceptions\DecodeError;


class TooManyRequestsResponse extends ErrorResponse
{
  /**
   * Promote this exception to an instance of TooManyRequestsResponseWithRetry,
   * adding the option to retry the request.
   *
   * @param callable $func
   *   The function to be called to remake the request. This likely should be
   *   set to __FUNCTION__.
   * @param array $args
   *   The args to pass to that function. This likely should be set to
   *   func_get_args().
   *
   * @return TooManyRequestsResponseWithRetry
   *
   * @throws DecodeError
   */
  public function addRetry (callable $func, array $args)
    : TooManyRequestsResponseWithRetry
  {
    $remake_request = function()use($func, $args){ return $func(...$args); };

    return new TooManyRequestsResponseWithRetry
      ( $this->method
      , $this->uri
      , $this->response
      , $this->error
      , $remake_request );
  }
}
