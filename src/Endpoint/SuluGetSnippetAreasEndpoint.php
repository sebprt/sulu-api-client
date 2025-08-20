<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetSnippetAreasEndpoint extends AbstractEndpoint
{
    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/snippet-areas.{_format}';
}
