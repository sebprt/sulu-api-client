<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutProfileEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-security-put-profile-put';

    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/profile.{_format}';
}
