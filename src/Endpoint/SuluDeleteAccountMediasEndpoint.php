<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteAccountMediasEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-contact-delete-account-medias-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/accounts/{contactId}/medias/{id}.{_format}';
}
