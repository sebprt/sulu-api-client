<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostPageResourcelocatorGenerateEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-page-post-page-resourcelocator-generate-post';

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/pages/resourcelocators/generates.{_format}';
}
