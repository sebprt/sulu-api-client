<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostSnippetEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-snippet-post-snippet-post';

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/snippets.{_format}';
}
