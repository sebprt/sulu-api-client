<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteSnippetAreaEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-snippet-delete-snippet-area-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/snippet-areas/{key}.{_format}';
}
