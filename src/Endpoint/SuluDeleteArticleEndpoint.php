<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteArticleEndpoint extends AbstractEndpoint
{
    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/articles/{id}.{_format}';
}
