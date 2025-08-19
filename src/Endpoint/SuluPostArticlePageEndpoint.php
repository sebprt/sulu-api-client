<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostArticlePageEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-article-post-article-page-post';

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/articles/{articleUuid}/pages.{_format}';
}
