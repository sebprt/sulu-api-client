<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostCollectionTriggerEndpoint extends AbstractEndpoint
{
    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/collections/{id}.{_format}';
}
