<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeletePageResourcelocatorsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-page-delete-page-resourcelocators-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/pages/{id}/resourcelocators.{_format}';
}
