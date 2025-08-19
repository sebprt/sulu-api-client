<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteAccountContactsEndpoint extends AbstractEndpoint
{

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/accounts/{accountId}/contacts/{id}.{_format}';
}
