<?php


namespace RightThisMinute\JWPlatform\Management\v2\response;


use RightThisMinute\StructureDecoder\exceptions\DecodeError;
use function RightThisMinute\StructureDecoder\field;
use RightThisMinute\StructureDecoder\types as T;


/**
 * Class WebhooksList
 *
 * @see https://developer.jwplayer.com/jwplayer/reference#section-get-v2-webhooks-response-parameters
 *
 * @package RightThisMinute\JWPlatform\Management\v2\response
 */
class WebhooksList extends Success
{
  /**
   * @var WebhooksListBody
   */
  public $body;

  /**
   * WebhooksList constructor.
   *
   * @param SuccessWithBody $response
   *
   * @throws DecodeError
   */
  public function __construct (SuccessWithBody $response)
  {
    parent::__construct($response->headers);
    $this->body = new WebhooksListBody($response->body);
  }
}


class WebhooksListBody
{
  use PagedResultTrait;

  /**
   * Resource for managing the individual webhooks of an account
   *
   * @var Webhook[]
   */
  public $webhooks;

  /**
   * WebhooksListBody constructor.
   *
   * @param $data
   *
   * @throws DecodeError
   */
  public function __construct ($data)
  {
    $this->constructPagedResultTrait($data);
    $this->webhooks = field
      ($data, 'webhooks', T\array_of(Webhook::decoder()));
  }
}
