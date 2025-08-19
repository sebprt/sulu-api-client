<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetGroupsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-security-get-groups-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/groups.{_format}';
}
