<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testSomething()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testSearchControllerAction()
    {
        $client = static::createClient();
        $client->request('GET', '/search?f[hotel_name]=hotel&f[city]=cairo&f[price_max]=200&s[price]=asc');

        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('content-type'));

        $this->assertSame('cairo', json_decode($response->getContent())[0]->city);
        $this->assertContains('hotel', json_decode($response->getContent())[0]->name, '', true);
        $this->assertSame('cairo', json_decode($response->getContent())[0]->city);
    }
}
