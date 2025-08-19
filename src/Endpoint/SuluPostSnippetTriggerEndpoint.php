<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostSnippetTriggerEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-snippet-post-snippet-trigger-post';

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/snippets/{id}.{_format}';
}
