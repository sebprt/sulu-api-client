<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostMediaPreviewEndpoint extends AbstractEndpoint
{

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/media/{id}/preview.{_format}';
}
