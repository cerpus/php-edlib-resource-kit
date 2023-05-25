<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti13\Mapping;

enum ReaderType
{
    case Constant;
    case Getter;
    case Property;
}
