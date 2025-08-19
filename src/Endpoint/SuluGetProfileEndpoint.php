<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetProfileEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-security-get-profile-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/profile.{_format}';
}
