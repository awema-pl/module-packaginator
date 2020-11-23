<?php

namespace AwemaPL\Packaginator\Sections\Creators\Services;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Finder\Finder;
use ZanySoft\Zip\Zip;

class PackageCreatorService
{

    /** @var Finder $finder */
    protected $finder;

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * Build Zip package
     *
     * @param string $namePackage
     * @return string
     * @throws Exception
     */
    public function buildZipPackage(string $namePackage)
    {
        $packageSourceFiles = $this->packageSourceFiles();
        $dirTempName = $this->buildFilename($namePackage);
        $this->copyDirectories($namePackage, $packageSourceFiles, $dirTempName);
        $this->copyFiles($namePackage, $packageSourceFiles, $dirTempName);
        $this->buildZip($dirTempName);
        $this->sendToStorage($dirTempName);
        $this->deleteDirTempByName($dirTempName);
        return $dirTempName;
    }

    /**
     * Dir package source
     *
     * @return false|string
     */
    private function dirPackageSource()
    {
        return realpath(__DIR__ . '/../../../../');
    }

    /**
     * Package source files
     *
     * @return Finder
     */
    private function packageSourceFiles()
    {
        $dirPackageSource = $this->dirPackageSource();
        return $this->finder
            ->ignoreDotFiles(false)
            ->ignoreVCSIgnored(true)
            ->in($dirPackageSource);
    }

    /**
     * Build filename
     *
     * @param string $namePackage
     * @return string
     */
    private function buildFilename(string $namePackage)
    {
        return $namePackage . '-' . str_replace('-', '',mb_strtolower(Uuid::uuid4()));
    }

    /**
     * Copy directory
     *
     * @param $namePackage
     * @param string $dirTempName
     * @param string $relativePath
     */
    private function copyDirectory($namePackage, string $dirTempName, string $relativePath)
    {
        $dirTempPath = $this->dirTempPath($dirTempName);
        $relativePath = $this->replaceNamePackageWords($namePackage, $relativePath);
        $toPath = $dirTempPath . '/' . $relativePath;
        if (!File::exists($toPath)) {
            mkdir($toPath, 0777, true);
        }
    }

    /**
     * Copy file
     *
     * @param $namePackage
     * @param string $realPath
     * @param string $dirTempName
     * @param string $relativePath
     */
    private function copyFile($namePackage, string $realPath, string $dirTempName, string $relativePath)
    {
        $dirTempPath = $this->dirTempPath($dirTempName);
        $relativePath = $this->replaceNamePackageWords($namePackage, $relativePath);
        $content = File::get($realPath);
        $content = $this->replaceNamePackageWords($namePackage, $content);
        $toPath = $dirTempPath . '/' . $relativePath;
        File::put($toPath, $content);
    }

    /**
     * Copy directories
     * @param $namePackage
     * @param $packageSourceFiles
     * @param $dirTempName
     */
    private function copyDirectories($namePackage, $packageSourceFiles, $dirTempName)
    {
        foreach ($packageSourceFiles as $file) {
            $relativePath = $file->getRelativePathname();
            $realPath = $file->getRealPath();
            if (File::isDirectory($realPath)) {
                $this->copyDirectory($namePackage, $dirTempName, $relativePath);
            }
        }
    }

    /**
     * Copy files
     *
     * @param $namePackage
     * @param $packageSourceFiles
     * @param $dirTempName
     */
    private function copyFiles($namePackage, $packageSourceFiles, $dirTempName)
    {
        foreach ($packageSourceFiles as $file) {

            $relativePath = $file->getRelativePathname();
            $realPath = $file->getRealPath();
            if (File::isFile($realPath)) {
                $this->copyFile($namePackage, $realPath, $dirTempName, $relativePath);
            }
        }
    }

    /**
     * Replace name package words
     *
     * @param $namePackage
     * @param $content
     * @return string|string[]
     */
    private function replaceNamePackageWords($namePackage, $content)
    {
        return str_replace([
            'packaginator',
            'Packaginator',
            'PACKAGINATOR',
        ], [
            mb_strtolower($namePackage),
            Str::ucfirst(mb_strtolower($namePackage)),
            mb_strtoupper($namePackage),
        ], $content);
    }

    /**
     * Build ZIP path
     *
     * @param $dirTempName
     * @throws Exception
     */
    private function buildZip($dirTempName)
    {
        $dirTempPath = $this->dirTempPath($dirTempName);
        $zipPath = $dirTempPath . '.zip';
        $zip = Zip::create($zipPath);
        $zip->add($dirTempPath);
        $zip->close();
    }

    /**
     * Directory temporary path
     *
     * @param string $dirTempName
     * @return string
     */
    private function dirTempPath(string $dirTempName)
    {
        return storage_path('app/temp/packaginator/' . $dirTempName);
    }

    /**
     * Delete directory temporary by name
     *
     * @param string $dirTempName
     */
    private function deleteDirTempByName(string $dirTempName)
    {
        $dirTempPath = $this->dirTempPath($dirTempName);
        File::deleteDirectory($dirTempPath);
    }

    /**
     * Send to storage
     *
     * @param string $dirTempName
     */
    private function sendToStorage(string $dirTempName)
    {
        $zipTempPath = $this->dirTempPath($dirTempName) . '.zip';
        $zipPath = 'temp/packaginator/' . $dirTempName  . '.zip';
        if (!Storage::exists($zipPath)){
            $content = File::get($zipTempPath);
            Storage::put($zipPath, $content);
        }
    }
}
