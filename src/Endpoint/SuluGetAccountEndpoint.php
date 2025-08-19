<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetAccountEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-contact-get-account-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/accounts/{id}.{_format}';
}
