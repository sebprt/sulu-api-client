<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPatchAccountEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-contact-patch-account-patch';

    protected const METHOD = 'PATCH';
    protected const PATH_TEMPLATE = '/admin/api/accounts/{id}.{_format}';
}
