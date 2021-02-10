<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\v1\response;


use RightThisMinute\JWPlatform\common\DecoderTrait;
use function RightThisMinute\StructureDecoder\field;
use RightThisMinute\StructureDecoder\types as T;


class TemplateFormatField
{
  use DecoderTrait;

  /** @var string */
  public $name;

  /** @var string */
  public $key;

  /**
   * ConversionTemplateFormatField constructor.
   *
   * @param object|array $data
   *
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  public function __construct ($data)
  {
    $this->name = field($data, 'name', T\string());
    $this->key = field($data, 'key', T\string());
  }
}
