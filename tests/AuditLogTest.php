<?php

declare(strict_types=1);

namespace FlexMindSoftware\CurrencyRate\Tests;

use FlexMindSoftware\CurrencyRate\Actions\StoreRate;
use FlexMindSoftware\CurrencyRate\Models\CurrencyRate;

class AuditLogTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        file_put_contents(__DIR__.'/../database/database.sqlite', '');
        $currencyMigration = include __DIR__.'/../database/migrations/create_currency_rate_table.php.stub';
        $currencyMigration->up();
        $auditMigration = include __DIR__.'/../database/migrations/create_audit_logs_table.php.stub';
        $auditMigration->up();
    }

    /** @test */
    public function it_logs_rate_changes()
    {
        CurrencyRate::create([
            'driver' => 'test',
            'code' => 'USD',
            'date' => now()->toDateString(),
            'rate' => 1.0,
            'multiplier' => 1,
            'no' => null,
        ]);

        (new StoreRate())->execute('USD', 2.5);

        $this->assertDatabaseHas('audit_logs', [
            'currency_code' => 'USD',
            'old_rate' => 1.0,
            'new_rate' => 2.5,
        ]);
    }
}
