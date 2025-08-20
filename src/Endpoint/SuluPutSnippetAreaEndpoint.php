<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutSnippetAreaEndpoint extends AbstractEndpoint
{
    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/snippet-areas/{key}.{_format}';
}
