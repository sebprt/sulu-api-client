<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteFormEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-form-delete-form-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/forms/{id}.{_format}';
}
