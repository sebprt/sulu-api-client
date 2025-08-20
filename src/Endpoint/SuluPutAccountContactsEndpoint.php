<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutAccountContactsEndpoint extends AbstractEndpoint
{
    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/accounts/{accountId}/contacts/{contactId}.{_format}';
}
