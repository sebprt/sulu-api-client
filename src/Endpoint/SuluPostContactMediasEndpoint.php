<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostContactMediasEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-contact-post-contact-medias-post';

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/contacts/{contactId}/medias.{_format}';
}
