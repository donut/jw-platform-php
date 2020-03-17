<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\response;


use function RightThisMinute\StructureDecoder\field;
use RightThisMinute\StructureDecoder\types as T;


class VideosConversionsListBody extends SuccessBody
{
  use ResultListTrait;

  /** @var \RightThisMinute\JWPlatform\Management\response\ConversionsFieldItem[] */
  public $conversions;

  /**
   * VideosConversionsListBody constructor.
   *
   * @param object|array $data
   *
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  public function __construct ($data)
  {
    parent::__construct($data);
    $this->constructResultLimitTrait($data);
    $this->conversions =
      field($data, 'conversions', ConversionsFieldItem::decoder());
  }
}


class ConversionsFieldItem
{
  use DecoderTrait;

  /** @var string */
  public $status;

  /** @var \RightThisMinute\JWPlatform\Management\response\ConversionTemplateField */
  public $template;

  /** @var string */
  public $mediatype;

  /** @var int */
  public $height;

  /** @var int */
  public $width;

  /** @var \RightThisMinute\JWPlatform\Management\response\ConversionLinkField */
  public $link;

  /** @var string */
  public $filesize;

  /** @var string */
  public $key;

  /** @var string */
  public $duration;


  /**
   * ConversionsFieldItem constructor.
   *
   * @param $data
   *
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  public function __construct ($data)
  {
    $this->status = field($data, 'status', T\string());
    $this->template =
      field($data, 'template', ConversionTemplateField::decoder());
    $this->mediatype = field($data, 'mediatype', T\string());
    $this->height = field($data, 'height', T\int());
    $this->width = field($data, 'width', T\int());
    $this->link =
      field($data, 'link', ConversionLinkField::decoder());
    $this->filesize = field($data, 'filesize', T\string());
    $this->key = field($data, 'key', T\string());
    $this->duration = field($data, 'duration', T\string());
  }
}


class ConversionTemplateField
{
  use DecoderTrait;

  /** @var bool */
  public $required;

  /** @var \RightThisMinute\JWPlatform\Management\response\TemplateFormatField */
  public $format;

  /** @var string */
  public $id;

  /** @var string */
  public $key;

  /** @var string */
  public $name;

  /**
   * ConversionTemplateField constructor.
   *
   * @param $data
   *
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  public function __construct ($data)
  {
    $this->required = field($data, 'required', T\bool());
    $this->format =
      field($data, 'format', TemplateFormatField::decoder());
    $this->id = field($data, 'id', T\string());
    $this->key = field($data, 'key', T\string());
    $this->name = field($data, 'name', T\string());
  }

}


class ConversionLinkField
{
  use DecoderTrait;

  /** @var string */
  public $path;

  /** @var string */
  public $protocol;

  /** @var string */
  public $address;

  /**
   * ConversionLinkField constructor.
   *
   * @param $data
   *
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  public function __construct ($data)
  {
    $this->path = field($data, 'path', T\string());
    $this->protocol = field($data, 'protocol', T\string());
    $this->address = field($data, 'address', T\string());
  }
}
