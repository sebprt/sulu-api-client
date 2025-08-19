<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetCollectionEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-media-get-collection-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/collections/{id}.{_format}';
}
