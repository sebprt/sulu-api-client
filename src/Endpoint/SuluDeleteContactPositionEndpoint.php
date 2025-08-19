<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluDeleteContactPositionEndpoint extends AbstractEndpoint
{

    protected const METHOD = 'DELETE';
    protected const PATH_TEMPLATE = '/admin/api/contact-positions/{id}.{_format}';
}
