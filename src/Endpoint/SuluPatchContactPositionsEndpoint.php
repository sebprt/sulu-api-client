<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPatchContactPositionsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-contact-patch-contact-positions-patch';

    protected const METHOD = 'PATCH';
    protected const PATH_TEMPLATE = '/admin/api/contact-positions.{_format}';
}
