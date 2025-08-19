<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetListsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-form-get-lists-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/form/lists.{_format}';
}
