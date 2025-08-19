<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteCollectionEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-media-delete-collection-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/collections/{id}.{_format}';
}
