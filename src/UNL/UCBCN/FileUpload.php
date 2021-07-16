<?php
namespace UNL\UCBCN;

class FileUpload
{
    private $imageInfo;
    private $mimeType;
    private $type;
    private $upload;
    private $errors = array();

    const MAX_SIZE_BYTES = 2097152;
    const TYPE_IMAGE = 'TYPE_IMAGE';

    public function __construct($uploadIdentifier, $type = self::TYPE_IMAGE)
    {
        if (isset($_FILES[$uploadIdentifier])) {
            $this->upload = $_FILES[$uploadIdentifier];
            $this->type = $type;

            $this->mimeType = $this->upload['type'];

            if ($this->type === self::TYPE_IMAGE) {
                $this->imageInfo = @getimagesize($this->upload['tmp_name']);
            }
        }
    }

    public function isValid() {
        $this->validate();
        return !$this->hasErrors();
    }

    public function getValidationErrors() {
        return $this->errors;
    }

    public function getPath() {
        return $this->upload["tmp_name"];
    }

    public function getType() {
        return $this->upload["type"];
    }

    public function compressImage() {
        $source = $this->upload['tmp_name'];
        $mime = isset($this->imageInfo['mime']) ? $this->imageInfo['mime'] : NULL;

        if ($mime == 'image/jpeg') {
            $image = imagecreatefromjpeg($source);
            imagejpeg($image, $source, 90);
        } elseif ($mime == 'image/gif') {
            $image = imagecreatefromgif($source);
            imagegif($image, $source);
        } elseif ($mime == 'image/png') {
            $image = imagecreatefrompng($source);
            imagepng($image, $source, 9, PNG_NO_FILTER);
        } else {
            return; // invalid mime, bail
        }

        imagedestroy($image);
    }

    private function validate() {
        $this->errors = array();
        $this->validateFile();

        if (!$this->hasErrors() && $this->type === self::TYPE_IMAGE) {
            $this->validateImage();
        }
    }

    private function hasErrors() {
        return count($this->errors) > 0;
    }

    private function allowedMimeTypes() {
        if ($this->type ===  self::TYPE_IMAGE) {
            return array('image/gif', 'image/jpeg', 'image/jpg', 'image/png');
        }
    }

    private function validateFile() {
        if (!file_exists($this->upload['tmp_name'])) {
            $this->errors[] = 'The upload file is missing.';
        } else {
            if (!in_array($this->mimeType, $this->allowedMimeTypes())) {
                $this->errors[] = 'Invalid upload mime type (' . $this->mimeType . '), limited to ' . implode(', ', $this->allowedMimeTypes()) . '.';
            } elseif ($this->upload["size"] > self::MAX_SIZE_BYTES) {
                $this->errors[] = 'The upload file (' . $this->formatBytes($this->upload["size"]) . ') exceeds ' . $this->formatBytes(self::MAX_SIZE_BYTES);
            }
        }
    }

    private function validateImage() {
        $width =  isset($this->imageInfo[0]) ? intval($this->imageInfo[0]) : -1;
        $height = isset($this->imageInfo[1]) ? intval( $this->imageInfo[1]) : -1;
        if ($width === -1 || $height === -1) {
            return;  // size check failed so bail
        }
        if ($width != $height) {
            $this->errors[] = 'Image must have a 1:1 ratio (square) where the image width and height are equal.';
        }
    }

    private function formatBytes($size, $precision = 2) {
        $base = log($size, 1024);
        $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
        return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
    }
}
