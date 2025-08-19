<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetRoleSettingEndpoint extends AbstractEndpoint
{

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/roles/{roleId}/settings/{key}.{_format}';
}
