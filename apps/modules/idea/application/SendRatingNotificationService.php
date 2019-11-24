<?php

namespace Idy\Idea\Application;

use Idy\Common\Events\DomainEventSubscriber;
use Idy\Idea\Domain\Model\IdeaRated;
use Phalcon\Di;

class SendRatingNotificationService implements DomainEventSubscriber
{
    public function handle($aDomainEvent)
    {
        $mailTemplateParams = [
            'name' => $aDomainEvent->getName(),
            'rating' => $aDomainEvent->getRating(),
            'title' => $aDomainEvent->getTitle(),
        ];

        $mailService = Di::getDefault()->get('mail');
        $message = $mailService->createMessageFromView('mail/idea_rated_plain', $mailTemplateParams)
            ->to($aDomainEvent->getEmail(), $aDomainEvent->getName())
            ->subject('Your Idea Received A New Rating');

        $message->send();
    }

    public function isSubscribedTo($aDomainEvent)
    {
        return $aDomainEvent instanceof IdeaRated;
    }
}