<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostArticlePageEndpoint extends AbstractEndpoint
{

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/articles/{articleUuid}/pages.{_format}';
}
