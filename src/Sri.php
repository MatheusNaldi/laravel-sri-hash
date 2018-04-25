<?php

namespace Naldi\LaravelSri;

use Illuminate\Support\Facades\File;

class Sri
{
    /** @var string */
    private $algorithm;

    /**
     * Initialize with an available algorithm
     *
     * @param string $algorithm
     */
    public function __construct($algorithm)
    {
        $this->algorithm = in_array($algorithm, ['sha256', 'sha384', 'sha512'])
            ? $algorithm
            : 'sha256';
    }

    /**
     * Return the SRI hash for the given path
     *
     * @param  string $path
     *
     * @return string
     */
    public function hash($path)
    {
        $json = json_decode(file_get_contents($this->jsonFilePath()));
        $prefixedPath = starts_with($path, '/') ? $path : "/{$path}";

        if (array_key_exists($prefixedPath, $json)) {
            return $json->{$prefixedPath};
        }

        $hash = hash($this->algorithm, $this->getFileContent($path), true);
        $base64Hash = base64_encode($hash);

        return $this->algorithm . '-' . $base64Hash;
    }

    /**
     * Gets the file content on the run
     *
     * @param  string $path
     *
     * @return string
     */
    private function getFileContent($path)
    {
        if (starts_with($path, ['http', 'https', '//'])) {
            $fileContent = file_get_contents($path);
        } else {
            $path = starts_with($path, '/') ? $path : "/{$path}";
            $path = parse_url($path, PHP_URL_PATH);

            $fileContent = file_get_contents(config('laravel-sri.base_path') . $path);
        }

        if (!$fileContent) {
            throw new \Exception('file not found');
        }

        return $fileContent;
    }

    /**
     * Returns the default json filepath
     *
     * @return string
     */
    private function jsonFilePath()
    {
        return config('laravel-sri.sri_hashes_file');
    }
}
