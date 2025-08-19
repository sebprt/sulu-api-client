<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteCollaborationsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-admin-delete-collaborations-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/collaborations.{_format}';
}
