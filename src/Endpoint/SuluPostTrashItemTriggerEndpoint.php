<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostTrashItemTriggerEndpoint extends AbstractEndpoint
{

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/trash-items/{id}.{_format}';
}
