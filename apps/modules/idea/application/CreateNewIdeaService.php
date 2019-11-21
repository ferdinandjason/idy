<?php

namespace Idy\Idea\Application;

use Idy\Idea\Domain\Model\Author;
use Idy\Idea\Domain\Model\Idea;
use Idy\Idea\Domain\Model\IdeaRepository;

class CreateNewIdeaService
{
    private $ideaRepository;

    public function __construct(
        IdeaRepository $ideaRepository)
    {
        $this->ideaRepository = $ideaRepository;
    }

    public function execute(CreateNewIdeaRequest $request)
    {
        $idea = Idea::makeIdea(
            $request->ideaTitle,
            $request->ideaDescription,
            new Author(
                $request->authorName,
                $request->authorEmail
            )
        );
        $this->ideaRepository->save($idea);

        return new CreateNewIdeaResponse($idea);
    }

}