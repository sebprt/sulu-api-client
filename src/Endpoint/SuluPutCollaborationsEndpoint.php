<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutCollaborationsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-admin-put-collaborations-put';

    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/collaborations.{_format}';
}
