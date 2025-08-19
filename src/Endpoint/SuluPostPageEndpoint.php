<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostPageEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-page-post-page-post';

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/pages.{_format}';
}
