<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\v1\exception;


use Psr\Http\Message\ResponseInterface;

class ConflictResponse extends ErrorWithRateLimitResponse {}
