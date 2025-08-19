<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutTagEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-tag-put-tag-put';

    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/tags/{id}.{_format}';
}
