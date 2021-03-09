<?php


namespace RightThisMinute\JWPlatform\Management\v2\response;


use RightThisMinute\JWPlatform\common\DecoderTrait;
use RightThisMinute\StructureDecoder\exceptions\DecodeError;
use function RightThisMinute\StructureDecoder\field;
use RightThisMinute\StructureDecoder\types as T;


class Webhook
{
  use DecoderTrait;

  /**
   * Name of the type of resource
   *
   * @var string
   */
  public $type;

  /**
   * Unique identifier for a resource
   *
   * @var string
   */
  public $id;

  /**
   * Date and time at which the resource was created
   *
   * Example: 2019-09-25T15:29:11.042095+00:00
   *
   * @var string
   */
  public $created;

  /**
   * Date and time at which the resource was most recently modified
   *
   * Example: 2019-09-25T15:29:11.042095+00:00
   *
   * @var string
   */
  public $last_modified;

  /**
   * @var RelationshipsField
   */
  public $relationships;

  /**
   * @var WebhookMetadataField
   */
  public $metadata;

  /**
   * Webhook constructor.
   *
   * @param $data
   *
   * @throws DecodeError
   */
  public function __construct ($data)
  {
    $this->id = field($data, 'id', T\string());
    $this->created = field($data, 'created', T\string());
    $this->last_modified = field($data, 'last_modified', T\string());
    $this->relationships = field
      ($data, 'relationships', RelationshipsField::decoder());
    $this->metadata = field
      ($data, 'metadata', WebhookMetadataField::decoder());
  }
}


/**
 * Class WebhookMetadataField
 *
 * @see https://developer.jwplayer.com/jwplayer/reference#section-get-v2-webhooks-webhooks-metadata
 *
 * @package RightThisMinute\JWPlatform\Management\v2\response
 */
class WebhookMetadataField
{
  use DecoderTrait;

  /**
   * Display name for webhook subscription
   *
   * @var string
   */
  public $name;

  /**
   * Description for the webhook subscription
   *
   * @var string
   */
  public $description;

  /**
   * Subscribed events that trigger a notification
   *
   * Possible values include:
   *
   * • conversions_complete
   * • media_available
   * • media_update
   *
   * @var string[] =
   *  ['conversions_complete', 'media_available', 'media_update'][$any]
   */
  public $events;

  /**
   * List of site IDs corresponding to the webhook
   *
   * Each site ID is the unique identifier for each property. You can specify
   * any number of site IDs in this array.
   *
   * @var string[]
   */
  public $site_ids;

  /**
   * Valid HTTPS endpoint to be notified upon event(s) occurring
   *
   * @var string
   */
  public $webhook_url;


  /**
   * WebhookMetadataField constructor.
   *
   * @param object|array $data
   *
   * @throws DecodeError
   */
  public function __construct ($data)
  {
    $this->name = field($data, 'name', T\string());
    $this->description = field($data, 'description', T\string());
    $this->events = field($data, 'events', T\array_of(T\string()));
    $this->site_ids = field($data, 'site_ids', T\array_of(T\string()));
    $this->webhook_url = field($data, 'webhook_url', T\string());
  }
}
