<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutSnippetAreaEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-snippet-put-snippet-area-put';

    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/snippet-areas/{key}.{_format}';
}
