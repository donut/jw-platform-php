<?php
declare(strict_types=1);


namespace RightThisMinute\JWPlatform\common;


/**
* Ran into the max request length at least once. Contact JW support about it
* and after some digging they said it's probably 8k characters for the whole
* request. This seems a very safe limit, leaving more than enough characters
* for the request headers.
*/
const MAX_REQUEST_URL_LENGTH = 7000;
