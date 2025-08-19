<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetAccountMediasEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-contact-get-account-medias-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/accounts/{contactId}/medias.{_format}';
}
