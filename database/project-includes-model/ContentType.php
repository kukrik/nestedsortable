<?php

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/ContentTypeGen.php');

    /**
     * The ContentType class defined here contains any
     * customized code for the ContentType enumerated type.
     *
     * It represents the enumerated values found in the "content_type" table in the database
     * and extends from the code generated abstract ContentTypeGen
     * class, which contains all the values extracted from the database.
     *
     * Type classes which are generally used to attach a type to a data object.
     * However, they may be used as simple database independent enumerated type.
     *
     * @package My QCubed Application
     * @subpackage DataObjects
     */
    abstract class ContentType extends ContentTypeGen
    {
        /**
         * Returns TRUE if the given content type ID is a standard content type (ID 1–17), otherwise FALSE.
         */
        public static function isStandardContentType(int $contentTypeId): bool
        {
            return $contentTypeId >= 1 && $contentTypeId <= 17;
        }
    }