<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\exception;


/**
 * Class PreconditionFailedResponse
 *
 * 412 Precondition Failed (RFC 7232)
 * The server does not meet one of the preconditions that the requester put on
 * the request header fields.
 *
 * Not sure of all the cases that JW will respond with this. I've seen it happen
 * when trying to update the download_url of an existing asset but the asset's
 * status is "processing". I got this message:
 *
 * > Video must have status `ready`, `failed`, or `updating` before its original file can be updated. Current status is: processing.
 *
 * @package RightThisMinute\JWPlatform\Management\exception
 */
class PreconditionFailedResponse extends ErrorWithRateLimitResponse {}
