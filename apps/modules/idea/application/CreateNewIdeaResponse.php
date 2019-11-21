<?php

namespace Idy\Idea\Application;

use Idy\Idea\Domain\Model\Idea;

class CreateNewIdeaResponse
{
    public $data;
    public $message;

    public function __construct(Idea $idea, $message = 'Idea Successfully Created!')
    {
        $this->data = $idea;
        $this->message = $message;
    }

}