<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetFormatsEndpoint extends AbstractEndpoint
{
    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/formats.{_format}';
}
