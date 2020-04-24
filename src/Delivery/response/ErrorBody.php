<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Delivery\response;


use function RightThisMinute\StructureDecoder\field;
use function RightThisMinute\StructureDecoder\types\string;

class ErrorBody
{
  /** @var string */
  public $message;


  /**
   * ErrorBody constructor.
   *
   * @param object $json_body
   *
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  public function __construct (object $json_body)
  {
    $this->message = field($json_body, 'message', string());
  }
}
