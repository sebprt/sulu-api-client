<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutContactPositionEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-contact-put-contact-position-put';

    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/contact-positions/{id}.{_format}';
}
