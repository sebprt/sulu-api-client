<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutContactTitleEndpoint extends AbstractEndpoint
{
    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/contact-titles/{id}.{_format}';
}
