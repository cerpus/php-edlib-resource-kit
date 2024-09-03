<?php

namespace Cerpus\EdlibResourceKit\Lti\Lti11\Mapper\DeepLinking;

use Cerpus\EdlibResourceKit\Lti\Lti11\Context\DeepLinkingProps as Prop;
use Cerpus\EdlibResourceKit\Lti\Message\DeepLinking\LineItem;

final readonly class LineItemMapper implements LineItemMapperInterface
{
    public function __construct(
        private ScoreConstraintsMapperInterface $constraintsMapper = new ScoreConstraintsMapper(),
    ) {
    }

    public function map(array $data): LineItem|null
    {
        $scoreConstraints = $this->constraintsMapper->map($data[Prop::SCORE_CONSTRAINTS] ?? []);

        return new LineItem(
            $scoreConstraints
        );
    }
}
