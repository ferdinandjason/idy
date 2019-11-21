<?php

namespace Idy\Idea\Application;

class ViewAllIdeasResponse
{
    public $data;

    public function __construct($ideas)
    {
        $this->data = $ideas;
    }
}