<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\response;


use function RightThisMinute\StructureDecoder\field;
use RightThisMinute\StructureDecoder\types as T;
use function RightThisMinute\StructureDecoder\optional_field;


class AccountsTemplatesListBody extends SuccessBody
{
  /** @var string */
  public $name;

  /** @var \RightThisMinute\JWPlatform\Management\response\TemplateFormatField */
  public $format;

  /** @var string */
  public $default;

  /** @var int */
  public $id;

  /** @var mixed|null */
  public $min_scale_width;

  /** @var int|null */
  public $width;

  /** @var bool|null */
  public $upscale;

  /** @var string */
  public $key;

  /** @var bool */
  public $required;


  public function __construct ($data)
  {
    parent::__construct($data);

    $this->name = field($data, 'name', T\string());
    $this->format =
      field($data, 'format', TemplateFormatField::decoder());
    $this->id = field($data, 'id', T\int());
    $this->default = field($data, 'default', T\string());
    $this->min_scale_width =
      optional_field($data, 'min_scale_width', T\mixed());
    $this->width = optional_field($data, 'width', T\int());
    $this->upscale = optional_field($data, 'upscale', T\bool());
    $this->key = field($data, 'key', T\string());
    $this->required = field($data, 'required', T\bool());
  }
}


class TemplateQualityField
{
  use DecoderTrait;

  /** @var int|null */
  public $audio;

  /** @var int|null */
  public $video;

  /**
   * TemplateQualityField constructor.
   *
   * @param object|array $data
   *
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  public function __construct ($data)
  {
    $this->audio = optional_field($data, 'audio', T\int());
    $this->video = optional_field($data, 'video', T\int());
  }
}
