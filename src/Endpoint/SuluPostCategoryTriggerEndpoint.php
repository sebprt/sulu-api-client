<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostCategoryTriggerEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-category-post-category-trigger-post';

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/categories/{id}.{_format}';
}
