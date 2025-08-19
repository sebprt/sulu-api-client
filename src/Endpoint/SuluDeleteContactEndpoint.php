<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteContactEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-contact-delete-contact-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/contacts/{id}.{_format}';
}
