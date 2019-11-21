<?php

namespace Idy\Idea\Application;

use Idy\Idea\Domain\Model\Idea;

class VoteIdeaResponse
{
    public $data;
    public $message;

    public function __construct(Idea $idea, $message = 'Vote Successful!')
    {
        $this->data = $idea;
        $this->message = $message;
    }
}