<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Spagi\EmailParser\EmailMessageService;
use Spagi\EmailParser\ImapClientFactory;

final class EmailMessageServiceTest extends TestCase
{
    /**
     * @test
     */
    public function findCategoryNoteTest(): void
    {
        $array = ['note' => "Restaurant Blahovka ma nejlepsi pivo v Brne"];

        $client = Mockery::mock(ImapClientFactory::class);
        $service = new EmailMessageService($client);
        $result = $service->findCategoryNote($array);

        static::assertEquals("Jidlo", $result);
    }

    /**
     * @test
     */
    public function parseMessageTest(): void
    {
        $message = "nejaka testovaci zprava \r\n ktera ma \r\n byt zpracovana";
        $client = Mockery::mock(ImapClientFactory::class);
        $service = new EmailMessageService($client);
        $collection = $service->parseMessage($message);

        static::assertEquals(3, $collection->count());
    }

    /**
     * @test
     */
    public function getPriceDetailsTest(): void
    {
        $array = ['price' => "555 CZK"];
        $client = Mockery::mock(ImapClientFactory::class);
        $service = new EmailMessageService($client);
        $price = $service->getPriceDetails($array);

        static::assertEquals(2, count($price));
        static::assertEquals(['price' => 555, 'currency' => 'CZK'], $price);

    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}
