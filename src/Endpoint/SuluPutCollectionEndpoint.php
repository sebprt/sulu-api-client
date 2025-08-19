<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutCollectionEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-media-put-collection-put';

    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/collections/{id}.{_format}';
}
