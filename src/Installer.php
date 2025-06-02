<?php

declare(strict_types=1);

namespace Ysato\PhpSkeleton;

use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Script\Event;

class Installer
{
    /**
     * @var array
     */
    private static $packageName;

    public static function preInstall(Event $event): void
    {
        $io = $event->getIO();
        $vendorClass = self::ask($io, 'What is the vendor name ?', 'MyVendor');
        $packageClass = self::ask($io, 'What is the package name ?', 'MyPackage');
        $packageName = sprintf('%s/%s', self::camel2dashed($vendorClass), self::camel2dashed($packageClass));
        $json = new JsonFile(Factory::getComposerFile());
        $composerDefinition = self::getDefinition($vendorClass, $packageClass, $packageName, $json);
        self::$packageName = [$vendorClass, $packageClass];
        // Update composer definition
        $json->write($composerDefinition);
        $io->write("<info>composer.json for {$composerDefinition['name']} is created.\n</info>");
    }

    public static function postInstall(Event $event = null): void
    {
        unset($event);
        [$vendorName, $packageName] = self::$packageName;
        $skeletonRoot = dirname(__DIR__);
        self::recursiveJob($skeletonRoot, self::rename($vendorName, $packageName));
        //mv
        $skeletonPhp = __DIR__ . '/Skeleton.php';
        copy($skeletonPhp, "{$skeletonRoot}/src/{$packageName}.php");
        $skeletonTest = "{$skeletonRoot}/tests/SkeletonTest.php";
        copy($skeletonTest, "{$skeletonRoot}/tests/{$packageName}Test.php");
        // remove installer files
        unlink($skeletonRoot . '/readme.md');
        unlink($skeletonPhp);
        unlink($skeletonTest);
        unlink(__FILE__);
    }

    private static function ask(IOInterface $io, string $question, string $default): string
    {
        $ask = sprintf("\n<question>%s</question>\n(<comment>%s</comment>):", $question, $default);

        return $io->ask($ask, $default);
    }

    private static function recursiveJob(string $path, callable $job): void
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $file) {
            $job($file);
        }
    }

    private static function getDefinition(string $vendor, string $package, string $packageName, JsonFile $json): array
    {
        $composerDefinition = $json->read();
        unset(
            $composerDefinition['autoload']['psr-4'],
            $composerDefinition['scripts']['post-create-project-cmd'],
            $composerDefinition['scripts']['post-root-package-install'],
            $composerDefinition['keywords'],
            $composerDefinition['homepage'],
            $composerDefinition['license']
        );
        $composerDefinition['name'] = $packageName;
        $composerDefinition['description'] = '';
        $composerDefinition['license'] = 'proprietary';
        $composerDefinition['autoload']['psr-4'] = [
            "{$vendor}\\{$package}\\" => 'src/',
        ];

        return $composerDefinition;
    }

    private static function rename(string $vendor, string $package): \Closure
    {
        return function (\SplFileInfo $file) use ($vendor, $package) : void {
            $filename = $file->getFilename();
            $filePath = (string) $file;
            if ($file->isDir() || strpos($filename, '.') === 0 || ! is_writable($filePath)) {
                return;
            }
            $contents = file_get_contents($filePath);
            $contents = str_replace('__Vendor__', $vendor, $contents);
            $contents = str_replace('__Package__', $package, $contents);
            $contents = str_replace('__PackageVarName__', lcfirst($package), $contents);
            file_put_contents($filePath, $contents);
        };
    }

    private static function camel2dashed(string $name): string
    {
        return strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $name));
    }
}
