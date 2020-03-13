<?php


namespace RightThisMinute\JWPlatform\exception;


use const RightThisMinute\JWPlatform\common\MAX_REQUEST_URL_LENGTH;

/**
 * Class URLTooLong
 *
 * Thrown when the URL is longer than the set safe max URL length.
 *
 * @package RightThisMinute\JWPlatform\exception
 */
class URLTooLong extends \Exception
{
  /**
   * @var int
   * The number of characters over the max length.
   */
  private $over_count;

  /**
   * @return int
   * The number of characters over the max length.
   */
  public function getOverCount(): int { return $this->over_count; }

  public function __construct(int $url_length)
  {
    $this->over_count = $url_length - MAX_REQUEST_URL_LENGTH;

    $message = 'The URL exceeds the maximum safe length of '
      . number_format(MAX_REQUEST_URL_LENGTH) . ' by '
      . number_format($this->over_count) . ' characters.';

    parent::__construct($message);
  }
}
