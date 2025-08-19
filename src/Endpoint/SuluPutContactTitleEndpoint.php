<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluPutContactTitleEndpoint extends AbstractEndpoint
{
    public const OPERATION_ID = 'sulu-contact-put-contact-title-put';

    protected const METHOD = 'PUT';
    protected const PATH_TEMPLATE = '/admin/api/contact-titles/{id}.{_format}';
}
