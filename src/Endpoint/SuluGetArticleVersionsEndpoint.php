<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetArticleVersionsEndpoint extends AbstractEndpoint
{

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/articles/{id}/versions.{_format}';
}
