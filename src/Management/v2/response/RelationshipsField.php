<?php


namespace RightThisMinute\JWPlatform\Management\v2\response;


use RightThisMinute\JWPlatform\common\DecoderTrait;
use RightThisMinute\StructureDecoder\exceptions\DecodeError;
use function RightThisMinute\StructureDecoder\field;
use RightThisMinute\StructureDecoder\types as T;
use function RightThisMinute\StructureDecoder\optional_field;


class RelationshipsField
{
  use DecoderTrait;


  /**
   * @var ProtectionRuleField|null
   */
  public $protection_rule;

  /**
   * RelationshipsField constructor.
   *
   * @param object $data = ['protection_rule => ['id' => (string)]]
   *
   * @throws DecodeError
   */
  public function __construct (object $data)
  {
    $this->protection_rule = optional_field
      ($data, 'protection_rule', ProtectionRuleField::decoder());
  }
}


class ProtectionRuleField
{
  use DecoderTrait;

  /**
   * Unique identifier for the protection rule
   *
   * @var string
   */
  public $id;

  /**
   * ProtectionRuleField constructor.
   *
   * @param object $data = ['id' => (string)]
   *
   * @throws DecodeError
   */
  public function __construct (object $data)
  {
    $this->id = field($data, 'id', T\string());
  }
}
