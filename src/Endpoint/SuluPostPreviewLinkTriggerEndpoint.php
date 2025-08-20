<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostPreviewLinkTriggerEndpoint extends AbstractEndpoint
{
    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/preview-links/{resourceId}/triggers.{_format}';
}
