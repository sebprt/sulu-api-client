<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteSnippetEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-snippet-delete-snippet-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/snippets/{id}.{_format}';
}
