<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteTrashItemEndpoint extends AbstractEndpoint
{

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/trash-items/{id}.{_format}';
}
