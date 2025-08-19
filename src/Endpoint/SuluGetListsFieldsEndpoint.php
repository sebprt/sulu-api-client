<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetListsFieldsEndpoint extends AbstractEndpoint
{

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/form/lists/fields.{_format}';
}
