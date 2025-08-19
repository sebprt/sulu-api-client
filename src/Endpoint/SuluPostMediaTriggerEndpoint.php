<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostMediaTriggerEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-media-post-media-trigger-post';

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/media/{id}.{_format}';
}
