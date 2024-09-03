<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti\Lti11\Serializer\DeepLinking;

use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\LineItem;

interface LineItemSerializerInterface
{
    /**
     * @return array<mixed>
     *     The compact form JSON-LD representation of LineItem
     */
    public function serialize(LineItem $lineItem): array;
}
