<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/book/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testNewBook() {
        $client = static::createClient();
        
        $crawler = $client->request('GET', '/book/new');
        
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

//        $form = $crawler->filter('button.btn-success')->form(array(
//            'appbundle_book[name]' => 'Book',
//            'appbundle_book[price]' => '10',
//            'appbundle_book[description]' => 'fffff',
//            'appbundle_book[code]' => 'code',
//        ));
//        $crawler = $client->submit($form);
//
//        $this->assertTrue($client->getResponse()->isRedirection());
//        $crawler = $client->followRedirect();
//        $this->assertGreaterThan(
//                0, $crawler->filter('html:contains("Book")')->count()
//        );
    }
}
