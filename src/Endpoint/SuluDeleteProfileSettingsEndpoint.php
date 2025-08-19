<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteProfileSettingsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-security-delete-profile-settings-delete';

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/profile/settings.{_format}';
}
