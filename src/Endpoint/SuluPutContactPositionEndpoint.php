<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutContactPositionEndpoint extends AbstractEndpoint
{
    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/contact-positions/{id}.{_format}';
}
