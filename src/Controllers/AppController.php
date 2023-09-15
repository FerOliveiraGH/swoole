<?php

namespace Fernando\Swoole\Controllers;

use GuzzleHttp\Client;

class AppController
{
    public function __construct()
    {
        $this->client = new Client(['verify' => false]);
    }

    public function getUsers()
    {
        $response = $this->client->get('https://jsonplaceholder.typicode.com/users');

        return $response->getBody()->getContents();
    }

    public function getUserTodos($user)
    {
        $response = $this->client->get("https://jsonplaceholder.typicode.com/users/$user/todos");

        return $response->getBody()->getContents();
    }

    public function getPosts()
    {
        $response = $this->client->get('https://jsonplaceholder.typicode.com/posts');

        return $response->getBody()->getContents();
    }
}