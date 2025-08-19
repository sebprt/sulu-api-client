<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetSnippetsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-snippet-get-snippets-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/snippets.{_format}';
}
