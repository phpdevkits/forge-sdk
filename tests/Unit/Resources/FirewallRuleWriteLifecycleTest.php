<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\CreateFirewallRuleData;
use PhpDevKits\ForgeSdk\Data\FirewallRule;
use PhpDevKits\ForgeSdk\Enums\RuleType;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Servers\CreateFirewallRule;
use PhpDevKits\ForgeSdk\Requests\Servers\DeleteFirewallRule;
use PhpDevKits\ForgeSdk\Requests\Servers\GetFirewallRule;
use PhpDevKits\ForgeSdk\Requests\Servers\GetFirewallRules;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\FirewallRuleFixture;
use Throwable;

/*
 * End-to-end firewall-rule lifecycle: create -> list/find -> get -> delete,
 * against the configured server (recording) or replayed from fixtures.
 *
 * The rule targets an unused high port (38080) and is located by that port,
 * so the server's default rules (SSH/HTTP/HTTPS) are never touched. POST and
 * DELETE are async (202, empty body), so the created rule is recovered via a
 * list-and-match. try/finally guarantees cleanup.
 *
 * Required env vars (recording only): FORGE_TEST_TOKEN, FORGE_TEST_ORG_SLUG,
 * FORGE_TEST_SERVER_ID.
 */
test('lifecycle: create -> list/find -> get -> delete',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';

        $recording = ! file_exists(__DIR__.'/../../Fixtures/Saloon/firewall-rules/get.json');

        $mockClient = new MockClient([
            CreateFirewallRule::class => new FirewallRuleFixture('firewall-rules/create'),
            GetFirewallRules::class => new FirewallRuleFixture('firewall-rules/lifecycle-list'),
            GetFirewallRule::class => new FirewallRuleFixture('firewall-rules/get'),
            DeleteFirewallRule::class => new FirewallRuleFixture('firewall-rules/delete'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $findByPort = static function (Forge $client) use ($serverId): ?FirewallRule {
            foreach ($client->server($serverId)->firewallRules()->all()->data as $rule) {
                if ($rule->port === '38080') {
                    return $rule;
                }
            }

            return null;
        };

        $createdId = null;
        try {
            $forge->server($serverId)->firewallRules()->create(
                new CreateFirewallRuleData(name: 'sdk-test-fw', type: RuleType::Allow, port: '38080'),
            );

            // POST is async 202 with no body; wait for the rule to register
            // (recording only — replay reads the committed list fixture).
            if ($recording) {
                $live = new Forge($token, $org);
                for ($attempt = 0; $attempt < 30; $attempt++) {
                    if ($findByPort($live) instanceof FirewallRule) {
                        break;
                    }

                    sleep(2);
                }
            }

            $rule = $findByPort($forge);
            expect($rule)->toBeInstanceOf(FirewallRule::class);

            /** @var FirewallRule $rule */
            $createdId = $rule->id;

            $fetched = $forge->server($serverId)->firewallRule($createdId)->get();
            expect($fetched)->toBeInstanceOf(FirewallRule::class)
                ->and($fetched->id)->toBeString()->not->toBeEmpty();

            $forge->server($serverId)->firewallRule($createdId)->delete();
            $createdId = null;
        } finally {
            if ($createdId !== null) {
                try {
                    $forge->server($serverId)->firewallRule($createdId)->delete();
                } catch (Throwable) {
                    // best-effort cleanup; original failure surfaces
                }
            }
        }
    });
