<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetReferencesEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-reference-get-references-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/references.{_format}';
}
