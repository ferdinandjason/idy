<?php

namespace Idy\Idea\Application;

use Idy\Idea\Domain\Model\IdeaRepository;

class VoteIdeaService
{
    private $ideaRepository;

    public function __construct(
        IdeaRepository $ideaRepository)
    {
        $this->ideaRepository = $ideaRepository;
    }

    public function execute(VoteIdeaRequest $request)
    {
        $idea = $this->ideaRepository->byId($request->ideaId);

        $idea->vote();
        $this->ideaRepository->save($idea);

        return new VoteIdeaResponse($idea);
    }

}