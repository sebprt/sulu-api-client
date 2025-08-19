<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteContactMediasEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-contact-delete-contact-medias-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/contacts/{contactId}/medias/{id}.{_format}';
}
