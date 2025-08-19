<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostArticleEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-article-post-article-post';

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/articles.{_format}';
}
