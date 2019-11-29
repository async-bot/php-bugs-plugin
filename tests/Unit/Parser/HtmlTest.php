<?php declare(strict_types=1);

namespace AsyncBot\Plugin\PhpBugsTest\Unit\Parser;

use AsyncBot\Plugin\PhpBugs\Event\Data\Bug;
use AsyncBot\Plugin\PhpBugs\Exception\UnexpectedHtmlFormat;
use AsyncBot\Plugin\PhpBugs\Parser\Html;
use PHPUnit\Framework\TestCase;
use function Room11\DOMUtils\domdocument_load_html;

final class HtmlTest extends TestCase
{
    private function getTestDataDom(string $filename): \DOMDocument
    {
        return domdocument_load_html(file_get_contents(TEST_DATA_DIR . $filename));
    }

    public function testParseThrowsWhenIdColumnCanNotBeFound(): void
    {
        $this->expectException(UnexpectedHtmlFormat::class);
        $this->expectExceptionMessage('Could not find the "id" element in the row');

        (new Html())->parse($this->getTestDataDom('/ResponseHtml/missing-id-column.html'));
    }

    public function testParseThrowsWhenIdNodeCanNotBeFound(): void
    {
        $this->expectException(UnexpectedHtmlFormat::class);
        $this->expectExceptionMessage('Could not find the "id" element in the row');

        (new Html())->parse($this->getTestDataDom('/ResponseHtml/missing-id-node.html'));
    }

    public function testParseThrowsWhenTimestampColumnCanNotBeFound(): void
    {
        $this->expectException(UnexpectedHtmlFormat::class);
        $this->expectExceptionMessage('Could not find the "timestamp" element in the row');

        (new Html())->parse($this->getTestDataDom('/ResponseHtml/missing-timestamp-column.html'));
    }

    public function testParseThrowsWhenLastModifiedColumnCanNotBeFound(): void
    {
        $this->expectException(UnexpectedHtmlFormat::class);
        $this->expectExceptionMessage('Could not find the "last modified" element in the row');

        (new Html())->parse($this->getTestDataDom('/ResponseHtml/missing-last-modified-column.html'));
    }

    public function testParseThrowsWhenPackageColumnCanNotBeFound(): void
    {
        $this->expectException(UnexpectedHtmlFormat::class);
        $this->expectExceptionMessage('Could not find the "package" element in the row');

        (new Html())->parse($this->getTestDataDom('/ResponseHtml/missing-package-column.html'));
    }

    public function testParseThrowsWhenTypeColumnCanNotBeFound(): void
    {
        $this->expectException(UnexpectedHtmlFormat::class);
        $this->expectExceptionMessage('Could not find the "type" element in the row');

        (new Html())->parse($this->getTestDataDom('/ResponseHtml/missing-type-column.html'));
    }

    public function testParseThrowsWhenStatusColumnCanNotBeFound(): void
    {
        $this->expectException(UnexpectedHtmlFormat::class);
        $this->expectExceptionMessage('Could not find the "status" element in the row');

        (new Html())->parse($this->getTestDataDom('/ResponseHtml/missing-status-column.html'));
    }

    public function testParseThrowsWhenPhpVersionColumnCanNotBeFound(): void
    {
        $this->expectException(UnexpectedHtmlFormat::class);
        $this->expectExceptionMessage('Could not find the "PHP version" element in the row');

        (new Html())->parse($this->getTestDataDom('/ResponseHtml/missing-php-version-column.html'));
    }

    public function testParseThrowsWhenOsColumnCanNotBeFound(): void
    {
        $this->expectException(UnexpectedHtmlFormat::class);
        $this->expectExceptionMessage('Could not find the "OS" element in the row');

        (new Html())->parse($this->getTestDataDom('/ResponseHtml/missing-os-column.html'));
    }

    public function testParseThrowsWhenSummaryColumnCanNotBeFound(): void
    {
        $this->expectException(UnexpectedHtmlFormat::class);
        $this->expectExceptionMessage('Could not find the "summary" element in the row');

        (new Html())->parse($this->getTestDataDom('/ResponseHtml/missing-summary-column.html'));
    }

    public function testParseReturnsCorrectCollection(): void
    {
        $bugs = (new Html())->parse($this->getTestDataDom('/ResponseHtml/valid.html'));

        $this->assertCount(33, $bugs);
    }

    public function testParseReturnsCorrectDataInCollection(): void
    {
        $bugs = (new Html())->parse($this->getTestDataDom('/ResponseHtml/valid.html'));

        $expectedData = [
            [
                'id'           => 78379,
                'timestamp'    => '2019-08-06 05:34:00',
                'lastModified' => '2019-08-13 08:01:00',
                'package'      => 'Reproducible crash',
                'type'         => 'Bug',
                'status'       => 'Re-Opened',
                'phpVersion'   => '7.2Git-2019-08-06 (Git)',
                'os'           => 'Linux',
                'summary'      => 'Cast to object confuses GC, causes crash',
            ],
            [
                'id'           => 78380,
                'timestamp'    => '2019-08-06 09:46:00',
                'lastModified' => '2019-08-21 00:49:00',
                'package'      => 'mbstring related',
                'type'         => 'Security',
                'status'       => 'Assigned',
                'phpVersion'   => '7.1.31',
                'os'           => '*',
                'summary'      => 'Oniguruma 6.9.3 fixes CVEs',
            ],
            [
                'id'           => 78382,
                'timestamp'    => '2019-08-06 16:43:00',
                'lastModified' => null,
                'package'      => 'Apache2 related',
                'type'         => 'Bug',
                'status'       => 'Open',
                'phpVersion'   => '7.3.8',
                'os'           => 'Ubuntu 18.04 LTS [Buster]',
                'summary'      => 'Segmentation Violation in opcache',
            ],
            [
                'id'           => 78384,
                'timestamp'    => '2019-08-07 17:19:00',
                'lastModified' => '2019-08-08 12:21:00',
                'package'      => 'PDO SQLite',
                'type'         => 'Bug',
                'status'       => 'Open',
                'phpVersion'   => '7.1.31',
                'os'           => 'Linux Ubuntu 14',
                'summary'      => 'Missing .c file. Compilation error',
            ],
        ];

        foreach ($bugs as $index => $bug) {
            $this->assertSame($expectedData[$index]['id'], $bug->getId());
            $this->assertSame($expectedData[$index]['timestamp'], $bug->getTimestamp()->format('Y-m-d H:i:s'));
            $this->assertSame($expectedData[$index]['lastModified'], $bug->getLastModified() === null ? $bug->getLastModified() : $bug->getLastModified()->format('Y-m-d H:i:s'));
            $this->assertSame($expectedData[$index]['package'], $bug->getPackage());
            $this->assertSame($expectedData[$index]['type'], $bug->getType()->toString());
            $this->assertSame($expectedData[$index]['status'], $bug->getStatus()->toString());
            $this->assertSame($expectedData[$index]['phpVersion'], $bug->getPhpVersion());
            $this->assertSame($expectedData[$index]['os'], $bug->getOs());
            $this->assertSame($expectedData[$index]['summary'], $bug->getSummary());

            if ($index === 3) {
                break;
            }
        }
    }

    public function testParseReturnsCorrectDataForLastRow(): void
    {
        $bugs = (new Html())->parse($this->getTestDataDom('/ResponseHtml/valid.html'));

        $bug = $bugs->getLast();

        $this->assertSame(78448, $bug->getId());
        $this->assertSame('2019-08-23 08:21:00', $bug->getTimestamp()->format('Y-m-d H:i:s'));
        $this->assertSame(null, $bug->getLastModified() === null ? $bug->getLastModified() : $bug->getLastModified()->format('Y-m-d H:i:s'));
        $this->assertSame('Documentation problem', $bug->getPackage());
        $this->assertSame('Documentation Problem', $bug->getType()->toString());
        $this->assertSame('Open', $bug->getStatus()->toString());
        $this->assertSame('Irrelevant', $bug->getPhpVersion());
        $this->assertSame(null, $bug->getOs());
        $this->assertSame('The definition of the parameter newscope is not clear.', $bug->getSummary());
    }

    public function testParseReturnsNullForNotModifiedLastModifiedTimestamp(): void
    {
        $bugs = (new Html())->parse($this->getTestDataDom('/ResponseHtml/valid.html'));

        /** @var Bug $bugWithoutModifications */
        $bugWithoutModifications = iterator_to_array($bugs)[2];

        $this->assertNull($bugWithoutModifications->getLastModified());
    }

    public function testParseReturnsNullForNonFilledInOs(): void
    {
        $bugs = (new Html())->parse($this->getTestDataDom('/ResponseHtml/valid.html'));

        /** @var Bug $bugWithoutOs */
        $bugWithoutOs = iterator_to_array($bugs)[5];

        $this->assertNull($bugWithoutOs->getOs());
    }
}
