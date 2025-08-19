<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetRoleSettingEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-security-get-role-setting-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/roles/{roleId}/settings/{key}.{_format}';
}
