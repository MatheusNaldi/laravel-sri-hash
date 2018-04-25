<?php

namespace Naldi\LaravelSri\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class GenerateSriHashes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel-sri:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the SRI hashes for the specified files in configs';

    /**
     * @var Filesystem
     */
    private $file;

    public function __construct(Filesystem $file)
    {
        parent::__construct();

        $this->file = $file;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Starting SRI hashes generator');

        $searchFileExts = config('laravel-sri.sri_generate.search_file_ext');
        $algorithm = config('laravel-sri.algorithm');

        $hashes = [];
        foreach (config('laravel-sri.sri_generate.folders') as $folder) {
            foreach ($this->file->allfiles($folder) as $file) {
                if (!in_array($file->getExtension(), $searchFileExts)) {
                    continue;
                }

                $hash = hash_file($algorithm, $file, true);
                $base64Hash = base64_encode($hash);

                $filename = '/' . $file->getRelativePathname();

                $hashes[$filename] = $base64Hash;
            }
        }

        $hashesJson = json_encode($hashes, JSON_UNESCAPED_SLASHES);

        $this->file->put(config('laravel-sri.sri_hashes_file'), $hashesJson);

        $this->info('SRI hashes generator finished successfully');
    }
}
