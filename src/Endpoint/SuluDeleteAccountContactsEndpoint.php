<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteAccountContactsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-contact-delete-account-contacts-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/accounts/{accountId}/contacts/{id}.{_format}';
}
