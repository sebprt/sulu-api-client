<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteMediaPreviewEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-media-delete-media-preview-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/media/{id}/preview.{_format}';
}
