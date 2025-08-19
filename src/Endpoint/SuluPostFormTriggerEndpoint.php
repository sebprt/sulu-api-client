<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostFormTriggerEndpoint extends AbstractEndpoint
{

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/forms/{id}.{_format}';
}
