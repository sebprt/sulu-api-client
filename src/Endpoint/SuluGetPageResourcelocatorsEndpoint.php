<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetPageResourcelocatorsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-page-get-page-resourcelocators-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/pages/{id}/resourcelocators.{_format}';
}
