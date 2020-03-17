<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\Management\accounts\templates;


use RightThisMinute\JWPlatform\exception\UnexpectedResponseBody;
use RightThisMinute\JWPlatform\Management\Client;
use RightThisMinute\JWPlatform\Management\response\AccountsTemplatesListBody;
use RightThisMinute\JWPlatform\Management\response\SuccessJSONBody;


/**
 * @param \RightThisMinute\JWPlatform\Management\Client $client
 *
 * @return \RightThisMinute\JWPlatform\Management\response\AccountsTemplatesListBody
 * @throws \RightThisMinute\JWPlatform\exception\URLTooLong
 * @throws \RightThisMinute\JWPlatform\exception\UnexpectedResponse
 * @throws \RightThisMinute\JWPlatform\exception\UnexpectedResponseBody
 * @throws \RightThisMinute\StructureDecoder\exceptions\DecodeError
 */
function list_ (Client $client) : AccountsTemplatesListBody
{
  $endpoint = '/accounts/templates/list';

  $response_body = $client->get($endpoint);

  if (!($response_body instanceof SuccessJSONBody))
    throw new UnexpectedResponseBody($endpoint, $response_body);

  return new AccountsTemplatesListBody($response_body->json);
}
