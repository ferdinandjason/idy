<?php

namespace Idy\Idea\Domain\Model;

class Idea
{
    private $id;
    private $title;
    private $description;
    private $author;
    private $ratings;
    private $averageRating;
    private $votes;
    
    public function __construct(IdeaId $id, $title, $description, Author $author, $votes = 0, $averageRating = 0)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->author = $author;
        $this->ratings = array();
        $this->averageRating = $averageRating;
        $this->votes = $votes;
    }

    public function id() 
    {
        return $this->id;
    }

    public function title()
    {
        return $this->title;
    }

    public function description()
    {
        return $this->description;
    }

    public function author()
    {
        return $this->author;
    }

    public function votes()
    {
        return $this->votes;
    }

    public function addRating($user, $ratingValue)
    {
        $newRating = new Rating($user, $ratingValue);

        if ($newRating->isValid()) {
            $exist = false;
            foreach ($this->ratings as $existingRating) {
                if ($existingRating->equals($newRating)) {
                    $exist = true;
                }
            }

            if (!$exist) {
                array_push($this->ratings, $newRating);
            } else {
                throw new Exception('Author ' . $newRating->author() . ' has given a rating.');
            }

            DomainEventPublisher::instance()->publish(
                new IdeaRated($this->author->name(), $this->author->email(), 
                    $this->title, $ratingValue)
            );

        }
    }

    public function vote()
    {   
        $this->votes = $this->votes + 1;
    }

    public function averageRating()
    {
        return $this->averageRating;
    }

    public static function makeIdea($title, $description, $author)
    {
        $newIdea = new Idea(new IdeaId(), $title, $description, $author);
        
        return $newIdea;
    }

}