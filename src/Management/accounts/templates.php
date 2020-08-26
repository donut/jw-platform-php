<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\accounts\templates;


use RightThisMinute\JWPlatform\exception\InvalidResponseJSON;
use RightThisMinute\JWPlatform\exception\URLTooLong;
use RightThisMinute\JWPlatform\Management\Client;
use RightThisMinute\JWPlatform\Management\exception\BadRequestResponse;
use RightThisMinute\JWPlatform\Management\exception\ConflictResponse;
use RightThisMinute\JWPlatform\Management\exception\MethodNotAllowedResponse;
use RightThisMinute\JWPlatform\Management\exception\NotFoundResponse;
use RightThisMinute\JWPlatform\Management\exception\TooManyRequestsResponse;
use RightThisMinute\JWPlatform\Management\exception\UnknownErrorResponse;
use RightThisMinute\JWPlatform\Management\response\AccountsTemplatesListBody;
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
 */
function list_ (Client $client) : AccountsTemplatesListBody
{
  $endpoint = '/accounts/templates/list';

  $response_body = $client->get($endpoint);

  return new AccountsTemplatesListBody($response_body->json);
}
