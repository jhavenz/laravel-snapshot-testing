<?php

namespace Sourcefli\SnapshotTesting;

use Illuminate\Contracts\Support\DeferrableProvider;
use Sourcefli\SnapshotTesting\Contracts\ISnapshotConnection;
use Sourcefli\SnapshotTesting\Snapshots\Connection as SnapshotConnection;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SnapshotTestingServiceProvider extends PackageServiceProvider implements DeferrableProvider
{
	public function provides()
	{
		return [
			'snapshot-testing',
			ISnapshotConnection::class,
		];
	}

	public function configurePackage(Package $package): void
	{
		$package
			->name('laravel-snapshot-testing')
			->hasConfigFile('snapshot-testing');
	}

	public function packageRegistered()
	{
		$this->app->singleton('snapshot-testing', fn ($app) => new SnapshotTesting($app['config']));
		$this->app->singleton(ISnapshotConnection::class, fn ($app) => new SnapshotConnection($app['config'], $app['snapshot-testing']));
	}

	public function packageBooted()
	{
		config(['filesystems.disks.snapshot-testing' => config('snapshot-testing.disk.snapshot-testing')]);
	}
}
