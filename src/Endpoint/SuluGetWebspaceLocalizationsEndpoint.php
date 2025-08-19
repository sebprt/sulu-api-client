<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetWebspaceLocalizationsEndpoint extends AbstractEndpoint
{

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/webspace/localizations.{_format}';
}
