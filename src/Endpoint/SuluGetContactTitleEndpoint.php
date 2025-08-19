<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetContactTitleEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-contact-get-contact-title-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/contact-titles/{id}.{_format}';
}
