<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetPagesEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-page-get-pages-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/pages.{_format}';
}
