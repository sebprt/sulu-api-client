<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostArticleVersionTriggerEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-article-post-article-version-trigger-post';

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/articles/{id}/versions/{version}.{_format}';
}
