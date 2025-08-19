<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetFormEndpoint extends AbstractEndpoint
{

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/forms/{id}.{_format}';
}
