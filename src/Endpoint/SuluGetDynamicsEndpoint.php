<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetDynamicsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-form-get-dynamics-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/form/dynamics.{_format}';
}
