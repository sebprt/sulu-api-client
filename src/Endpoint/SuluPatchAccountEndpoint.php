<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPatchAccountEndpoint extends AbstractEndpoint
{

    protected const METHOD = 'PATCH';
    protected const PATH_TEMPLATE = '/admin/api/accounts/{id}.{_format}';
}
