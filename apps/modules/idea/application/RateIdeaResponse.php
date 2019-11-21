<?php

namespace Idy\Idea\Application;

class RateIdeaResponse
{
    public $data;
    public $message;

    public function __construct($rate, $message = 'Successfully Rated an Idea!')
    {
        $this->data = $rate;
        $this->message = $message;
    }
}