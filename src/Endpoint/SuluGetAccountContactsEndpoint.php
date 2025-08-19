<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetAccountContactsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-contact-get-account-contacts-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/accounts/{id}/contacts.{_format}';
}
