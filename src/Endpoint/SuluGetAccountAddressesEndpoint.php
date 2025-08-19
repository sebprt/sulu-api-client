<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Endpoint;

final class SuluGetAccountAddressesEndpoint extends AbstractEndpoint
{

    protected const METHOD = 'GET';
    protected const PATH_TEMPLATE = '/admin/api/accounts/{id}/addresses.{_format}';
}
