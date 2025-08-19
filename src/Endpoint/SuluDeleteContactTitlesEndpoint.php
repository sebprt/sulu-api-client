<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteContactTitlesEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-contact-delete-contact-titles-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/contact-titles.{_format}';
}
