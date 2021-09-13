<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\v2\response;


use RightThisMinute\JWPlatform\common\DecoderTrait;
use RightThisMinute\StructureDecoder\exceptions\DecodeError;
use RightThisMinute\StructureDecoder\exceptions\WrongType;
use function RightThisMinute\StructureDecoder\field;
use RightThisMinute\StructureDecoder\types as T;
use function RightThisMinute\StructureDecoder\optional_field;


class Media
{
  use DecoderTrait;
  
  /**
   * @var string|null
   */
  
  public $schema;
  
  /**
   * Unique identifier for a resource
   *
   * @var string
   */
  public $id;
  
  /**
   * Date and time at which the resource was created
   *
   * @var string
   */
  public $created;
  
  /**
   * Date and time at which the resource was most recently modified
   *
   * @var string
   */
  public $last_modified;
  
  /**
   * Name of the type of resource.
   *
   * @var string
   */
  public $type;
  
  /**
   * Ancilliary resources associated to the primary resource being read
   *
   * @var array<string, RelationshipsFieldValue>
   */
  public $relationships;
  
  /**
   * @var MediaMetadataField
   */
  public $metadata;
  
  /**
   * Media upload status
   *
   * @var string
   */
  public $status;
  
  /**
   * @var string
   */
  public $media_type;
  
  /**
   * Indicates whether the media is hosted with JW Player or not
   *
   * @var string
   */
  public $hosting_type;
  
  /**
   * @var string|null
   */
  public $mime_type;
  
  /**
   * Message describing an issue uploading or processing the media
   *
   * @var string|null
   */
  public $error_message;
  
  /**
   * Length of the media in seconds
   *
   * @var float
   */
  public $duration;
  
  /**
   * Starting point to trim the video
   *
   * @var string
   */
  public $trim_in_point;
  
  /**
   * Ending point to trim the video
   *
   * @var string
   */
  public $trim_out_point;
  
  /**
   * Media constructor
   *
   * @param $data
   * @throws DecodeError
   */
  public function __construct ($data)
  {
    $decode_float = function($value) : float {
      if (!is_float($value))
        throw new WrongType($value, 'float');
  
      return $value;
    };
    
    $this->schema = optional_field($data, 'schema', T\string());
    $this->id = field($data, 'id', T\string());
    $this->created = field($data, 'created', T\string());
    $this->last_modified = field($data, 'last_modified', T\string());
    $this->type = field($data, 'type', T\string());
    
    $this->relationships = field
      ( $data
      , 'relationships'
      , T\dict_of(RelationshipsFieldValue::decoder()) );
    
    $this->metadata
      = field($data, 'metadata', MediaMetadataField::decoder());
    
    $this->status = field($data, 'status', T\string());
    $this->media_type = field($data, 'media_type', T\string());
    $this->mime_type = optional_field($data, 'mime_type', T\string());
    $this->duration = field($data, 'duration', $decode_float);
    
    $this->error_message
      = optional_field($data, 'error_message', T\string());
  }
}


class RelationshipsFieldValue
{
  use DecoderTrait;
  
  /**
   * Unique identifier for a resource
   *
   * @var string
   */
  public $id;
  
  /**
   * Resource type
   *
   * @var string
   */
  public $type;
  
  /**
   * @param $data
   * @throws DecodeError
   */
  public function __construct ($data)
  {
    $this->id = field($data, 'id', T\string());
    $this->type = field($data, 'type', T\string());
  }
}


class MediaMetadataField
{
  use DecoderTrait;
  
  /**
   * @var string
   */
  public $title;
  
  /**
   * @var string|null
   */
  public $description;
  
  /**
   * @var string|null
   */
  public $author;
  
  /**
   * URL of the page where this media is published
   *
   * @var string|null
   */
  public $permalink;
  
  /**
   * IAB category
   *
   * @var string|null
   */
  public $category;
  
  /**
   * Start date and time in ISO 8601 format when media is available for
   * streaming
   *
   * @var string
   */
  public $publish_start_date;
  
  /**
   * End date and time in ISO 8601 format when media is no longer available for
   * streaming
   *
   * @var string|null
   */
  public $publish_end_date;
  
  /**
   * @var string[]
   */
  public $tags;
  
  /**
   * Two-letter ISO-639-1 language code for the media
   *
   * This is used to index the media by language, to provide relevant playlist
   * recommendations.
   *
   * @var string|null
   */
  public $language;
  
  /**
   * User-generated key-value pairs
   *
   * @var array<string, string>
   */
  public $custom_params;
  
  /**
   * @param $data
   * @throws DecodeError
   */
  public function __construct ($data)
  {
    $this->title = field($data, 'title', T\string());
    $this->description = optional_field($data, 'description', T\string());
    $this->author = optional_field($data, 'author', T\string());
    $this->permalink = optional_field($data, 'permalink', T\string());
    $this->category = optional_field($data, 'category', T\string());
    $this->publish_start_date
      = optional_field($data, 'publish_start_date', T\string());
    $this->publish_end_date
      = optional_field($data, 'publish_end_date', T\string());
    $this->tags = field($data, 'tags', T\array_of(T\string()));
    $this->language = optional_field($data, 'language', T\string());
    $this->custom_params
      = field($data, 'custom_params', T\dict_of(T\string()));
  }
}
