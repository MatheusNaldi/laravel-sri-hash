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
     * Return the html required for the SRI validation
     *
     * @param  string  $path
     * @param  boolean $useCredentials
     *
     * @return string
     */
    public function html($path, $useCredentials = false)
    {
        $integrity = $this->hash($path);
        $crossOrigin = $useCredentials ? 'use-credentials' : 'anonymous';

        return "integrity={$integrity} crossorigin={$crossOrigin}";
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
        if ($this->existsInConfigFile($path)) {
            return config('laravel-sri.hashes')[$path];
        }

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
     * Check if the path exists in the config file
     *
     * @param  string $path
     *
     * @return boolean
     */
    private function existsInConfigFile($path)
    {
        return array_key_exists($path, config('laravel-sri.hashes'));
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
