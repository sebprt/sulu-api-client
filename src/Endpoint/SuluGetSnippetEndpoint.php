<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetSnippetEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-snippet-get-snippet-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/snippets/{id}.{_format}';
}
