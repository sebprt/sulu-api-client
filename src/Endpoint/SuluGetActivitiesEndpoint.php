<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetActivitiesEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-activity-get-activities-get';

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/activities.{_format}';
}
