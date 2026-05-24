<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\CreateRedirectRuleData;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Data\RedirectRule;
use PhpDevKits\ForgeSdk\Enums\RedirectType;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Sites\CreateRedirectRule;
use PhpDevKits\ForgeSdk\Requests\Sites\DeleteRedirectRule;
use PhpDevKits\ForgeSdk\Requests\Sites\GetRedirectRule;
use PhpDevKits\ForgeSdk\Requests\Sites\GetRedirectRules;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\RedirectRuleFixture;
use Throwable;

/*
 * End-to-end redirect-rule lifecycle: create -> list/find -> iterate -> get ->
 * delete, against the configured site (recording) or replayed from fixtures.
 *
 * The rule redirects an unused path and is located by its `from`, then removed.
 * POST/DELETE are async (202, empty body), so the created rule is recovered via
 * a list-and-match. try/finally guarantees cleanup.
 *
 * Required env vars (recording only): FORGE_TEST_TOKEN, FORGE_TEST_ORG_SLUG,
 * FORGE_TEST_SERVER_ID, FORGE_TEST_SITE_ID.
 */
test('lifecycle: create -> list/find -> iterate -> get -> delete',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';
        $siteId = ($_ENV['FORGE_TEST_SITE_ID'] ?? '') ?: '1';

        $recording = ! file_exists(__DIR__.'/../../Fixtures/Saloon/redirect-rules/get.json');

        $mockClient = new MockClient([
            CreateRedirectRule::class => new RedirectRuleFixture('redirect-rules/create'),
            GetRedirectRules::class => new RedirectRuleFixture('redirect-rules/list'),
            GetRedirectRule::class => new RedirectRuleFixture('redirect-rules/get'),
            DeleteRedirectRule::class => new RedirectRuleFixture('redirect-rules/delete'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $findByFrom = static function (Forge $client) use ($serverId, $siteId): ?RedirectRule {
            foreach ($client->server($serverId)->site($siteId)->redirectRules()->all()->data as $rule) {
                if ($rule->from === '/sdk-test-redirect') {
                    return $rule;
                }
            }

            return null;
        };

        $createdId = null;
        try {
            $forge->server($serverId)->site($siteId)->redirectRules()->create(
                new CreateRedirectRuleData(from: '/sdk-test-redirect', to: 'https://example.com', type: RedirectType::Redirect),
            );

            // POST is async 202 with no body; wait for the rule to register
            // (recording only — replay reads the committed list fixture).
            if ($recording) {
                $live = new Forge($token, $org);
                for ($attempt = 0; $attempt < 30; $attempt++) {
                    if ($findByFrom($live) instanceof RedirectRule) {
                        break;
                    }

                    sleep(2);
                }
            }

            $page = $forge->server($serverId)->site($siteId)->redirectRules()->all();
            expect($page)->toBeInstanceOf(Page::class)
                ->and($page->data)->not->toBeEmpty();

            $rule = null;
            foreach ($page->data as $candidate) {
                if ($candidate->from === '/sdk-test-redirect') {
                    $rule = $candidate;
                }
            }
            expect($rule)->toBeInstanceOf(RedirectRule::class);

            /** @var RedirectRule $rule */
            $createdId = $rule->id;

            $iterated = iterator_to_array($forge->server($serverId)->site($siteId)->redirectRules()->iterate());
            expect($iterated)->not->toBeEmpty();

            $fetched = $forge->server($serverId)->site($siteId)->redirectRule($createdId)->get();
            expect($fetched)->toBeInstanceOf(RedirectRule::class);

            $forge->server($serverId)->site($siteId)->redirectRule($createdId)->delete();
            $createdId = null;
        } finally {
            if ($createdId !== null) {
                try {
                    $forge->server($serverId)->site($siteId)->redirectRule($createdId)->delete();
                } catch (Throwable) {
                    // best-effort cleanup; original failure surfaces
                }
            }
        }
    });
