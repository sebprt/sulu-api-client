<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteDynamicEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-form-delete-dynamic-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/form/dynamics/{id}.{_format}';
}
