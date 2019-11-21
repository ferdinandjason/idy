<?php

namespace Idy\Idea\Infrastructure;

use Idy\Idea\Domain\Model\IdeaId;
use Idy\Idea\Domain\Model\Rating;
use Idy\Idea\Domain\Model\RatingRepository;
use Phalcon\Db\Column;

class SqlRatingRepository implements RatingRepository
{
    private $connection;
    private $statement;
    private $statementTypes;

    const INDEX_IDEA_ID = 0, INDEX_USER = 1, INDEX_VALUE = 2;

    public function __construct($di)
    {
        $this->connection = $di->get('db');
        $this->statement = [
            'create' => $this->connection->prepare(
                "INSERT INTO rating (`idea_id`, `user`, `value`) VALUES (:ideaId, :user, :value)"
            ),
            'find_by_idea' => $this->connection->prepare(
                "SELECT idea_id, `user`, `value` FROM rating WHERE idea_id = :ideaId"
            )
        ];

        $this->statementTypes = [
            'create' => [
                'ideaId' => Column::BIND_PARAM_STR,
                'user' => Column::BIND_PARAM_STR,
                'value' => Column::BIND_PARAM_STR,
            ],
            'find_by_idea' => [
                'ideaId' => Column::BIND_PARAM_STR
            ],
        ];
    }

    public function save(Rating $rating)
    {
        $ratingData = [
            'ideaId' => $rating->ideaId()->id(),
            'user' => $rating->user(),
            'value' => $rating->value(),
        ];

        $success = $this->connection->executePrepared(
            $this->statement['create'],
            $ratingData,
            $this->statementTypes['create']
        );
    }

    public function byIdeaId(IdeaId $ideaId)
    {
        $ideaIdData = [
            'ideaId' => $ideaId->id(),
        ];

        $result = $this->connection->executePrepared(
            $this->statement['find_by_idea'],
            $ideaIdData,
            $this->statementTypes['find_by_idea']
        );

        $ratings = array();
        foreach ($result as $item) {
            array_push($ratings, new Rating(
                    new IdeaId(
                        $item[self::INDEX_IDEA_ID]
                    ),
                    $item[self::INDEX_USER],
                    $item[self::INDEX_VALUE]
                )
            );
        }

        return $ratings;
    }
}
