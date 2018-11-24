<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TicketControllerTest extends WebTestCase {

    public function testTicketIsUp(){
        $client = static::createClient();
        $client->request('GET', '/billeterie');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

}