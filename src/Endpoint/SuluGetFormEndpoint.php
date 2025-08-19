<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetFormEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-form-get-form-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/forms/{id}.{_format}';
}
