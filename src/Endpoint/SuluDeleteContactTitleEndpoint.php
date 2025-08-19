<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteContactTitleEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-contact-delete-contact-title-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/contact-titles/{id}.{_format}';
}
