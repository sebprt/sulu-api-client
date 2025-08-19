<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutRoleSettingEndpoint extends AbstractEndpoint
{

    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/roles/{roleId}/settings/{key}.{_format}';
}
