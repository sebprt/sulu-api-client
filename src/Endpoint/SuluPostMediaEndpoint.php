<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostMediaEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-media-post-media-post';

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/media.{_format}';
}
