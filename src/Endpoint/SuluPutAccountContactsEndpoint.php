<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutAccountContactsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-contact-put-account-contacts-put';

    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/accounts/{accountId}/contacts/{contactId}.{_format}';
}
