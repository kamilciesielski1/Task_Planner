<?php

namespace TaskPlannerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    public function testNewtask()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/newTask');
    }

    public function testDeletetask()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/deleteTask');
    }

    public function testEdittask()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/editTask');
    }

}
