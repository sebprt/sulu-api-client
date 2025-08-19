<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetContactEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-contact-get-contact-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/contacts/{id}.{_format}';
}
