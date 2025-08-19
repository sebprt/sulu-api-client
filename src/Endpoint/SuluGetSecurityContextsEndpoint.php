<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetSecurityContextsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-security-get-security-contexts-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/security-contexts.{_format}';
}
