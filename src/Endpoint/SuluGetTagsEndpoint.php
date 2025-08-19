<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetTagsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-tag-get-tags-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/tags.{_format}';
}
