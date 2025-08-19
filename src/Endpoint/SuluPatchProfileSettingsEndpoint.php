<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPatchProfileSettingsEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-security-patch-profile-settings-patch';

    protected const METHOD = 'PATCH';
    protected const PATH_TEMPLATE = '/admin/api/profile/settings.{_format}';
}
