<?php

namespace RightThisMinute\JWPlatform\Management\v2\response;

use RightThisMinute\StructureDecoder\exceptions\DecodeError;
use function RightThisMinute\StructureDecoder\field;
use RightThisMinute\StructureDecoder\types as T;

trait PagedResultTrait
{
  /**
   * Offset for returned resources. Starts at 1.
   *
   * @var int
   */
  public $page;

  /**
   * Maximum number of resources to return
   *
   * @var int
   */
  public $page_length;

  /**
   * Number of resources available before pagination
   *
   * @var int
   */
  public $total;
  
  /**
   * @param $data
   * @throws DecodeError
   */
  private function constructPagedResultTrait ($data) : void
  {
    $this->page = field($data, 'page', T\int());
    $this->page_length = field($data, 'page_length', T\int());
    $this->total = field($data, 'total', T\int());
  }
}
