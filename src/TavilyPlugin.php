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

    /**
     * Translation catalogues this plugin ships.
     *
     * Each directory holds one `<lang>.json` per language. Spora resolves
     * a string through **your** catalogue first, then the host's, then the
     * English literal itself, so shipping a catalogue is purely additive:
     * nothing breaks while `de.json` is missing or partial.
     *
     * Keys are the English source text — `de.json` reads
     * `{"Tavily Search": "Tavily-Suche"}`, not `{"tool": "Tavily-Suche"}`.
     * That is what lets the host's CI fail when a string is edited without
     * being re-translated: the key changes, the old entry orphans.
     *
     * **Not everything English belongs in here.** A string the model reads
     * is never translated — not `#[Tool].description`, not
     * `#[ToolParameter].description`, not `#[ToolSetting].label` or its
     * `options`, and not `ToolResult` content. What `locales/de.json`
     * holds is every `#[ToolSetting].description` plus the tool's
     * `displayName`, each a string only an operator ever reads.
     *
     * Requires `spora-core >= 0.31.0` at runtime; older versions fail to
     * resolve the method when the loader is strict.
     *
     * @return string[]
     */
    public function translationsPaths(): array
    {
        return [
            __DIR__ . '/../locales',
        ];
    }
}
