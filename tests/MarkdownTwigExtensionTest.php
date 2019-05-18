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
}
