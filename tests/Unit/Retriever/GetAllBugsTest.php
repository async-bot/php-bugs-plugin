<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpBugsTest\Unit\Retriever;

use Amp\Http\Client\HttpClientBuilder;
use AsyncBot\Core\Http\Client;
use AsyncBot\Plugin\PhpBugs\Event\Data\Bugs;
use AsyncBot\Plugin\PhpBugs\Parser\Html as Parser;
use AsyncBot\Plugin\PhpBugs\Retriever\GetAllBugs;
use AsyncBot\Plugin\PhpBugsTest\Fakes\HttpClient\MockResponseInterceptor;
use PHPUnit\Framework\TestCase;
use function Amp\Promise\wait;

final class GetAllBugsTest extends TestCase
{
    public function testRetrieveReturnsBugs(): void
    {
        $httpClient = new Client(
            (new HttpClientBuilder())->intercept(
                new MockResponseInterceptor(file_get_contents(TEST_DATA_DIR . '/ResponseHtml/valid.html')),
            )->build(),
        );

        $status = wait((new GetAllBugs($httpClient, new Parser()))->retrieve());

        $this->assertInstanceOf(Bugs::class, $status);
    }
}
