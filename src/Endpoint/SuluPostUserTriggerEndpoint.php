<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostUserTriggerEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-security-post-user-trigger-post';

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/users/{id}.{_format}';
}
