<?php

declare(strict_types=1);

use BuzzingPixel\TwigMarkdown\MarkdownTwigExtension;
use cebe\markdown\GithubMarkdown;
use cebe\markdown\Markdown;
use cebe\markdown\MarkdownExtra;

return [
    MarkdownTwigExtension::class => static function () {
        return new MarkdownTwigExtension(
            new Markdown(),
            new GithubMarkdown(),
            new MarkdownExtra()
        );
    },
];
