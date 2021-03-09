<?php


namespace RightThisMinute\JWPlatform\Management\v2\response;


use RightThisMinute\JWPlatform\common\DecoderTrait;
use RightThisMinute\StructureDecoder\exceptions\DecodeError;
use function RightThisMinute\StructureDecoder\field;
use RightThisMinute\StructureDecoder\types as T;


class WebhooksCreate extends Success
{
  /**
   * @var WebhooksCreateBody
   */
  public $body;

  /**
   * WebhooksCreate constructor.
   *
   * @param SuccessWithBody $response
   *
   * @throws DecodeError
   */
  public function __construct (SuccessWithBody $response)
  {
    parent::__construct($response->headers);
    $this->body = new WebhooksCreateBody($response->body);
  }
}


/**
 * Class WebhooksCreateBody
 *
 * @see https://developer.jwplayer.com/jwplayer/reference#section-post-v2-webhooks-response-parameters
 *
 * @package RightThisMinute\JWPlatform\Management\v2\response
 */
class WebhooksCreateBody extends Webhook
{
  use DecoderTrait;

  /**
   * Shared secret used for verifying authenticity of webhook
   *
   * @var string
   */
  public $secret;


  public function __construct ($data)
  {
    parent::__construct($data);
    $this->secret = field($data, 'secret', T\string());
  }
}
