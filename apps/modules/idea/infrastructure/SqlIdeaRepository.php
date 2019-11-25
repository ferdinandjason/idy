<?php 

namespace Idy\Idea\Infrastructure;

use Idy\Common\Exception\ResourceNotFoundException;
use Idy\Idea\Domain\Model\Author;
use Idy\Idea\Domain\Model\Idea;
use Idy\Idea\Domain\Model\IdeaId;
use Idy\Idea\Domain\Model\IdeaRepository;
use Phalcon\Db\Column;

class SqlIdeaRepository implements IdeaRepository
{
    private $connection;
    private $statement;
    private $statementTypes;

    const INDEX_ID = 0, INDEX_TITLE = 1, INDEX_DESCRIPTION = 2, INDEX_AUTHOR_NAME = 3, INDEX_AUTHOR_EMAIL = 4, INDEX_VOTES = 5, INDEX_RATING = 6;

    public function __construct($di)
    {
        $this->connection = $di->get('db');
        $this->statement = [
            'create' => $this->connection->prepare(
                "INSERT INTO idea VALUES (:id, :title, :description, :authorName, :authorEmail, :votes)"
            ),
            'update' => $this->connection->prepare(
                "UPDATE idea SET title=:title, description=:description, author_name=:authorName, author_email=:authorEmail, votes=:votes WHERE id=:id"
            ),
            'find_by_id' => $this->connection->prepare(
                "SELECT idea.id as id, idea.title as title, idea.description as description, idea.author_name as author_name, idea.author_email as author_email, idea.votes as votes, AVG(rating.value) as rating FROM idea LEFT JOIN rating ON idea.id = rating.idea_id  WHERE idea.id = :id GROUP BY idea.id"
            ),
            'find_all' => $this->connection->prepare(
                "SELECT idea.id as id, idea.title as title, idea.description as description, idea.author_name as author_name, idea.author_email as author_email, idea.votes as votes, AVG(rating.value) as rating FROM idea LEFT JOIN rating ON idea.id = rating.idea_id GROUP BY idea.id"
            ),
        ];

        $this->statementTypes = [
            'create' => [
                'id' => Column::BIND_PARAM_STR,
                'title' => Column::BIND_PARAM_STR,
                'description' => Column::BIND_PARAM_STR,
                'authorName' => Column::BIND_PARAM_STR,
                'authorEmail' => Column::BIND_PARAM_STR,
                'votes' => Column::BIND_PARAM_INT,
            ],
            'update' => [
                'title' => Column::BIND_PARAM_STR,
                'description' => Column::BIND_PARAM_STR,
                'authorName' => Column::BIND_PARAM_STR,
                'authorEmail' => Column::BIND_PARAM_STR,
                'votes' => Column::BIND_PARAM_INT,
                'id' => Column::BIND_PARAM_STR,
            ],
            'find_by_id' => [
                'id' => Column::BIND_PARAM_STR,
            ],
            'find_all' => [],
        ];
    }

    public function byId(IdeaId $id)
    {
        $ideaIdData = [
            'id' => $id->id(),
        ];

        $result = $this->connection->executePrepared(
            $this->statement['find_by_id'],
            $ideaIdData,
            $this->statementTypes['find_by_id']
        );

        $result = $result->fetch();

        if ($result) {
            return new Idea(
                new IdeaId(
                    $result[self::INDEX_ID]
                ),
                $result[self::INDEX_TITLE],
                $result[self::INDEX_DESCRIPTION],
                new Author(
                    $result[self::INDEX_AUTHOR_NAME],
                    $result[self::INDEX_AUTHOR_EMAIL]
                ),
                $result[self::INDEX_VOTES],
                $result[self::INDEX_RATING]
            );
        } else {
            return null;
        }
    }

    public function create(Idea $idea)
    {
        $ideaData = [
            'id' => $idea->id()->id(),
            'title' => $idea->title(),
            'description' => $idea->description(),
            'authorName' => $idea->author()->name(),
            'authorEmail' => $idea->author()->email(),
            'votes' => $idea->votes(),
        ];

        $success = $this->connection->executePrepared(
            $this->statement['create'],
            $ideaData,
            $this->statementTypes['create']
        );
    }

    public function update(Idea $idea)
    {
        $ideaData = [
            'title' => $idea->title(),
            'description' => $idea->description(),
            'authorName' => $idea->author()->name(),
            'authorEmail' => $idea->author()->email(),
            'votes' => $idea->votes(),
            'id' => $idea->id()->id(),
        ];

        $success = $this->connection->executePrepared(
            $this->statement['update'],
            $ideaData,
            $this->statementTypes['update']
        );
    }

    public function save(Idea $idea)
    {
        try {
            $ideaFromDB = $this->byId($idea->id());
            if ($ideaFromDB != null) {
                $this->update($idea);
            } else {
                throw new ResourceNotFoundException();
            }
        } catch (ResourceNotFoundException $exception) {
            $this->create($idea);
        }
    }

    public function allIdeas()
    {
        $result = $this->connection->executePrepared(
            $this->statement['find_all'],
            [],
            $this->statementTypes['find_all']
        );

        $ideas = array();
        foreach ($result as $item) {
            array_push($ideas, new Idea(
                    new IdeaId(
                        $item[self::INDEX_ID]
                    ),
                    $item[self::INDEX_TITLE],
                    $item[self::INDEX_DESCRIPTION],
                    new Author(
                        $item[self::INDEX_AUTHOR_NAME],
                        $item[self::INDEX_AUTHOR_EMAIL]
                    ),
                    $item[self::INDEX_VOTES],
                    $item[self::INDEX_RATING]
                )
            );
        }

        return $ideas;
    }
    
}