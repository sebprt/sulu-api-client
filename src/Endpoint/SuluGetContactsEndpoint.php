<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetContactsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-contact-get-contacts-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/contacts.{_format}';
}
