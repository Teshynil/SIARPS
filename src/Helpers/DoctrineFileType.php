<?php

namespace App\Helpers;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

class DoctrineFileType extends Type {

    const FILE = 'file';

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform) {
        return $platform->getJsonTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        if ($value === null) {
            return null;
        }
        $encoded = json_encode($value, JSON_UNESCAPED_UNICODE);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw ConversionException::conversionFailedSerialization($value, 'json', json_last_error_msg());
        }

        return $encoded;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform) {

        if ($value === null || $value === '') {
            return null;
        }

        if (is_resource($value)) {
            $value = stream_get_contents($value);
        }

        $val = json_decode($value, TRUE);
        if ($val == null) {
            return null;
        }
        $path = $val["path"];
        $name = $val["name"];
        $mime = $val["mime"];
        $size = $val["size"];

        $file = new File($path, $name, $mime, $size);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return DoctrineFileType::FILE;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform) {
        return !$platform->hasNativeJsonType();
    }

}
