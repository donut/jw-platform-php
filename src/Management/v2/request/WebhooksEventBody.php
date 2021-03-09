<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\v2\request;


use RightThisMinute\StructureDecoder\exceptions\DecodeError;
use function RightThisMinute\StructureDecoder\field;
use RightThisMinute\StructureDecoder\types as T;


class WebhooksEventBody
{
  /**
   * The event type.
   *
   * @var string
   */
  public $event;

  /**
   * The ID of the media the event relates to.
   *
   * @var string
   */
  public $media_id;

  /**
   * The ID of the webhook that generated this event.
   *
   * @var string
   */
  public $webhook_id;

  /**
   * The ID of the site (aka property) where the media lives.
   *
   * @var string
   */
  public $site_id;

  /**
   * When the event happened.
   *
   * @var string
   */
  public $event_time;

  /**
   * WebhooksPublishBody constructor.
   *
   * @param object|array $data
   *   The parsed JSON request body as an object or associative array.
   *
   * @throws DecodeError
   */
  public function __construct ($data)
  {
    $this->event = field($data, 'event', T\string());
    $this->media_id = field($data, 'media_id', T\string());
    $this->webhook_id = field($data, 'webhook_id', T\string());
    $this->site_id = field($data, 'site_id', T\string());
    $this->event_time = field($data, 'event_time', T\string());
  }
}
