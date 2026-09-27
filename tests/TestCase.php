<?php

namespace Tests;

use Closure;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use LogicException;

abstract class TestCase extends BaseTestCase
{
    protected bool $migrateAllTables = false;

    protected string $testDatabaseName;

    protected function setUp(): void
    {
        parent::setUp();

        if ($this->migrateAllTables) {
            $this->artisan('migrate', ['--database' => 'mysql', '--no-interaction' => true])->assertExitCode(0);
        }
    }

    protected function setUpTraits(): array
    {
        $cleanup = $this->useIsolatedMysqlDatabase();
        RefreshDatabaseState::$migrated = false;
        try {
            $traits = parent::setUpTraits();
        } catch (\Throwable $exception) {
            $cleanup();
            throw $exception;
        }
        $this->beforeApplicationDestroyed($cleanup);

        return $traits;
    }

    private function useIsolatedMysqlDatabase(): Closure
    {
        if (! $this->app->environment('testing')) {
            throw new LogicException('Database pengujian hanya boleh dibuat dalam APP_ENV=testing.');
        }

        $connection = DB::connection('mysql')->getConfig();
        if ($connection['driver'] !== 'mysql') {
            throw new LogicException('Tes aplikasi memerlukan koneksi MySQL.');
        }

        $databaseName = 'portofolio_test_'.bin2hex(random_bytes(8));
        $applicationDatabase = $connection['database'];
        config(['database.connections.test_mysql_admin' => array_replace($connection, [
            'name' => 'test_mysql_admin', 'database' => null, 'url' => null,
        ])]);
        $admin = DB::connection('test_mysql_admin');
        $admin->getSchemaBuilder()->createDatabase($databaseName);
        $this->testDatabaseName = $databaseName;

        $cleanup = function () use ($admin, $databaseName, $applicationDatabase): void {
            if (! preg_match('/^portofolio_test_[a-f0-9]{16}$/', $databaseName) || $databaseName === $applicationDatabase) {
                throw new LogicException('Menolak menghapus database di luar pengujian ini.');
            }

            DB::purge('mysql');
            $admin->getSchemaBuilder()->dropDatabaseIfExists($databaseName);
            DB::purge('test_mysql_admin');
        };

        config([
            'database.default' => 'mysql',
            'database.connections' => [
                'mysql' => array_replace($connection, ['name' => 'mysql', 'database' => $databaseName, 'url' => null]),
                'test_mysql_admin' => $admin->getConfig(),
            ],
        ]);
        DB::purge('mysql');
        Schema::clearResolvedInstance('db.schema');

        if (DB::connection()->selectOne('SELECT DATABASE() AS name')->name !== $databaseName
            || Schema::getConnection()->getDatabaseName() !== $databaseName
            || Schema::getConnection()->getName() !== 'mysql') {
            throw new LogicException('Koneksi migrasi tidak mengarah ke database MySQL pengujian.');
        }

        return $cleanup;
    }
}
