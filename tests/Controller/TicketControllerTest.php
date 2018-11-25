<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TicketControllerTest extends WebTestCase {

    public function testTicketIsUp(){
        $client = static::createClient();
        $client->request('GET', '/billeterie');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testTicket(){
        $client = static::createClient();
        $crawler = $client->request('GET', '/billeterie');

        $form = $crawler->selectButton('Valider')->form();
        $form['reservation[email]'] = 'test@test.fr';
        $form['reservation[visitDate]'] = \DateTime::class;
        if(date('H') >= 14){
            $form['reservation[halfDay]'] = true;
        }

        $values = array_merge_recursive(
            $form->getPhpValues(),
            array(
                'reservation[visitors]' => array (
                    'visitors' => array(
                        array(
                            'reservation[visitors][0][lastname]' => 'Martin',
                            'reservation[visitors][0][firstname]' => 'Julien',
                            'reservation[visitors][0][birthdate][day]' => '15',
                            'reservation[visitors][0][birthdate][month]' => '01',
                            'reservation[visitors][0][birthdate][year]' => '1996',
                            'reservation[visitors][0][nationality]' => 'Française',
                            'reservation[visitors][0][reducedRate]' => 1
                        )
                    )
                )
            )
        );

        $client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $client->submit($form);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

}