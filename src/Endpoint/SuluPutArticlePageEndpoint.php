<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutArticlePageEndpoint extends AbstractEndpoint
{

    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/articles/{articleUuid}/pages/{uuid}.{_format}';
}
