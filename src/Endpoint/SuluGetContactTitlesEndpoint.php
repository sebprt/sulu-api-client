<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetContactTitlesEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-contact-get-contact-titles-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/contact-titles.{_format}';
}
