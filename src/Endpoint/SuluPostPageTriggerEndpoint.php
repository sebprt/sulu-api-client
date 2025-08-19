<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostPageTriggerEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-page-post-page-trigger-post';

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/pages/{id}.{_format}';
}
