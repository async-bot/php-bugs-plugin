<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpBugs\Parser;

use AsyncBot\Plugin\PhpBugs\Event\Data\Bug;
use AsyncBot\Plugin\PhpBugs\Event\Data\Bugs;
use AsyncBot\Plugin\PhpBugs\Event\Data\Status;
use AsyncBot\Plugin\PhpBugs\Event\Data\Type;
use AsyncBot\Plugin\PhpBugs\Exception\UnexpectedHtmlFormat;

final class Html
{
    private const BASE_URL = 'https://bugs.php.net/';

    public function parse(\DOMDocument $dom): Bugs
    {
        $xpath = new \DOMXPath($dom);

        /** @var \DOMNodeList $bugRows */
        $bugRows = $xpath->evaluate('//tr[@class]');

        return new Bugs(...array_reverse($this->parseRows($bugRows)));
    }

    /**
     * @return array<Bug>
     */
    private function parseRows(\DOMNodeList $bugRows): array
    {
        $bugs = [];

        foreach ($bugRows as $bugRow) {
            $bugs[] = $this->parseBugRow($bugRow);
        }

        return $bugs;
    }

    private function parseBugRow(\DOMElement $bugRow): Bug
    {
        return new Bug(
            $this->getId($bugRow),
            $this->getUrl($bugRow),
            $this->getTimestamp($bugRow),
            $this->getLastModified($bugRow),
            $this->getPackage($bugRow),
            $this->getType($bugRow),
            $this->getStatus($bugRow),
            $this->getPhpVersion($bugRow),
            $this->getOs($bugRow),
            $this->getSummary($bugRow),
        );
    }

    private function getColumn(\DOMElement $row, int $index, string $elementName): \DOMElement
    {
        $column = $row->getElementsByTagName('td')->item($index);

        if (!$column instanceof \DOMElement) {
            throw new UnexpectedHtmlFormat($elementName);
        }

        return $column;
    }

    private function getId(\DOMElement $bugRow): int
    {
        $idColumn = $this->getColumn($bugRow, 0, 'id');

        $idNode = $idColumn->getElementsByTagName('a')->item(0);

        if (!$idNode instanceof \DOMNode) {
            throw new UnexpectedHtmlFormat('id');
        }

        return (int) $idNode->textContent;
    }

    private function getUrl(\DOMElement $bugRow): string
    {
        $urlColumn = $this->getColumn($bugRow, 0, 'url');

        $urlNode = $urlColumn->getElementsByTagName('a')->item(0);

        if (!$urlNode instanceof \DOMElement) {
            throw new UnexpectedHtmlFormat('url');
        }

        return self::BASE_URL . $urlNode->getAttribute('href');
    }

    private function getTimestamp(\DOMElement $bugRow): \DateTimeImmutable
    {
        $timestampColumn = $this->getColumn($bugRow, 1, 'timestamp');

        return new \DateTimeImmutable($timestampColumn->textContent);
    }

    private function getLastModified(\DOMElement $bugRow): ?\DateTimeImmutable
    {
        $lastModifiedColumn = $this->getColumn($bugRow, 2, 'last modified');

        if (trim($lastModifiedColumn->textContent) === 'Not modified') {
            return null;
        }

        return new \DateTimeImmutable($lastModifiedColumn->textContent);
    }

    private function getPackage(\DOMElement $bugRow): string
    {
        $packageColumn = $this->getColumn($bugRow, 3, 'package');

        return trim($packageColumn->textContent);
    }

    private function getType(\DOMElement $bugRow): Type
    {
        $typeColumn = $this->getColumn($bugRow, 4, 'type');

        return Type::fromBugsNet(trim($typeColumn->textContent));
    }

    private function getStatus(\DOMElement $bugRow): Status
    {
        $statusColumn = $this->getColumn($bugRow, 5, 'status');

        return Status::fromBugsNet(trim($statusColumn->textContent));
    }

    private function getPhpVersion(\DOMElement $bugRow): string
    {
        $versionColumn = $this->getColumn($bugRow, 6, 'PHP version');

        return trim($versionColumn->textContent);
    }

    private function getOs(\DOMElement $bugRow): ?string
    {
        $osColumn = $this->getColumn($bugRow, 7, 'OS');

        $osContent = trim(str_replace("\xC2\xA0", ' ', $osColumn->textContent));

        if ($osContent === '') {
            return null;
        }

        return $osContent;
    }

    private function getSummary(\DOMElement $bugRow): ?string
    {
        $summaryColumn = $this->getColumn($bugRow, 8, 'summary');

        return trim($summaryColumn->textContent);
    }
}
