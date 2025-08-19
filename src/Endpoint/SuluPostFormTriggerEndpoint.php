<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostFormTriggerEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-form-post-form-trigger-post';

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/forms/{id}.{_format}';
}
