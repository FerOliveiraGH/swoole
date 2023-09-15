<?php

require_once 'vendor/autoload.php';

use Fernando\Swoole\Controllers\AppController;
use Swoole\Coroutine as Co;
use Swoole\Coroutine\Channel;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

Co::set(['hook_flags' => SWOOLE_HOOK_CURL]);

$http = new Server('0.0.0.0', 8080);

$http->on('start', function () {
    echo 'Start' . PHP_EOL;
});

$http->on('request', function (Request $request, Response $response) {
    $response->header('Content-type', 'application/json; charset=utf-8');
    $channel = new Channel(2);

    go(function () use ($channel) {
        echo 'Users' . PHP_EOL;
        $users = json_decode((new AppController())->getUsers(), true);

        $channelTodos = new Channel(count($users));
        foreach ($users as $key => $user) {
            go(function () use ($key, $user, $channelTodos) {
                echo "Todos$key" . PHP_EOL;
                $todos = json_decode((new AppController())->getUserTodos($user['id']), true);
                $channelTodos->push($todos);
            });
        }

        for ($x=0; $x < $channelTodos->capacity; $x++) {
            $users[$x]['todos'] = $channelTodos->pop();
        }

        $channel->push(['users' => $users]);
    });

    go(function () use ($channel) {
        echo 'Posts' . PHP_EOL;
        $posts = json_decode((new AppController())->getPosts(), true);
        $channel->push(['posts' => $posts]);
    });

    go(function () use (&$response, $channel) {
        $data = [];
        for($x=0; $x < $channel->capacity; $x++) {
            $data = array_merge($data, $channel->pop());
        }
        $response->end(json_encode($data));
    });

});

$http->start();