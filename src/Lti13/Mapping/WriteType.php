<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti13\Mapping;

enum WriteType
{
    case ConstructParam;
    case Setter;
    case Wither;
}
