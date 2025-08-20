<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostAccountMediasEndpoint extends AbstractEndpoint
{
    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/accounts/{contactId}/medias.{_format}';
}
