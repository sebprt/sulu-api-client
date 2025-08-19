<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetContactPositionsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-contact-get-contact-positions-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/contact-positions.{_format}';
}
