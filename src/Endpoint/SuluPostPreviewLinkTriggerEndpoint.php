<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostPreviewLinkTriggerEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-preview-post-preview-link-trigger-post';

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/preview-links/{resourceId}/triggers.{_format}';
}
