<?php

namespace Idy\Idea\Application;

use Idy\Idea\Domain\Model\IdeaRepository;

class ViewAllIdeasService
{
    private $ideaRepository;

    public function __construct(
        IdeaRepository $ideaRepository)
    {
        $this->ideaRepository = $ideaRepository;
    }

    public function execute()
    {
        $ideas = $this->ideaRepository->allIdeas();
        return new ViewAllIdeasResponse($ideas);
    }
}