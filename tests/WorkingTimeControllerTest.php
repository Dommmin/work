<?php

namespace App\Tests;

use App\Entity\Employee;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WorkingTimeControllerTest extends WebTestCase
{
    public function testSuccessCreateWorkingTime(): void
    {
        $client = static::createClient();

        $employee = new Employee();
        $employee->setFirstName('Dominik');
        $employee->setLastName('Test');

        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);

        $entityManager->persist($employee);
        $entityManager->flush();

        $jsonData = json_encode([
            'employee' => $employee->getUuid(),
            'startDate' => Carbon::now()->format('Y-m-d H:i:s'),
            'endDate' => Carbon::now()->addHours(2)->format('Y-m-d H:i:s'),
        ]);

        $headers = [
            'HTTP_CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ];

        $client->request('POST', '/v1/working-times', [], [], $headers, $jsonData);

        $this->assertResponseIsSuccessful();
    }
}
