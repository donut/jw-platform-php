<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\accounts\templates;


use RightThisMinute\JWPlatform\Management\Client;
use RightThisMinute\JWPlatform\Management\response\AccountsTemplatesListBody;


/**
 * Returns a list of conversion templates available to this account.
 *
 * This can be used to find the template key needed to add a conversion to a
 * video.
 *
 * @param \RightThisMinute\JWPlatform\Management\Client $client
 *
 * @return \RightThisMinute\JWPlatform\Management\response\AccountsTemplatesListBody
 * @throws \RightThisMinute\JWPlatform\Management\exception\BadRequestResponse
 * @throws \RightThisMinute\JWPlatform\Management\exception\ConflictResponse
 * @throws \RightThisMinute\JWPlatform\exception\InvalidResponseJSON
 * @throws \RightThisMinute\JWPlatform\Management\exception\MethodNotAllowedResponse
 * @throws \RightThisMinute\JWPlatform\Management\exception\NotFoundResponse
 * @throws \RightThisMinute\JWPlatform\Management\exception\TooManyRequestsResponse
 * @throws \RightThisMinute\JWPlatform\Management\exception\UnknownErrorResponse
 * @throws \RightThisMinute\JWPlatform\exception\URLTooLong
 * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
 */
function list_ (Client $client) : AccountsTemplatesListBody
{
  $endpoint = '/accounts/templates/list';

  $response_body = $client->get($endpoint);

  return new AccountsTemplatesListBody($response_body->json);
}
