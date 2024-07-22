<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EmployeeControllerTest extends WebTestCase
{
    public function testCreateEmployee(): void
    {
        $client = static::createClient();

        $jsonData = json_encode([
            'firstName' => 'Dominik',
            'lastName' => 'Test',
        ]);

        $headers = [
            'HTTP_CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ];

        $client->request('POST', '/v1/employees', [], [], $headers, $jsonData);

        $this->assertResponseIsSuccessful();
    }
}
