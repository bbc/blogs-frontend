#!/usr/bin/env php
<?php
declare(strict_types = 1);

$rootPath = dirname(__DIR__);

$vendorPath = $rootPath . join(DIRECTORY_SEPARATOR, ['', 'vendor', 'bbc', 'gel-iconography-assets', 'dist']);
$outputPath = $rootPath . join(DIRECTORY_SEPARATOR, ['', 'resources', 'gelicons']);

$update = new UpdateGelicons($vendorPath, $outputPath);
$update->fixAllSvgs();

class UpdateGelicons
{
    private const REGEX = '#dist/([^/]+)/individual/([^/]+).svg#';

    private $vendorPath;

    private $outputPath;

    public function __construct(string $vendorPath, string $outputPath)
    {
        $this->vendorPath = $vendorPath;
        $this->makeDir($outputPath);
        $this->deleteAllFilesUnder($outputPath);
        $this->outputPath = $outputPath;
    }

    public function fixAllSvgs()
    {
        $directoryIterator = new RecursiveDirectoryIterator($this->vendorPath);
        $iterator = new RecursiveIteratorIterator($directoryIterator);
        // Filter to */individual/*.svg
        $regex = new RegexIterator($iterator, self::REGEX);

        foreach ($regex as $path => $fileInfo) {
            $this->fixSvg($path);
        }
    }

    private function fixSvg(string $inputPath): void
    {
        $contents = file_get_contents($inputPath);
        if ($contents === false) {
            throw new InvalidArgumentException("$inputPath does not exist or is not readable");
        }
        // Get rid of width
        $contents = preg_replace('#^\s*(<svg[^>]*?) width="[0-9\.]+"([^>]*>)#i', '${1}${2}', $contents);
        // get rid of height
        $contents = preg_replace('#^\s*(<svg[^>]*?) height="[0-9\.]+"([^>]*>)#i', '${1}${2}', $contents);
        // Replace <svg> with <symbol id=
        list($set, $icon) = $this->getDetailsFromPath($inputPath);
        $id = "gelicon--$set--$icon";
        $contents = preg_replace('#^<svg #i', '<symbol id="' . $id . '" ', $contents);
        $contents = preg_replace('#</svg>\s*$#i', '</symbol>', $contents);
        $this->putFile($contents, $inputPath);
    }

    private function putFile(string $contents, string $inputPath): void
    {
        list($set, $icon) = $this->getDetailsFromPath($inputPath);
        $setPath = $this->outputPath . '/' . $set;
        $this->makeDir($setPath);
        $fullPath = $setPath . '/' . $icon . '.svg';
        if (!file_put_contents($fullPath, $contents)) {
            throw new RuntimeException("Cannot write $fullPath");
        }
    }

    private function makeDir(string $path): void
    {
        if (!is_dir($path)) {
            if (!mkdir($path, 0755)) {
                throw new RuntimeException("Cannot create $path");
            }
        }
    }

    private function getDetailsFromPath(string $path): array
    {
        $matches = [];
        if (preg_match(self::REGEX, $path, $matches)) {
            $set = preg_replace('/[^A-Za-z0-9_-]/', '', $matches[1]);
            $icon = preg_replace('/[^A-Za-z0-9_-]/', '', $matches[2]);
            $icon = preg_replace('/^gel-icon-/i', '', $icon);
            $icon = preg_replace('/\.svg$/i', '', $icon);
            return [$set, $icon];
        }
        throw new InvalidArgumentException("$path does not match our svg matching regex. Somehow");
    }

    /**
     * THIS RUNS rm -rf $dir. BE AFRAID. BE VERY AFRAID.
     */
    private function deleteAllFilesUnder(string $dir)
    {
        if (!is_dir($dir)) {
            throw new InvalidArgumentException("Cannot delete $dir as it is not a directory");
        }
        $directoryIterator = new RecursiveDirectoryIterator($dir, FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::SKIP_DOTS);
        $iterator = new RecursiveIteratorIterator($directoryIterator, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($iterator as $path => $fileInfo) {
            $success = true;
            if (is_dir($path)) {
                $success = rmdir($path);
            } elseif (is_file($path)) {
                $success = unlink($path);
            }
            if (!$success) {
                throw new RuntimeException("Cannot unlink $path");
            }
        }
    }
}
