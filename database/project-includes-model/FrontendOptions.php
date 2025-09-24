<?php

    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/FrontendOptionsGen.php');

    /**
     * The FrontendOptions class defined here contains any
     * customized code for the FrontendOptions class in the
     * Object Relational Model. It represents the "frontend_options" table
     * in the database and extends from the code generated abstract FrontendOptionsGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class FrontendOptions extends FrontendOptionsGen
    {
        /**
         * Default "to string" handler
         * Allows pages to _p()/echo()/print() this object, and to define the default
         * way this object would be outputted.
         *
         * @return string a nicely formatted string representation of this object
         */
        public function __toString(): string
        {
            return t($this->FrontendTemplateName);
        }

        /**
         * Loads a FrontendOptions object by its ContentTypesManagementId.
         * Retrieves a single FrontendOptions object based on the provided ID and optional clauses.
         *
         * @param int $intId The ID to locate the FrontendOptions object by (ContentTypesManagementId).
         * @param mixed $objOptionalClauses Optional query clauses to customize the query.
         *
         * @return FrontendOptions|null The FrontendOptions object if found, or null if not found.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function loadByIdFromId(int $intId, mixed $objOptionalClauses = null): ?FrontendOptions
        {
            // Use QuerySingle to Perform the Query
            return FrontendOptions::querySingle(
                QQ::AndCondition(
                    QQ::Equal(QQN::FrontendOptions()->ContentTypesManagementId, $intId)
                ), $objOptionalClauses
            );
        }

        /**
         * Checks if the given frontend template name exists in the database.
         *
         * @param string $frontendTemplateName The name of the frontend template to check for existence.
         *
         * @return bool True if the frontend template name exists, false otherwise.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function frontendTemplateNameExists(string $frontendTemplateName): bool
        {
            $objCondition = QQ::Equal(QQN::FrontendOptions()->FrontendTemplateName, $frontendTemplateName);
            $objChangesArray = FrontendOptions::queryArray($objCondition);

            return count($objChangesArray) > 0;
        }

        /**
         * Checks if a template file with the given path exists in the file system.
         *
         * The compiled path is in the form: $templateRoot. '/' . $contentTypeFolder. '/' . $templateFile
         *
         * @param null|string $templateRoot Full path to the root/base folder (e.g. FRONTEND_DIR)
         * @param null|string $contentTypeFolder Subfolder where the template file is expected (e.g. content type folder name)
         * @param null|string $templateFile Name of the template file in the path (without the folder; e.g. 'view.php')
         *
         * @return bool Returns true if such a file exists, false if not found or some part is missing
         */
        public static function templateFileExists(?string $templateRoot, ?string $contentTypeFolder, ?string $templateFile): bool
        {
            if (!$templateRoot || !$contentTypeFolder || !$templateFile) {
                return false;
            }

            $fullPath = rtrim($templateRoot, DIRECTORY_SEPARATOR)
                . DIRECTORY_SEPARATOR . trim($contentTypeFolder, DIRECTORY_SEPARATOR)
                . DIRECTORY_SEPARATOR . ltrim($templateFile, DIRECTORY_SEPARATOR);

            return file_exists($fullPath);
        }

        // NOTE: Remember that when introducing a new custom function,
        // you must specify types for the function parameters as well as for the function return type!

        // Override or Create New load/count methods
        // (For obvious reasons, these methods are commented out...
        // But feel free to use these as a starting point)
        /*

            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return an array of FrontendOptions objects
                return FrontendOptions::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::FrontendOptions()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::FrontendOptions()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single FrontendOptions object
                return FrontendOptions::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::FrontendOptions()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::FrontendOptions()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of FrontendOptions objects
                return FrontendOptions::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::FrontendOptions()->Param1, $strParam1),
                        QQ::Equal(QQN::FrontendOptions()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = FrontendOptions::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `frontend_options`.*
                    FROM
                        `frontend_options` AS `frontend_options`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return FrontendOptions::instantiateDbResult($objDbResult);
            }
        */

        // Override or Create New Properties and Variables
        // For performance reasons, these variables and __set and __get override methods
        // are commented out.  But if you wish to implement or override any
        // of the data-generated properties, please feel free to uncomment them.
        /*
            protected $strSomeNewProperty;

            protected function __set(string $strName, mixed $mixValue): void
            {
                switch ($strName) {
                    case 'SomeNewProperty': return $this->strSomeNewProperty;

                    default:
                        try {
                            return parent::__get($strName);
                        } catch (Caller $objExc) {
                            $objExc->incrementOffset();
                            throw $objExc;
                        }
                }
            }

            public function __set(string $strName, mixed $mixValue): void
            {
                switch ($strName) {
                    case 'SomeNewProperty':
                        try {
                            return ($this->strSomeNewProperty = \QCubed\Type::Cast($mixValue, \QCubed\Type::String));
                        } catch (QInvalidCastException $objExc) {
                            $objExc->incrementOffset();
                            throw $objExc;
                        }

                    default:
                        try {
                            return (parent::__set($strName, $mixValue));
                        } catch (Caller $objExc) {
                            $objExc->incrementOffset();
                            throw $objExc;
                        }
                }
            }
        */

    }
