<?php

namespace Idy\Idea\Controllers\Web;

use Idy\Idea\Application\CreateNewIdeaRequest;
use Idy\Idea\Application\CreateNewIdeaService;
use Idy\Idea\Application\RateIdeaRequest;
use Idy\Idea\Application\RateIdeaService;
use Idy\Idea\Application\ViewAllIdeasService;
use Idy\Idea\Application\VoteIdeaRequest;
use Idy\Idea\Application\VoteIdeaService;
use Idy\Idea\Controllers\Validators\CreateNewIdeaValidator;
use Idy\Idea\Domain\Model\IdeaId;
use Idy\Idea\Infrastructure\SqlIdeaRepository;
use Phalcon\Mvc\Controller;
use Phalcon\Di;

class IdeaController extends Controller
{
    public function indexAction()
    {
        $ideaRepository = Di::getDefault()->get('sql_idea_repository');
        $service = new ViewAllIdeasService($ideaRepository);
        $response = $service->execute();

        $this->view->setVar('ideas', $response->data);
        return $this->view->pick('home');
    }

    public function addFormAction()
    {
        return $this->view->pick('add');
    }

    public function addAction()
    {
        if (!$this->request->isPost()) {
            return $this->view->pick('add');
        }
        $validator = new CreateNewIdeaValidator();
        $messages = $validator->validate($_POST);
        if (count($messages)) {
            foreach ($messages as $message) {
                $this->flashSession->error($message->getMessage());
            }
            return $this->view->pick('add');
        }

        $ideaRepository = Di::getDefault()->get('sql_idea_repository');
        $service = new CreateNewIdeaService($ideaRepository);
        $response = $service->execute(
            new CreateNewIdeaRequest(
                $this->request->getPost('title'),
                $this->request->getPost('description'),
                $this->request->getPost('author_name'),
                $this->request->getPost('author_email')
            )
        );

        $this->flashSession->error($response->message);
        return $this->view->pick('home');
    }

    public function voteAction()
    {
        $ideaRepository = Di::getDefault()->get('sql_idea_repository');
        $service = new VoteIdeaService($ideaRepository);
        $response = $service->execute(
            new VoteIdeaRequest(
                new IdeaId(
                    $this->request->getPost('id')
                )
            )
        );

        return $this->response->redirect('/');
    }

    public function rateAction()
    {
        $ratingRepository = Di::getDefault()->get('sql_rating_repository');
        $ideaRepository = Di::getDefault()->get('sql_idea_repository');
        $service = new RateIdeaService($ratingRepository, $ideaRepository);
        $response = $service->execute(
            new RateIdeaRequest(
                new IdeaId(
                    $this->request->getPost('id')
                ),
                'Anonymous',
                $this->request->getPost('rate')
            )
        );

        return $this->response->redirect('/');
    }

    public function rateFormAction()
    {
        $this->view->setVar('idea_id', $this->request->getQuery('id'));
        return $this->view->pick('vote');
    }

}