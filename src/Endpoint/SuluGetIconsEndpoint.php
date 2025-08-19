<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetIconsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-page-get-icons-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/icons.{_format}';
}
