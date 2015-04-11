<?php

namespace valify\validators;

class FileValidator extends AbstractValidator {
    public $emptyFile = "File {name} is empty";

    /**
     * @var array|string a list of file name extensions that are allowed to be uploaded.
     * This can be either an array or a string consisting of file extension names
     * separated by space or comma (e.g. "gif, jpg").
     * Extension names are case-insensitive. Defaults to null, meaning all file name
     * extensions are allowed.
     * @see wrongType for the customized message for wrong file type.
     */
    public $extensions;

    /**
     * @var boolean whether to check file type (extension) with mime-type. If extension produced by
     * file mime-type check differs from uploaded file extension, the file will be considered as invalid.
     */
    public $checkExtensionByMimeType = true;

    /**
     * @var array|string a list of file MIME types that are allowed to be uploaded.
     * This can be either an array or a string consisting of file MIME types
     * separated by space or comma (e.g. "text/plain, image/png").
     * Mime type names are case-insensitive. Defaults to null, meaning all MIME types
     * are allowed.
     * @see wrongMimeType for the customized message for wrong MIME type.
     */
    public $mimeTypes;

    /**
     * @var integer the minimum number of bytes required for the uploaded file.
     * Defaults to null, meaning no limit.
     * @see tooSmall for the customized message for a file that is too small.
     */
    public $minSize;

    /**
     * @var integer the maximum number of bytes required for the uploaded file.
     * Defaults to null, meaning no limit.
     * Note, the size limit is also affected by 'upload_max_filesize' INI setting
     * and the 'MAX_FILE_SIZE' hidden field value.
     * @see tooBig for the customized message for a file that is too big.
     */
    public $maxSize;

    /**
     * @var integer the maximum file count the given attribute can hold.
     * It defaults to 1, meaning single file upload. By defining a higher number,
     * multiple uploads become possible.
     * @see tooMany for the customized message when too many files are uploaded.
     */
    public $maxFiles = 1;

    public $message = "Please upload a file";

    public $tooBig = "Size of the file '{name}' is bigger than required {maxSize} bytes";

    public $tooSmall = "Size of the file '{name}' is less than required {minSize} bytes";

    public $tooMany = "You can upload at most {limit} files, {number} uploaded";

    public $wrongExtension = "Only files with these extensions are allowed: {extensions}";

    public $wrongMimeType = "Only files with these MIME types are allowed: {mimeTypes}";

    public function init() {
        if ( !is_array($this->extensions) ) {
            $this->extensions = preg_split('/[\s,]+/', strtolower($this->extensions), -1, PREG_SPLIT_NO_EMPTY);
        } else {
            $this->extensions = array_map('strtolower', $this->extensions);
        }
        if ( !is_array($this->mimeTypes) ) {
            $this->mimeTypes = preg_split('/[\s,]+/', strtolower($this->mimeTypes), -1, PREG_SPLIT_NO_EMPTY);
        } else {
            $this->mimeTypes = array_map('strtolower', $this->mimeTypes);
        }

        $uploadMaxFileSize = $this->sizeToBytes(ini_get('upload_max_filesize'));

        if ( isset($_POST['MAX_FILE_SIZE']) && $_POST['MAX_FILE_SIZE'] > 0 && $_POST['MAX_FILE_SIZE'] < $uploadMaxFileSize )
            $uploadMaxFileSize = (int)$_POST['MAX_FILE_SIZE'];

        if( $this->maxSize == null || $this->maxSize > $uploadMaxFileSize )
            $this->maxSize = $uploadMaxFileSize;

        parent::init();
    }

    protected function validateValue($value) {
        $amountOfFiles = false;

        if ( !is_array($value) || !$this->isFile($value) ) {
            $this->addError($this->message);
        } else {
            $value = $this->normailzeData($value);
            $amountOfFiles = count($value);
        }


        if(!$amountOfFiles || $amountOfFiles > $this->maxFiles) {
            $this->addError($this->tooMany, ['{number}'=>$amountOfFiles, '{limit}'=>$this->maxFiles]);
        } else {
            foreach ($value as $file) {
                if($file['error'] == 4 && $this->allowEmpty)
                    continue;
                elseif($file['error'] !== UPLOAD_ERR_OK)
                    $this->addError( $this->errorCodeToMessage($file['error']) );
                elseif($file['size'] == 0)
                    $this->addError( $this->emptyFile, ['{name}'=>$file['name']] );
                elseif($this->maxSize && $file['size'] > $this->maxSize)
                    $this->addError( $this->tooBig, ['{name}'=>$file['name'], '{maxSize}'=>$this->maxSize] );
                elseif($this->minSize && $file['size'] < $this->minSize)
                    $this->addError( $this->tooSmall, ['{name}'=>$file['name'], '{minSize}'=>$this->minSize] );
                elseif( !empty($this->extensions) ) {
                    $extensions = $this->extensions;
                    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);

                    if($this->checkExtensionByMimeType) {
                        $mimeTypeExtensions = $this->getExtensionsByFileMimeType($file['tmp_name']);
                        if( !in_array($extension, $mimeTypeExtensions) )
                            $extension = null;
                    }

                    if( !in_array($extension, $extensions) )
                        $this->addError( $this->wrongExtension, ['{extensions}'=>implode(', ', $this->extensions)] );
                } elseif( !empty($this->mimeTypes) && !in_array($file['type'], $this->mimeTypes) )
                    $this->addError( $this->wrongMimeType, ['{mimeTypes}'=>implode(', ', $this->mimeTypes)] );
            }
        }
    }

    private function isFile($value) {
        return isset($value['name'], $value['type'], $value['tmp_name'], $value['error'], $value['size']);
    }

    private function sizeToBytes($str) {
        switch (substr($str, -1)) {
            case 'M':
            case 'm':
                return (int)$str * 1048576;
            case 'K':
            case 'k':
                return (int)$str * 1024;
            case 'G':
            case 'g':
                return (int)$str * 1073741824;
            default:
                return (int)$str;
        }
    }

    private function errorCodeToMessage($code) {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;

            default:
                $message = "Unknown upload error";
                break;
        }
        return $message;
    }

    public function getExtensionsByFileMimeType($file) {
        $mimeType = $this->getMimeTypeForFile($file);

        $out = [];
        $file = fopen('/etc/mime.types', 'r');
        while(($line = fgets($file)) !== false) {
            $line = trim(preg_replace('/#.*/', '', $line));
            if(!$line)
                continue;
            $parts = preg_split('/\s+/', $line);
            if(count($parts) == 1)
                continue;
            $type = array_shift($parts);
            if($type == $mimeType) {
                $out = is_array($parts) ? $parts : [$parts];
                break;
            }
        }
        fclose($file);

        return $out;
    }

    private function getMimeTypeForFile($file) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
        $fileMimeType = finfo_file($finfo, $file);
        finfo_close($finfo);

        return $fileMimeType;
    }

    private function normailzeData($value) {
        $normalizedValue = [];

        foreach ($value as $key => $val) {
            if(is_array($val)) {
                foreach ($val as $i => $name)
                    $normalizedValue[$i][$key] = $name;
            } else {
                $normalizedValue[0][$key] = $val;
            }
        }

        return $normalizedValue;
    }
}