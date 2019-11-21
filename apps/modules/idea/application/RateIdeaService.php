<?php

namespace Idy\Idea\Application;

use Idy\Common\Events\DomainEventPublisher;
use Idy\Common\Exception\DuplicateItemException;
use Idy\Common\Exception\NotValidResourceException;
use Idy\Idea\Domain\Model\IdeaRated;
use Idy\Idea\Domain\Model\IdeaRepository;
use Idy\Idea\Domain\Model\Rating;
use Idy\Idea\Domain\Model\RatingRepository;

class RateIdeaService
{
    private $ratingRepository;
    private $ideaRepository;

    public function __construct(
        RatingRepository $ratingRepository,
        IdeaRepository $ideaRepository
    )
    {
        $this->ratingRepository = $ratingRepository;
        $this->ideaRepository = $ideaRepository;
    }

    public function execute(RateIdeaRequest $request)
    {
        $rating = new Rating(
            $request->ideaId,
            $request->raterName,
            $request->raterRate
        );
        $ideaRatings = $this->ratingRepository->byIdeaId($request->ideaId);
        if ($rating->isValid()) {
            $exist = false;
            foreach ($ideaRatings as $existingRating) {
                if ($existingRating->equals($rating)) {
                    $exist = true;
                    break;
                }
            }

            if(!$exist) {
                $this->ratingRepository->save($rating);
            } else {
                throw new DuplicateItemException();
            }

            $idea = $this->ideaRepository->byId($request->ideaId);

            DomainEventPublisher::instance()->publish(
                new IdeaRated($idea->author()->name(), $idea->author()->email(),
                    $idea->title(), $rating->value())
            );

            return new RateIdeaResponse($rating);
        } else {
            throw new NotValidResourceException();
        }

    }
}