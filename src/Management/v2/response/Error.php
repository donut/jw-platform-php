<?php


namespace RightThisMinute\JWPlatform\Management\v2\response;


use RightThisMinute\JWPlatform\common\DecoderTrait;
use RightThisMinute\StructureDecoder\exceptions\DecodeError;
use function RightThisMinute\StructureDecoder\field;
use RightThisMinute\StructureDecoder\types as T;


class Error extends Base
{
  /**
   * @var ErrorMessage[]
   */
  public $errors;

  /**
   * ErrorBody constructor.
   *
   * @param string[][] $headers
   * @param object $data =
   *  [ 'errors' =>
   *    [[ 'code' => (string)
   *     , 'description' => (string) ]]
   *
   * @throws DecodeError
   */
  public function __construct (array $headers, object $data)

  {
    parent::__construct($headers);
    $this->errors =
      field($data, 'errors', T\array_of(ErrorMessage::decoder()));
  }
}


class ErrorMessage
{
  use DecoderTrait;


  /**
   * @var string
   */
  public $code;

  /**
   * @var string
   */
  public $description;

  /**
   * ErrorMessage constructor.
   *
   * @param object $data
   *
   * @throws DecodeError
   */
  public function __construct (object $data)
  {
    $this->code = field($data, 'code', T\string());
    $this->description = field($data, 'description', T\string());
  }
}
