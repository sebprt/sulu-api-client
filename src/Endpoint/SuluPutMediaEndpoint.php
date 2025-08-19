<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutMediaEndpoint extends AbstractEndpoint
{

    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/media/{id}.{_format}';
}
