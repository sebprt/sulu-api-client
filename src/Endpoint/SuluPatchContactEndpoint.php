<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPatchContactEndpoint extends AbstractEndpoint
{

    protected const METHOD = 'PATCH';
    protected const PATH_TEMPLATE = '/admin/api/contacts/{id}.{_format}';
}
