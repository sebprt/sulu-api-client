<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteTagEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-tag-delete-tag-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/tags/{id}.{_format}';
}
