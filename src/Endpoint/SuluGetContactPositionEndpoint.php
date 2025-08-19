<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetContactPositionEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-contact-get-contact-position-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/contact-positions/{id}.{_format}';
}
