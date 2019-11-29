<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpBugs\Retriever;

use Amp\Promise;
use AsyncBot\Core\Http\Client;
use AsyncBot\Plugin\PhpBugs\Event\Data\Bugs;
use AsyncBot\Plugin\PhpBugs\Parser\Html as Parser;
use function Amp\call;

final class GetAllBugs implements Retriever
{
    private Client $httpClient;

    private Parser $parser;

    public function __construct(Client $httpClient, Parser $parser)
    {
        $this->httpClient = $httpClient;
        $this->parser     = $parser;
    }

    /**
     * @return Promise<Bugs>
     */
    public function retrieve(): Promise
    {
        return call(function () {
            /** @var \DOMDocument $dom */
            $dom = yield $this->httpClient->requestHtml(
                'https://bugs.php.net/search.php?limit=30&order_by=id&direction=DESC&cmd=display&status=Open&bug_type=All',
            );

            return $this->parser->parse($dom);
        });
    }
}
