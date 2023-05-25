<?php

declare(strict_types=1);

namespace Cerpus\EdlibResourceKit\Lti13;

enum DocumentTarget: string
{
    case Frame = 'frame';
    case Iframe = 'iframe';
    case Window = 'window';
}
