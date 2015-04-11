<?php

namespace tests;

use valify\Validator;

require_once '../valify/Validator.php';

class FileValidatorTest extends \PHPUnit_Framework_TestCase {
    /* @var $validator \valify\Validator */
    private $validator;
    private $path;
    private $mimeType;
    private $size;

    function setUp() {
        $this->validator = new Validator();
        $this->path = __DIR__ . '/testfile.txt';

        $testFile = fopen($this->path, "w");
        $txt = '';
        for($i = 0; $i < 10000; $i++)
            $txt .= "Test text";
        fwrite($testFile, $txt);
        fclose($testFile);

        $this->mimeType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $this->path);
        $this->size = filesize($this->path);
    }

    public function testIsNotValidFile()
    {

        $data = [
            'testFile' => [
                'name' => 'testfile.txt',
                'type' => $this->mimeType,
//                'size' => $this->size, // Lets omit this line to make file data not valid
                'tmp_name' => $this->path,
                'error' => 0,
            ]
        ];

        $validator = $this->validator->setRules([[array_keys($data), 'file']]);

        $isValid = $validator->loadData($data)->validate();
        $this->assertEquals(false, $isValid);
    }

    public function testIsEmptyFileValid() {
        $data = [
            'testFile' => [
                'name' => '',
                'type' => '',
                'size' => '',
                'tmp_name' => '',
                'error' => 4, // Means that empty file uploaded
            ]
        ];

        $validator = $this->validator->setRules([[array_keys($data), 'file']]);

        $isValid = $validator->loadData($data)->validate();
        $this->assertEquals(true, $isValid);
    }

    public function testIsTooManyFiles() {
        $data = [
            'testFile' => [
                'name' => ['testfile.txt', 'testfile.txt'],
                'type' => [$this->mimeType, $this->mimeType],
                'size' => [$this->size, $this->size],
                'tmp_name' => [$this->path, $this->path],
                'error' => [0, 0],
            ]
        ];

        $validator = $this->validator->setRules([[array_keys($data), 'file']]);

        $isValid = $validator->loadData($data)->validate();
        $this->assertEquals(false, $isValid);
    }

    public function testIsSizeTooBig() {
        $data = [
            'testFile' => [
                'name' => 'testfile.txt',
                'type' => $this->mimeType,
                'size' => $this->size,
                'tmp_name' => $this->path,
                'error' => 0,
            ]
        ];

        $validator = $this->validator->setRules([[array_keys($data), 'file', 'maxSize' => 900]]);

        $isValid = $validator->loadData($data)->validate();
        $this->assertEquals(false, $isValid);
    }

    public function testIsSizeTooSmall() {
        $data = [
            'testFile' => [
                'name' => 'testfile.txt',
                'type' => $this->mimeType,
                'size' => $this->size,
                'tmp_name' => $this->path,
                'error' => 0,
            ]
        ];

        $validator = $this->validator->setRules([[array_keys($data), 'file', 'minSize' => 9000000]]);

        $isValid = $validator->loadData($data)->validate();
        $this->assertEquals(false, $isValid);
    }

    public function testIsWrongExtension() {
        $data = [
            'testFile' => [
                'name' => 'testfile.txt',
                'type' => $this->mimeType,
                'size' => $this->size,
                'tmp_name' => $this->path,
                'error' => 0,
            ]
        ];

        $validator = $this->validator->setRules([[array_keys($data), 'file', 'extensions' => ['jpg'], 'checkExtensionByMimeType' => false]]);

        $isValid = $validator->loadData($data)->validate();
        $this->assertEquals(false, $isValid);
    }

    public function testIsWrongMimeType() {
        $data = [
            'testFile' => [
                'name' => 'testfile.jpg',
                'type' => $this->mimeType,
                'size' => $this->size,
                'tmp_name' => $this->path,
                'error' => 0,
            ]
        ];

        $validator = $this->validator->setRules([[array_keys($data), 'file', 'extensions' => ['jpg']]]);

        $isValid = $validator->loadData($data)->validate();
        $this->assertEquals(false, $isValid);
    }

    public function tearDown()
    {
        unlink($this->path);
    }
}
