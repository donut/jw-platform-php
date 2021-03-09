<?php


namespace RightThisMinute\JWPlatform\Management\v2\exception;


use Throwable;


class WebhooksRequestInvalidJSON extends \Exception
{
  /**
   * @var string
   */
  private $body;


  /**
   * WebhooksRequestInvalidJSON constructor.
   *
   * @param string $body
   *   The body that failed parsing as JSON.
   * @param Throwable $exn
   *   The exception that was thrown when attempting to parse as JSON.
   */
  public function __construct (string $body, Throwable $exn)
  {
    $message =
      'Failed parsing body as JSON: ' . $exn->getMessage();
    parent::__construct($message, null, $exn);

    $this->body = $body;
  }

  /**
   * The body that failed parsing as JSON.
   *
   * @return string
   */
  public function getBody (): string
  {
    return $this->body;
  }
}
