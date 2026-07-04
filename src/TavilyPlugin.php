<?php

declare(strict_types=1);

namespace Spora\Plugins\Tavily;

use Spora\Plugins\AbstractPlugin;
use Spora\Plugins\Tavily\Tools\TavilySearchTool;

/**
 * Tavily web search for Spora agents.
 */
final class TavilyPlugin extends AbstractPlugin
{
    public function getName(): string
    {
        return 'Tavily';
    }

    /** @return array<class-string<\Spora\Tools\ToolInterface>> */
    public function tools(): array
    {
        return [
            TavilySearchTool::class,
        ];
    }
}
