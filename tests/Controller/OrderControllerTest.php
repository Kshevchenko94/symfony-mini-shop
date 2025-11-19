<?php

namespace App\Tests\Controller;

use App\Entity\Order;
use App\Entity\User;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class OrderControllerTest extends WebTestCase
{
    private ?KernelBrowser $client;
    private ?OrderRepository $orderRepository;
    private ?User $testUser;
    const string TEST_USER_EMAIL = 'email@example.com';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->orderRepository = static::getContainer()->get(OrderRepository::class);
        $this->testUser = static::getContainer()
            ->get(UserRepository::class)
            ->findOneByEmail(self::TEST_USER_EMAIL);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        if (null === $this->orderRepository) {
            $this->orderRepository->close();
            $this->orderRepository = null;
        }
    }

    /**
     * @return void
     */
    public function testForbiddenUserToFormSuccess(): void
    {
        $this->client->request('GET', '/order');

        self::assertResponseStatusCodeSame(403);
    }

    /**
     * @return void
     */
    public function testAuthorizedUserToFormSuccess(): void
    {
        $this->client->loginUser($this->testUser);
        $crawler = $this->client->request('GET', '/order');

        self::assertResponseIsSuccessful();
        $this->assertGreaterThanOrEqual(1, $crawler->filter('select[name="order[service]"]')->count());
        $this->assertGreaterThanOrEqual(3, $crawler->filter('select[name="order[service]"]>option')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('input[name="order[email]"]')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('button[type="submit"]')->count());
    }

    /**
     * @return void
     */
    public function testCreatedOrderSuccess(): void
    {
        $this->client->loginUser($this->testUser);
        $this->client->request('GET', '/order');
        $this->client->submitForm('Подтвердить', [
            'order[service]' => '4',
            'order[email]' => 'test@mail.ru',
        ]);

        $order = $this->orderRepository->findOneBy(
            [
                'email' => 'test@mail.ru',
                'service' => '4',
            ]
        );

        self::assertResponseStatusCodeSame(302);
        self::assertInstanceOf(Order::class, $order);
        self::assertEquals('test@mail.ru', $order->getEmail());
    }

    public function testCreatedOrderFail(): void
    {
        $this->client->loginUser($this->testUser);
        $this->client->request('GET', '/order');
        $this->client->submitForm('Подтвердить', [
            'order[service]' => '4',
        ]);

        self::assertResponseStatusCodeSame(422);
    }
}
