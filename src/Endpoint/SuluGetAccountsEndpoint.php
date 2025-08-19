<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetAccountsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-contact-get-accounts-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/accounts.{_format}';
}
