<?php

use Idy\Common\Events\DomainEventPublisher;
use Phalcon\Mvc\View;
use Idy\Idea\Infrastructure\SqlIdeaRepository;
use Idy\Idea\Infrastructure\SqlRatingRepository;
use Idy\Idea\Application\SendRatingNotificationService;

$di['voltServiceMail'] = function($view) use ($di) {

    $config = $di->get('config');

    $volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);
    if (!is_dir($config->mail->cacheDir)) {
        mkdir($config->mail->cacheDir);
    }

    $compileAlways = $config->mode == 'DEVELOPMENT' ? true : false;

    $volt->setOptions(array(
        "compiledPath" => $config->mail->cacheDir,
        "compiledExtension" => ".compiled",
        "compileAlways" => $compileAlways
    ));
    return $volt;
};

$di['view'] = function () {
    $view = new View();
    $view->setViewsDir(__DIR__ . '/../views/');

    $view->registerEngines(
        [
            ".volt" => "voltService",
        ]
    );

    return $view;
};

$di['db'] = function () use ($di) {

    $config = $di->get('config');

    $dbAdapter = $config->database->adapter;

    return new $dbAdapter([
        "host" => $config->database->host,
        "username" => $config->database->username,
        "password" => $config->database->password,
        "dbname" => $config->database->dbname
    ]);
};
$di['mail'] = function () use ($di) {

    $config = $di->get('config');

    $mailDriver = $config->mail->driver;

    return new $mailDriver([
        'driver'     => 'smtp',
        'host'       => $config->mail->smtp->server,
        'port'       => $config->mail->smtp->port,
        'encryption' => 'ssl',
        'username'   => $config->mail->smtp->username,
        'password'   => $config->mail->smtp->password,
        'from' => [
            'email'  => $config->mail->fromEmail,
            'name'   => $config->mail->fromName,
        ],
    ]);
};

$di->setShared('sql_idea_repository', function() use ($di) {
    $repo = new SqlIdeaRepository($di);

    return $repo;
});

$di->setShared('sql_rating_repository', function() use ($di) {
    $repo = new SqlRatingRepository($di);

    return $repo;
});

DomainEventPublisher::instance()->subscribe(new SendRatingNotificationService());