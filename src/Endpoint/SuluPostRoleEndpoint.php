<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostRoleEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-security-post-role-post';

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/roles.{_format}';
}
