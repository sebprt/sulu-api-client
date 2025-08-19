<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostTagEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-tag-post-tag-post';

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/tags.{_format}';
}
