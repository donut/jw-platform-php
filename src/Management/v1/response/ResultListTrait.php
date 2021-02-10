<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\v1\response;


use function RightThisMinute\StructureDecoder\field;
use RightThisMinute\StructureDecoder\types as T;


trait ResultListTrait
{
  /** @var int */
  public $limit;

  /** @var int */
  public $offset;

  /** @var int */
  public $total;


  /**
   * @param object|array $data =
   *  [ 'limit' => 50
   *  , 'offset' => 50
   *  , 'total' => 147 ]
   *
   * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
   */
  private function constructResultLimitTrait ($data)
  {
    $this->limit = field($data, 'limit', T\int());
    $this->offset = field($data, 'offset', T\int());
    $this->total = field($data, 'total', T\int());
  }
}
