<?php

namespace Idy\Idea\Application;

use Idy\Idea\Domain\Model\IdeaId;

class VoteIdeaRequest
{
    public $ideaId;

    public function __construct(IdeaId $ideaId)
    {
        $this->ideaId = $ideaId;
    }
}