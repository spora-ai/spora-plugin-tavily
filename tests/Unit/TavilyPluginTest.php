<?php

declare(strict_types=1);

use Spora\Plugins\Tavily\TavilyPlugin;
use Spora\Plugins\Tavily\Tools\TavilySearchTool;

it('returns plugin name', function () {
    $plugin = new TavilyPlugin();
    expect($plugin->getName())->toBe('Tavily');
});

it('contributes the TavilySearchTool', function () {
    $plugin = new TavilyPlugin();
    expect($plugin->tools())->toBe([TavilySearchTool::class]);
});
