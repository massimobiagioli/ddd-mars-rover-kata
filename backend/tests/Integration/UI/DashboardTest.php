<?php
declare(strict_types=1);

namespace MarsRoverKata\Tests\Integration\UI;

use MarsRoverKata\Application\Query\MarsRover\MarsRover;
use MarsRoverKata\Application\Query\MarsRover\MarsRoverRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DashboardTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->catchExceptions(false);

        /** @var MarsRoverRepository $repository */
        $repository = static::$container->get(MarsRoverRepository::class);

        $marsRover = new MarsRover(
            '85dad1c7-1ee0-47d1-bb39-006d072ab79b',
            'mars rover test',
            new \DateTime('2022-02-22 22:22:22'),
            1,
            2,
            'N',
            3
        );
        $repository->store($marsRover);
    }

    public function test_it_load_dashboard(): void
    {
        $this->client->request(
            'GET',
            '/api/dashboard',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );

        $response = $this->client->getResponse();
        $dashboardData = json_decode($response->getContent(), true);

        self::assertEquals(200, $response->getStatusCode());
        self::assertCount(1, $dashboardData);
        self::assertEquals([
            "id" => "85dad1c7-1ee0-47d1-bb39-006d072ab79b",
            "name" => "mars rover test",
            "createdAt" => [
                "date" => "2022-02-22 22:22:22.000000",
                "timezone_type" => 3,
                "timezone" => "UTC"
            ],
            "coordinate_x" => 1,
            "coordinate_y" => 2,
            "orientation" => "N",
            "km" => 3
        ], $dashboardData[0]);
    }
}