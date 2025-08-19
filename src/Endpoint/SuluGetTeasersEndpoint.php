<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetTeasersEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-page-get-teasers-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/teasers.{_format}';
}
