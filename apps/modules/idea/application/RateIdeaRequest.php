<?php

namespace Idy\Idea\Application;

use Idy\Idea\Domain\Model\IdeaId;

class RateIdeaRequest
{
    public $ideaId;
    public $raterName;
    public $raterRate;

    public function __construct(IdeaId $ideaId, $raterName, $raterRate)
    {
        $this->ideaId = $ideaId;
        $this->raterName = $raterName;
        $this->raterRate = $raterRate;
    }
}