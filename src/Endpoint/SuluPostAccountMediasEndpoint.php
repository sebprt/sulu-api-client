<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostAccountMediasEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-contact-post-account-medias-post';

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/accounts/{contactId}/medias.{_format}';
}
