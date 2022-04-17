<?php

namespace Sourcefli\SnapshotTesting;


use Carbon\CarbonInterface;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use ReflectionClass;
use Sourcefli\SnapshotTesting\Contracts\ISnapshotConnection;
use Sourcefli\SnapshotTesting\Contracts\ITimeTravelScenario;
use Sourcefli\SnapshotTesting\Exceptions\SnapshotTestingException;
use Sourcefli\SnapshotTesting\Scenarios\SnapshotScenario;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class SnapshotTesting
{
	use HasSnapshotConfig;

	/**
	 * @var ITimeTravelScenario[]
	 */
	protected array $timeTravelScenarios = [];

	public function __construct(
		protected Repository $config
	) {}

	public function getConnection(): ISnapshotConnection
	{
		return app(ISnapshotConnection::class);
	}

	public function getDisk(): Filesystem|FilesystemAdapter
	{
		return Storage::disk('snapshot-testing');
	}

	/** @return Collection<array-key, SplFileInfo> */
	public function collectScenarioContracts(): Collection
	{
		$files = Finder::create()->in(__DIR__.DIRECTORY_SEPARATOR.'Contracts')->files()->getIterator();

		return collect($files)
			->map(fn (SplFileInfo $fileInfo) => new ReflectionClass("Sourcefli\\SnapshotTesting\\Contracts\\".$fileInfo->getFilenameWithoutExtension()))
			->filter(fn (ReflectionClass $class) => count($class->getAttributes(SnapshotScenario::class)))
			->map(fn (ReflectionClass $class) => $class->getName());
	}

	/**
	 * @return ITimeTravelScenario[]
	 */
	public function getTimeTravelScenarios(): array
	{
		return $this->getConfig('scenarios.time_travelers');
	}

	public function usingScenario(string $scenario): SnapshotScenario
	{
		return match(TRUE) {
			$scenario instanceof ITimeTravelScenario => $this->whenCurrentDateIs($scenario->getTimeTravelDate()),
			default => throw SnapshotTestingException::unknownScenario($scenario)
		};
	}

	public function whenCurrentDateIs(CarbonInterface $currentDate): ITimeTravelScenario&SnapshotScenario
	{

	}
}
