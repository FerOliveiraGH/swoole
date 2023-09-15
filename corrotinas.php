<?php

require_once 'vendor/autoload.php';

use Fernando\Swoole\Controllers\AppController;

$teste = [];

\Co\run(function () use (&$teste) {
    echo 'teste1';
    $teste['users'] = json_decode((new AppController())->getUsers(), true);

    foreach ($teste['users'] as $key => $user) {
        go(function () use ($key, $user, &$teste) {
            $teste['users'][$key]['todos'] = json_decode((new AppController())->getUserTodos($user['id']), true);
            echo 'teste2';
        });
    }


    go(function () use (&$teste) {
        echo 'teste3';
        $teste['posts'] = json_decode((new AppController())->getPosts(), true);
    });
});

var_dump($teste['users'][0]['name'], $teste['posts'][0]['title']);