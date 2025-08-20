<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutFormEndpoint extends AbstractEndpoint
{
    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/forms/{id}.{_format}';
}
