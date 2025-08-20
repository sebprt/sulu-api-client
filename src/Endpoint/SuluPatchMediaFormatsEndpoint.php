<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPatchMediaFormatsEndpoint extends AbstractEndpoint
{
    protected const METHOD = 'PATCH';
    protected const PATH_TEMPLATE = '/admin/api/media/{id}/formats.{_format}';
}
