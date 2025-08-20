<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutSnippetEndpoint extends AbstractEndpoint
{
    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/snippets/{id}.{_format}';
}
