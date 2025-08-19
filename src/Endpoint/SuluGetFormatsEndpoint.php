<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetFormatsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-media-get-formats-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/formats.{_format}';
}
