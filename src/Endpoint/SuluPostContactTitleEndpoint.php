<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostContactTitleEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-contact-post-contact-title-post';

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/contact-titles.{_format}';
}
