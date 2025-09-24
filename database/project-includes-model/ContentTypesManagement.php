<?php

    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/ContentTypesManagementGen.php');

    /**
     * The ContentTypesManagement class defined here contains any
     * customized code for the ContentTypesManagement class in the
     * Object Relational Model. It represents the "content_types_management" table
     * in the database and extends from the code generated abstract ContentTypesManagementGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class ContentTypesManagement extends ContentTypesManagementGen
    {
        /**
         * Default "to string" handler
         * Allows pages to _p()/echo()/print() this object, and to define the default
         * way this object would be outputted.
         *
         * @return string a nicely formatted string representation of this object
         * @throws Caller
         */
        public function __toString(): string
        {
            return $this->getContentName();
        }

        /**
         * Checks if a content name exists in the ContentTypesManagement table.
         *
         * @param string $contentName The content name to check for existence.
         *
         * @return bool True if the content name exists, otherwise false.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function contentNameExists(string $contentName): bool
        {
            $objCondition = QQ::Equal(QQN::ContentTypesManagement()->ContentName, $contentName);
            $objChangesArray = ContentTypesManagement::queryArray($objCondition);

            return count($objChangesArray) > 0;
        }

        /**
         * Checks if a pair of content type and view type exists in the ContentTypesManagement table.
         *
         * @param int $contentType The content type identifier to check.
         * @param int $viewType The view type identifier to check.
         *
         * @return bool True if the pair exists, false otherwise.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function pairExists(int $contentType, int $viewType): bool
        {
            $objCondition = QQ::andCondition(
                QQ::equal(QQN::ContentTypesManagement()->ContentType, $contentType),
                QQ::equal(QQN::ContentTypesManagement()->ViewType, $viewType)
            );

            return ContentTypesManagement::queryCount($objCondition) > 0;
        }

        /**
         * Loads a ContentTypesManagement object by its ID.
         * Performs a database query to retrieve a single ContentTypesManagement object
         * based on the provided ID and optional query clauses.
         *
         * @param null|int $intId The ID of the ContentTypesManagement to be loaded.
         * @param mixed|null $objOptionalClauses Optional clauses to customize the query.
         *
         * @return ContentTypesManagement|null The loaded ContentTypesManagement object, or null if not found.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function loadByIdFromId(?int $intId, mixed $objOptionalClauses = null): ?ContentTypesManagement
        {
            // Use QuerySingle to Perform the Query
            return ContentTypesManagement::querySingle(
                QQ::AndCondition(
                    QQ::Equal(QQN::ContentTypesManagement()->Id, $intId)
                ), $objOptionalClauses
            );
        }

        // NOTE: Remember that when introducing a new custom function,
        // you must specify types for the function parameters as well as for the function return type!

        // Override or Create New load/count methods
        // (For obvious reasons, these methods are commented out...
        // But feel free to use these as a starting point)
        /*

            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return an array of ContentTypesManagement objects
                return ContentTypesManagement::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::ContentTypesManagement()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::ContentTypesManagement()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single ContentTypesManagement object
                return ContentTypesManagement::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::ContentTypesManagement()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::ContentTypesManagement()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of ContentTypesManagement objects
                return ContentTypesManagement::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::ContentTypesManagement()->Param1, $strParam1),
                        QQ::Equal(QQN::ContentTypesManagement()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = ContentTypesManagement::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `content_types_management`.*
                    FROM
                        `content_types_management` AS `content_types_management`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return ContentTypesManagement::instantiateDbResult($objDbResult);
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
