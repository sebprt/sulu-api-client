<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetListsFieldsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-form-get-lists-fields-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/form/lists/fields.{_format}';
}
