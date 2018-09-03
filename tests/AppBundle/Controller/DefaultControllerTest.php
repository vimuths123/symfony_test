<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $this->assertGreaterThan(
                0, $crawler->filter('a#btnEmpty')->count()
        );
        
        $this->assertGreaterThan(
                0, $crawler->filter('select#book_category_category')->count()
        );
        
        $this->assertGreaterThan(
                0, $crawler->filter('div#product-grid')->count()
        );
        
        $this->assertGreaterThan(
                0, $crawler->filter('html:contains("Shopping Cart")')->count()
        );
    }
    
    public function testCheckout()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/checkout');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());      
    }
}
