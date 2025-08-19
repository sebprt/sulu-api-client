<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostUserEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-security-post-user-post';

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/users.{_format}';
}
