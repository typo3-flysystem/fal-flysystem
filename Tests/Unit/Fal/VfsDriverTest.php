<?php

namespace CedricZiel\FalFlysystem\Tests\Unit\Fal;

use CedricZiel\FalFlysystem\Fal\VfsDriver;
use PHPUnit_Framework_TestCase;

/**
 * Class VfsDriverTest
 * Tests the abstract FlysystemDriver through the VfsDriver
 * which maps closest to the LocalDriver.
 *
 * @package CedricZiel\FalFlysystem\Tests\Unit\Fal
 */
class VfsDriverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function itCanBeInstantiated()
    {
        $driver = new VfsDriver(['path' => '/']);
        $driver->initialize();

        $this->assertInstanceOf(VfsDriver::class, $driver);
    }

    /**
     * @test
     */
    public function itCanCheckIfAFileExists()
    {
        $driver = $this->getInitializedDriver();
        $this->assertFalse($driver->fileExists('/foo.txt'));

        $driver->getFilesystem()->put('/foo.txt', 'bar');
        $this->assertTrue($driver->fileExists('/foo.txt'));

        $driver->getFilesystem()->put('/bar/foo.txt', 'bar');
        $this->assertTrue($driver->fileExists('/bar/foo.txt'));
    }

    /**
     * @test
     */
    public function itCanListDirectoriesInTheRoot()
    {
        $driver = $this->getInitializedDriver();

        $emptyFileArray = $driver->getFoldersInFolder('/');
        $this->assertTrue(is_array($emptyFileArray));
        $this->assertCount(0, $emptyFileArray);

        $oneDirArray = $driver->getFoldersInFolder('/');
        $driver->getFilesystem()->put('/foo/bar.txt', 'baz');
        $this->assertTrue(is_array($oneDirArray));
        $this->assertCount(1, $oneDirArray);
    }

    /**
     * @test
     */
    public function itCanListFilesInTheRoot()
    {
        $driver = $this->getInitializedDriver();

        $emptyFileArray = $driver->getFilesInFolder('/');
        $this->assertTrue(is_array($emptyFileArray));
        $this->assertCount(0, $emptyFileArray);

        $driver->getFilesystem()->put('yo.txt', 'whazzup?');
        $oneFileArray = $driver->getFilesInFolder('/');
        $this->assertTrue(is_array($oneFileArray));
        $this->assertCount(1, $oneFileArray);

        $driver->getFilesystem()->put('yo1.txt', 'whazzup?');
        $twoFileArray = $driver->getFilesInFolder('/');
        $this->assertTrue(is_array($twoFileArray));
        $this->assertCount(2, $twoFileArray);
    }

    /**
     * @test
     */
    public function itCanCreateFolders()
    {
        $driver = $this->getInitializedDriver();

        $this->assertFalse($driver->folderExists('/test'));
        $driver->createFolder('/test');
        $this->assertTrue($driver->folderExists('/test'));

        $this->assertFalse($driver->folderExists('/test/test2'));
        $driver->createFolder('test2', '/test');
        $this->assertTrue($driver->folderExists('/test/test2'));
    }

    /**
     * @test
     */
    public function itCanRenameFolders()
    {
        $driver = $this->getInitializedDriver();

        $driver->createFolder('test');
        $this->assertTrue($driver->getFilesystem()->has('test'));

        $driver->renameFolder('test', 'test2');
        $this->assertTrue($driver->getFilesystem()->has('test2'));
    }

    /**
     * @return VfsDriver
     */
    private function getInitializedDriver()
    {
        $driver = new VfsDriver(['path' => '/']);
        $driver->initialize();

        return $driver;
    }
}
