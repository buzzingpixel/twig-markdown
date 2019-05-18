<?php

declare(strict_types=1);

namespace BuzzingPixelTests\TwigMarkdown;

use BuzzingPixel\TwigMarkdown\MarkdownTwigExtension;
use corbomite\di\Di;
use PHPUnit\Framework\TestCase;
use Throwable;

class MarkdownTwigExtensionTest extends TestCase
{
    /** @var MarkdownTwigExtension */
    private $markdownTwigExtension;

    /**
     * @throws Throwable
     */
    public function setUp() : void
    {
        $this->markdownTwigExtension = Di::diContainer()->get(MarkdownTwigExtension::class);
    }

    public function testGetFilters() : void
    {
        $filters = $this->markdownTwigExtension->getFilters();

        self::assertCount(2, $filters);

        $counter = 0;

        foreach ($filters as $filter) {
            $counter++;

            $callable = $filter->getCallable();

            self::assertInstanceOf(MarkdownTwigExtension::class, $callable[0]);

            self::assertSame($this->markdownTwigExtension, $callable[0]);

            if ($counter === 1) {
                self::assertSame('markdownParse', $filter->getName());

                self::assertSame('markdownParse', $callable[1]);

                continue;
            }

            if ($counter === 2) {
                self::assertSame('markdownParseParagraph', $filter->getName());

                self::assertSame('markdownParseParagraph', $callable[1]);

                continue;
            }
        }

        self::assertSame(2, $counter);
    }

    public function testMarkdownParseDefaultGitHub() : void
    {
        $returnVal = $this->markdownTwigExtension->markdownParse(
            "## Header 2 ## {#header2}\n\n" .
            'I ~~still~~ used to smoke [cigars](/cigars)'
        );

        self::assertEquals(
            "<h2>Header 2 ## {#header2}</h2>\n" .
            "<p>I <del>still</del> used to smoke <a href=\"/cigars\">cigars</a></p>\n",
            (string) $returnVal
        );
    }

    public function testMarkdownParseParagraphDefaultGitHub() : void
    {
        $returnVal = $this->markdownTwigExtension->markdownParseParagraph(
            "## Header 2 ## {#header2}\n\n" .
            'I ~~still~~ used to smoke [cigars](/cigars)'
        );

        self::assertEquals(
            "## Header 2 ## {#header2}\n\n" .
            'I <del>still</del> used to smoke <a href="/cigars">cigars</a>',
            (string) $returnVal
        );
    }

    public function testMarkdownParseMarkdownExtra() : void
    {
        $returnVal = $this->markdownTwigExtension->markdownParse(
            "## Header 2 ## {#header2}\n\n" .
            'I ~~still~~ used to smoke [cigars](/cigars)',
            'extra'
        );

        self::assertEquals(
            "<h2 id=\"header2\">Header 2</h2>\n" .
            "<p>I ~~still~~ used to smoke <a href=\"/cigars\">cigars</a></p>\n",
            (string) $returnVal
        );
    }

    public function testMarkdownParseParagraphMarkdownExtra() : void
    {
        $returnVal = $this->markdownTwigExtension->markdownParseParagraph(
            "## Header 2 ## {#header2}\n\n" .
            'I ~~still~~ used to smoke [cigars](/cigars)',
            'extra'
        );

        self::assertEquals(
            "## Header 2 ## {#header2}\n\n" .
            'I ~~still~~ used to smoke <a href="/cigars">cigars</a>',
            (string) $returnVal
        );
    }

    public function testMarkdownParseMarkdown() : void
    {
        $returnVal = $this->markdownTwigExtension->markdownParse(
            "## Header 2 ## {#header2}\n\n" .
            'I ~~still~~ used to smoke [cigars](/cigars)',
            'markdown'
        );

        self::assertEquals(
            "<h2>Header 2 ## {#header2}</h2>\n" .
            "<p>I ~~still~~ used to smoke <a href=\"/cigars\">cigars</a></p>\n",
            (string) $returnVal
        );
    }

    public function testMarkdownParseParagraphMarkdown() : void
    {
        $returnVal = $this->markdownTwigExtension->markdownParseParagraph(
            "## Header 2 ## {#header2}\n\n" .
            'I ~~still~~ used to smoke [cigars](/cigars)',
            'markdown'
        );

        self::assertEquals(
            "## Header 2 ## {#header2}\n\n" .
            'I ~~still~~ used to smoke <a href="/cigars">cigars</a>',
            (string) $returnVal
        );
    }
}
