<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPostContactPositionEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-contact-post-contact-position-post';

    protected const METHOD = 'POST';
    protected const PATH_TEMPLATE = '/admin/api/contact-positions.{_format}';
}
