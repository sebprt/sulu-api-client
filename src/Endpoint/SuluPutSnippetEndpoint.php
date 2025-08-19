<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutSnippetEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-snippet-put-snippet-put';

    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/snippets/{id}.{_format}';
}
