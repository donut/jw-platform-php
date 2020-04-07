<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\response;


use RightThisMinute\JWPlatform\Management\response\DecoderTrait;
use RightThisMinute\JWPlatform\Management\response\RateLimitField;
use RightThisMinute\JWPlatform\Management\response\SuccessBody;
use function RightThisMinute\StructureDecoder\field;
use RightThisMinute\StructureDecoder\types as T;
use function RightThisMinute\StructureDecoder\optional_field;


class VideosShowBody extends SuccessBody
{
  /**
   * @var \RightThisMinute\JWPlatform\Management\videos\VideoField
   */
  public $video;

  public function __construct ($data)
  {
    parent::__construct($data);
    $this->video = field($data, 'video', VideoField::decoder());
  }
}


class VideoField
{
  use DecoderTrait;

  /**
   * @var string
   */
  public $key;

  /**
   * @var string
   */
  public $title;

  /**
   * @var string
   */
  public $status;

  /**
   * @var int
   */
  public $date;

  /**
   * @var int
   */
  public $updated;

  /**
   * @var int|null
   */
  public $expires_date;

  /**
   * @var \RightThisMinute\JWPlatform\Management\videos\VideoErrorField|null
   */
  public $error;

  /**
   * @var string[]
   */
  public $tags;

  /**
   * @var string|null
   */
  public $category;

  /**
   * @var string|null
   */
  public $description;

  /**
   * @var string|null
   */
  public $author;

  /**
   * @var array
   */
  public $custom;

  /**
   * @var string|null
   */
  public $link;

  /**
   * @var int
   */
  public $views;

  /**
   * @var string
   */
  public $sourcetype;

  /**
   * @var string|null
   */
  public $sourceformat;

  /**
   * @var string|null
   */
  public $sourceurl;

  /**
   * @var string|null
   */
  public $upload_session_id;

  /**
   * @var string
   */
  public $mediatype;

  /**
   * @var string
   */
  public $size;

  /**
   * @var string
   */
  public $md5;

  /**
   * @var string
   */
  public $duration;

  /**
   * @var string|null
   */
  public $trim_out_point;

  /**
   * @var string|null
   */
  public $protectionrule_key;

  /**
   * VideoField constructor.
   *
   * @param $data
   *
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  public function __construct ($data)
  {
    $tags = function($v) {
      $tags = T\string()($v);
      return preg_split
      ('/(\s*,\s*)+/', $tags, PREG_SPLIT_NO_EMPTY);
    };

    $this->key = field($data, 'key', T\string());
    $this->title = field($data, 'title', T\string());

    $this->status = field($data, 'status', T\string());
    $this->date = field($data, 'date', T\int());
    $this->updated = field($data, 'updated', T\int());
    $this->expires_date = optional_field($data, 'expires_date', T\int());
    $this->error =
      optional_field($data, 'error', VideoErrorField::decoder());

    $this->tags = optional_field($data, 'tags', $tags, []);
    $this->category = optional_field($data, 'category', T\string());
    $this->description = optional_field($data, 'description', T\string());
    $this->author = optional_field($data, 'author', T\string());
    $this->custom = field($data, 'custom', T\dict_of(T\string()));
    $this->link = optional_field($data, 'link', T\string());
    $this->views = field($data, 'views', T\int());

    $this->sourcetype = field($data, 'sourcetype', T\string());
    $this->sourceformat =
      optional_field($data, 'sourceformat', T\string());
    $this->sourceurl = optional_field($data, 'sourceurl', T\string());
    $this->upload_session_id =
      optional_field($data, 'upload_session_id', T\string());

    $this->mediatype = field($data, 'mediatype', T\string());
    $this->size = field($data, 'size', T\string());
    $this->md5 = field($data, 'md5', T\string());
    $this->duration = field($data, 'duration', T\string());
    $this->trim_out_point =
      optional_field($data, 'trim_out_point', T\string());

    $this->protectionrule_key =
      optional_field($data, 'protectionrule_key', T\string());
  }
}


class VideoErrorField
{
  use DecoderTrait;

  /**
   * @var string
   */
  public $message;

  /**
   * VideoErrorField constructor.
   *
   * @param object|array $data
   *
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  public function __construct ($data)
  {
    $this->message = field($data, 'message', T\string());
  }

}
