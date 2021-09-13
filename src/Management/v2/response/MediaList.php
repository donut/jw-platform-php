<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\v2\response;


use RightThisMinute\JWPlatform\common\DecoderTrait;
use RightThisMinute\StructureDecoder\exceptions\DecodeError;
use function RightThisMinute\StructureDecoder\field;
use function RightThisMinute\StructureDecoder\types\array_of;

class MediaList extends Success
{
  /**
   * @var MediaListBody
   */
  public $body;
  
  /**
   * MediaList response constructor
   *
   * @param SuccessWithBody $response
   * @throws DecodeError
   */
  public function __construct (SuccessWithBody $response)
  {
    parent::__construct($response->headers);
    $this->body = MediaListBody::decoder()($response->body);
  }
}


class MediaListBody
{
  use DecoderTrait;
  use PagedResultTrait;
  
  /**
   * @var Media
   */
  public $media;
  
  
  /**
   * MediaListBody constructor
   *
   * @param $data
   * @throws DecodeError
   */
  public function __construct ($data)
  {
    $this->constructPagedResultTrait($data);
    $this->media = field($data, 'media', array_of(Media::decoder()));
  }
}
