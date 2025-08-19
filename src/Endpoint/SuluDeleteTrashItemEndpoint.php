<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteTrashItemEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-trash-delete-trash-item-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/trash-items/{id}.{_format}';
}
