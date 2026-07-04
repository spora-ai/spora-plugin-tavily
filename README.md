# Tavily Plugin for Spora

Adds [Tavily](https://tavily.com)'s AI-native web search to
[Spora](https://github.com/spora-ai/spora) agents — search the web and
receive an LLM-optimised answer plus ranked source results in one tool
call. Tavily is a paid API; a free tier with **1,000 credits/month** is
available.

Sign up and get an API key at <https://tavily.com> (no credit card
required for the free tier).

## Installation

```bash
# Recommended — install via the Spora CLI
php bin/spora plugin:install spora-ai/spora-plugin-tavily
php bin/spora spora:install   # applies the plugin's migration

# For development against a sibling git clone, pass --path:
php bin/spora plugin:install spora-ai/spora-plugin-tavily --path=/abs/path/to/checkout

# Alternative — drop a clone into the Spora repo
git clone https://github.com/spora-ai/spora-plugin-tavily.git plugins/tavily
php bin/spora spora:install

# Alternative — external path (no Spora checkout changes)
git clone https://github.com/spora-ai/spora-plugin-tavily.git /opt/spora-plugins/tavily
echo 'SPORA_PLUGINS_PATHS=/opt/spora-plugins/tavily' >> .env
php bin/spora spora:install
```

After install, the tool is exposed as `tavily_search` (visible in
`php bin/spora plugin:list` and the agent UI under Tools).

## Configuration

Settings → Tools → Tavily Search. Authentication uses a Bearer token
against `https://api.tavily.com`.

| Setting | Required | Default |
|---|---|---|
| `core.tavily.api_key` | yes | — |
| `core.tavily.http_timeout` | no | `30` (seconds; overridden by `SPORA_TOOL_HTTP_TIMEOUT` env var) |

`api_key` is encrypted at rest by Spora's `ToolConfigService`, masked in
the UI, and never logged (the `api_key` field in outbound payload logs
is redacted as `***`).

## Per-tool parameters

The plugin ships **one** tool, `tavily_search`. It calls Tavily's
`POST https://api.tavily.com/search` endpoint and returns
`ToolResult::ok` (formatted text) or `ToolResult::fail`. Never throws
— a single API failure cannot kill the agent loop.

| Parameter | Type | Required | Default | Notes |
|---|---|---|---|---|
| `query` | string | yes | — | The exact research question or search query. |
| `search_depth` | string | no | `basic` | `basic` or `advanced`. Advanced takes longer but traverses deeper (1 credit vs 2 credits per call). |

### What it returns

On success, the tool returns a single text block:

```
Search Results for '<query>':

Summary: <LLM-generated answer, if include_answer is true>

[1] <result title>
URL: <result url>
<result content snippet>

[2] ...
```

Internally the tool sends:

```json
{
  "api_key": "<your key>",
  "query": "<query>",
  "search_depth": "basic",
  "include_answer": true
}
```

to `https://api.tavily.com/search`. The full Tavily `/search` parameter
set (`topic`, `max_results`, `include_raw_content`, `time_range`,
`start_date`, `end_date`, `include_domains`, `exclude_domains`,
`country`, `include_images`, `auto_parameters`, `exact_match`,
`safe_search`, …) is documented at
<https://docs.tavily.com/documentation/api-reference/endpoint/search>.
Exposing them as agent-callable arguments is out of scope for v1; if you
need a knob, open an issue.

## Tavily account, pricing, and limits

| Tier | Price | Credits / month |
|---|---|---|
| **Researcher** (free) | $0 | 1,000 — no credit card required |
| **Pay As You Go** | $0.008 / credit | metered |
| **Project** | starts at 4,000 credits/mo | higher rate limits |
| **Enterprise** | custom | custom API calls, SLAs |

Credit cost: a `basic` `/search` call costs **1 credit**, `advanced`
costs **2 credits**. Plan / rate-limit errors come back as HTTP
`429` / `432` / `433` and surface as `ToolResult::fail` with the status
code embedded.

- Sign up: <https://tavily.com> → "Try it for free"
- API key: <https://app.tavily.com/home>
- Pricing: <https://tavily.com/pricing>
- API reference: <https://docs.tavily.com>
- Status page / support: <https://docs.tavily.com>

## Development

```bash
composer install
./vendor/bin/pest           # 4 tests
./vendor/bin/phpstan analyse --no-progress
./vendor/bin/php-cs-fixer fix --dry-run --diff --no-interaction
```

CI: `.github/workflows/ci.yml` — Pest on PHP 8.4 + 8.5, PHPStan level
(per `phpstan.neon`), php-cs-fixer dry-run. A separate `coverage` job
runs Pest with `pcov` and uploads `coverage.xml` + JUnit; the `sonar`
job uploads both to SonarCloud (project key
`spora-ai_spora-plugin-tavily`), so the `new_coverage` metric is
measurable per PR. Requires the `SONAR_TOKEN` secret in the repo.

## License

MIT.