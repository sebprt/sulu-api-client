<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetTrashItemEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-trash-get-trash-item-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/trash-items/{id}.{_format}';
}
