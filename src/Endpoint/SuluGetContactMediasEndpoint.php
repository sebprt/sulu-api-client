<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetContactMediasEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-contact-get-contact-medias-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/contacts/{contactId}/medias.{_format}';
}
