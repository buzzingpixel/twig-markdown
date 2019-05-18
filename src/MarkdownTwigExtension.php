<?php

declare(strict_types=1);

namespace BuzzingPixel\TwigMarkdown;

use cebe\markdown\GithubMarkdown;
use cebe\markdown\Markdown;
use cebe\markdown\MarkdownExtra;
use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFilter;

class MarkdownTwigExtension extends AbstractExtension
{
    /** @var Markdown */
    private $markdown;
    /** @var GithubMarkdown */
    private $githubMarkdown;
    /** @var MarkdownExtra */
    private $markdownExtra;

    public function __construct(
        Markdown $markdown,
        GithubMarkdown $githubMarkdown,
        MarkdownExtra $markdownExtra
    ) {
        $this->markdown              = $markdown;
        $this->markdown->html5       = true;
        $this->githubMarkdown        = $githubMarkdown;
        $this->githubMarkdown->html5 = true;
        $this->markdownExtra         = $markdownExtra;
        $this->markdownExtra->html5  = true;
    }

    /**
     * @return TwigFilter[]
     */
    public function getFilters() : array
    {
        return [
            new TwigFilter('markdownParse', [$this, 'markdownParse']),
            new TwigFilter('markdownParseParagraph', [$this, 'markdownParseParagraph']),
        ];
    }

    public function markdownParse(string $str, string $flavor = 'github') : Markup
    {
        switch ($flavor) {
            case 'github':
                $processedStr = $this->githubMarkdown->parse($str);
                break;
            case 'extra':
                $processedStr = $this->markdownExtra->parse($str);
                break;
            default:
                $processedStr = $this->markdown->parse($str);
        }

        return new Markup($processedStr, 'UTF-8');
    }

    public function markdownParseParagraph(string $str, string $flavor = 'github') : Markup
    {
        switch ($flavor) {
            case 'github':
                $processedStr = $this->githubMarkdown->parseParagraph($str);
                break;
            case 'extra':
                $processedStr = $this->markdownExtra->parseParagraph($str);
                break;
            default:
                $processedStr = $this->markdown->parseParagraph($str);
        }

        return new Markup($processedStr, 'UTF-8');
    }
}
