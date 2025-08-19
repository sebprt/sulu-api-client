<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteContactPositionEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-contact-delete-contact-position-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/contact-positions/{id}.{_format}';
}
