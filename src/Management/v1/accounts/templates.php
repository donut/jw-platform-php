<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\v1\accounts\templates;


use RightThisMinute\JWPlatform\exception\InvalidResponseJSON;
use RightThisMinute\JWPlatform\exception\URLTooLong;
use RightThisMinute\JWPlatform\Management\v1\Client;
use RightThisMinute\JWPlatform\Management\v1\exception\BadRequestResponse;
use RightThisMinute\JWPlatform\Management\v1\exception\ConflictResponse;
use RightThisMinute\JWPlatform\Management\v1\exception\MethodNotAllowedResponse;
use RightThisMinute\JWPlatform\Management\v1\exception\NotFoundResponse;
use RightThisMinute\JWPlatform\Management\v1\exception\PreconditionFailedResponse;
use RightThisMinute\JWPlatform\Management\v1\exception\TooManyRequestsResponse;
use RightThisMinute\JWPlatform\Management\v1\exception\UnknownErrorResponse;
use RightThisMinute\JWPlatform\Management\v1\response\AccountsTemplatesListBody;
use RightThisMinute\StructureDecoder\exceptions\DecodeError;


/**
 * Returns a list of conversion templates available to this account.
 *
 * This can be used to find the template key needed to add a conversion to a
 * video.
 *
 * @param Client $client
 *
 * @return AccountsTemplatesListBody
 * @throws BadRequestResponse
 * @throws ConflictResponse
 * @throws InvalidResponseJSON
 * @throws MethodNotAllowedResponse
 * @throws NotFoundResponse
 * @throws TooManyRequestsResponse
 * @throws UnknownErrorResponse
 * @throws URLTooLong
 * @throws DecodeError
 * @throws PreconditionFailedResponse
 */
function list_ (Client $client) : AccountsTemplatesListBody
{
  $endpoint = '/accounts/templates/list';

  $response_body = $client->get($endpoint);

  return new AccountsTemplatesListBody($response_body->json);
}
