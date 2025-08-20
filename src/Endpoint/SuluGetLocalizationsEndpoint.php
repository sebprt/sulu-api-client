<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetLocalizationsEndpoint extends AbstractEndpoint
{
    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/localizations.{_format}';
}
