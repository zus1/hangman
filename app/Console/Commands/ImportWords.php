<?php

namespace App\Console\Commands;

use App\Models\Language;
use App\Repository\LanguageRepository;
use App\Repository\WordRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ImportWords extends Command
{
    private const FLUSH_COUNT = 1000;

    private Language $language;

    public function __construct(
        private WordRepository $repository,
        private LanguageRepository $languageRepository,
    )
    {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-words {language}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports words to database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->setLanguage();
        $fh = $this->loadFile();

        $collection = new Collection();
        while (($word = fgets($fh)) !== false) {
            $word = trim($word);
            if($word === '') {
                continue;
            }

            $wordObj = $this->repository->make($word, $this->language);
            $collection->add($wordObj);

            $this->info('Processed word '.$word);

            $this->flushIfPossible($collection);
        }

        $this->flush($collection);

        return 0;
    }

    private function loadFile()
    {
        $file = resource_path('/words/'.sprintf('words_%s.csv', $this->language->short));

        if(!file_exists($file)) {
            $this->terminate('File not exist .'. $file);
        }

        return fopen($file, 'r');
    }

    private function flushIfPossible(Collection $collection): void
    {
        if($collection->count() === self::FLUSH_COUNT) {
            $this->repository->insertIfNotExists($collection);
            $collection->forget($collection->keys());
        }
    }

    private function flush(Collection $collection): void
    {
        if($collection->isNotEmpty()) {
            $this->repository->insertIfNotExists($collection);
        }
    }

    private function setLanguage(): void
    {
        $language = $this->argument('language');

        try {
            /** @var Language $languageObject */
            $languageObject = $this->languageRepository->findOneByOr404(['short' => $language]);
        } catch (HttpException $e) {
            $this->terminate($e->getMessage());
        }


        $this->language = $languageObject;
    }
    #[NoReturn]
    private function terminate(string $message): void
    {
        $this->error($message);
        $this->error('Terminated');

        exit();
    }
}
