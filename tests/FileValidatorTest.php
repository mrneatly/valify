<?php

use valify\Validator;

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
            $txt .= 'Test text ';
        fwrite($testFile, $txt);
        fclose($testFile);

        $this->mimeType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $this->path);
        $this->size = filesize($this->path);
    }

    public function testIsFakeFileDataValid()
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

//        $validator = $this->validator->setRules([[array_keys($data), 'file']]);
//
//        $isValid = $validator->loadData($data)->validate();
//        $this->assertEquals(false, $isValid);


        $isValid = Validator::validateFor('file', $data)->isValid;

        $this->assertFalse($isValid);
    }

    public function testIsEmptyFileDataValid() {
        $data = [
            'testFile' => [
                'name' => '',
                'type' => '',
                'size' => '',
                'tmp_name' => '',
                'error' => 4, // Means that empty file uploaded
            ]
        ];

        # When using static method validateFor(),
        # the value is being checked for emptiness.
        # We allow empty value for this case
        # by setting 'allowEmpty to true just
        # to check that if all array keys exist
        # in file array, the file counts as valid
        $isValid = Validator::validateFor('file', $data, ['allowEmpty' => true])->isValid;

        $this->assertTrue($isValid);
    }

    public function testIsTooBigAmountOfFilesValid() {
        $data = [
            'testFile' => [
                'name' => ['testfile.txt', 'testfile.txt'],
                'type' => [$this->mimeType, $this->mimeType],
                'size' => [$this->size, $this->size],
                'tmp_name' => [$this->path, $this->path],
                'error' => [0, 0],
            ]
        ];

        # By default, only a single file is allowed to be uploaded.
        # So multiple file upload should not be valid.
        $isValid = Validator::validateFor('file', $data)->isValid;

        $this->assertFalse($isValid);
    }

    public function testIsTooBigSizeValid() {
        $data = [
            'testFile' => [
                'name' => 'testfile.txt',
                'type' => $this->mimeType,
                'size' => $this->size,
                'tmp_name' => $this->path,
                'error' => 0,
            ]
        ];

//        $validator = $this->validator->setRules([[array_keys($data), 'file', 'maxSize' => 900]]);
//
//        $isValid = $validator->loadData($data)->validate();
//        $this->assertEquals(false, $isValid);


        $isValid = Validator::validateFor('file', $data, ['maxSize' => 900])->isValid;

        $this->assertFalse($isValid);
    }

    public function testIsTooSmallSizeValid() {
        $data = [
            'testFile' => [
                'name' => 'testfile.txt',
                'type' => $this->mimeType,
                'size' => $this->size,
                'tmp_name' => $this->path,
                'error' => 0,
            ]
        ];

//        $validator = $this->validator->setRules([[array_keys($data), 'file', 'minSize' => 9000000]]);
//
//        $isValid = $validator->loadData($data)->validate();
//        $this->assertEquals(false, $isValid);

        $isValid = Validator::validateFor('file', $data, ['minSize' => 9000000])->isValid;

        $this->assertFalse($isValid);
    }

    public function testIsWrongExtensionValid() {
        $data = [
            'testFile' => [
                'name' => 'testfile.txt',
                'type' => $this->mimeType,
                'size' => $this->size,
                'tmp_name' => $this->path,
                'error' => 0,
            ]
        ];

//        $validator = $this->validator->setRules([[array_keys($data), 'file', 'extensions' => ['jpg'], 'checkExtensionByMimeType' => false]]);
//
//        $isValid = $validator->loadData($data)->validate();
//        $this->assertEquals(false, $isValid);


        $isValid = Validator::validateFor('file', $data, ['extensions' => ['jpg'], 'checkExtensionByMimeType' => false])->isValid;

        $this->assertFalse($isValid);
    }

    public function testIsWrongMimeTypeValid() {
        $data = [
            'testFile' => [
                'name' => 'testfile.jpg',
                'type' => $this->mimeType,
                'size' => $this->size,
                'tmp_name' => $this->path,
                'error' => 0,
            ]
        ];

        $isValid = Validator::validateFor('file', $data, ['extensions' => ['jpg']])->isValid;

        $this->assertFalse($isValid);
    }

    public function testIsProperFileValidationWorking() {
        $data = [
            'testFile' => [
                'name' => 'testfile.txt',
                'type' => $this->mimeType,
                'size' => $this->size,
                'tmp_name' => $this->path,
                'error' => 0,
            ]
        ];

        $isValid = Validator::validateFor('file', $data, ['extensions' => ['txt']])->isValid;

        $this->assertTrue($isValid);
    }

    public function tearDown()
    {
        unlink($this->path);
    }
}
