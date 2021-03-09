<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\v2\response;


use Psr\Http\Message\ResponseInterface;
use RightThisMinute\JWPlatform\common\DecoderTrait;
use RightThisMinute\StructureDecoder\exceptions\DecodeError;
use function Functional\last;
use function Functional\map;
use function RightThisMinute\StructureDecoder\field;
use RightThisMinute\StructureDecoder\types as T;


abstract class Base
{
  /**
   * The JW specific headers.
   *
   * @var BaseJWHeaders
   */
  public $headers;

  /**
   * Base constructor.
   *
   * @param BaseJWHeaders|string[][] $headers
   *   The headers array as returned by `ResponseInterface::getHeaders()`.
   *
   * @throws DecodeError
   */
  public function __construct ($headers)
  {
    $this->headers = ($headers instanceof BaseJWHeaders)
      ? $this->headers = $headers : new BaseJWHeaders($headers);
  }
}


class BaseJWHeaders
{
  use DecoderTrait;

  /**
   * @var string
   */
  public $dashboard_page;

  /**
   * @var bool
   */
  public $proxied_request;

  /**
   * @var string
   */
  public $request_id;

  /**
   * @var int
   */
  public $request_limit;

  /**
   * @var int
   */
  public $request_remaining;


  /**
   * BaseJWHeaders constructor.
   *
   * @param string[][] $headers
   *   The headers array as returned by `ResponseInterface::getHeaders()`.
   *
   * @throws DecodeError
   */
  public function __construct (array $headers)
  {
    $headers = map($headers, function($h){ return last($h); });
    $headers = array_change_key_case($headers, CASE_LOWER);

    $this->dashboard_page =
      field($headers, 'jw-dashboard-page', T\string());
    $this->proxied_request =
      field($headers, 'jw-proxied-request', T\bool_of_mixed());
    $this->request_id =
      field($headers, 'jw-request-id', T\string());
    $this->request_limit =
      field($headers, 'jw-request-limit', T\int_of_string());
    $this->request_remaining =
      field($headers, 'jw-request-remaining', T\int_of_string());
  }
}
