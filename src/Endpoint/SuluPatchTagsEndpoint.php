<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPatchTagsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-tag-patch-tags-patch';

    protected const METHOD = 'PATCH';
    protected const PATH_TEMPLATE = '/admin/api/tags.{_format}';
}
