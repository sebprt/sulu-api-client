<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostCollectionTriggerEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-media-post-collection-trigger-post';

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/collections/{id}.{_format}';
}
