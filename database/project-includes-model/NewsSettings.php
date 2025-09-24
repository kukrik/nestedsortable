<?php

    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/NewsSettingsGen.php');

    /**
     * The NewsSettings class defined here contains any
     * customized code for the NewsSettings class in the
     * Object Relational Model. It represents the "news_settings" table
     * in the database and extends from the code generated abstract NewsSettingsGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class NewsSettings extends NewsSettingsGen
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
            return $this->getName();
        }

        /**
         * Loads a NewsSettings record based on the NewsGroupId.
         *
         * @param int $intId The ID of the NewsGroup to query.
         * @param null|mixed $objOptionalClauses Optional clauses to customize the query.
         *
         * @return null|\NewsSettings The corresponding NewsSettings object if found, or null if not found.
         * @throws Caller If an error occurs during the query execution.
         */
        public static function loadByIdFromNewsSettings(int $intId, mixed $objOptionalClauses = null): ?NewsSettings
        {
            // Call NewsSettings::querySingle to perform the loadByIdFromNewsSettings query
            try {
                return NewsSettings::querySingle(
                    QQ::Equal(QQN::NewsSettings()->MenuContentId, $intId),
                    $objOptionalClauses);
            } catch (Caller $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }
        }

        /**
         * Retrieves a single NewsSettings object based on its ID.
         * Executes the query to load an object of type NewsSettings using the provided ID.
         *
         * @param int $intId The ID of the NewsSettings object to retrieve.
         * @param null|mixed $objOptionalClauses Additional optional clauses for the query, if any.
         *
         * @return NewsSettings|null The NewsSettings object if found, or null if no matching record exists.
         * @throws Caller If an exception occurs during the query execution.
         */
        public static function selectedByIdFromNewsSettings(int $intId, mixed $objOptionalClauses = null): ?NewsSettings
        {
            // Call NewsSettings::querySingle to perform the loadByIdFromNewsSettings query
            try {
                return NewsSettings::querySingle(
                    QQ::Equal(QQN::NewsSettings()->Id, $intId),
                    $objOptionalClauses);
            } catch (Caller $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }
        }

        /**
         * Checks if the given title exists in the database.
         *
         * @param string $title The title to check
         *
         * @return bool True if the title exists, False otherwise
         * @throws Caller
         * @throws InvalidCast
         */
        public static function titleExists(string $title): bool
        {
            $objCondition = QQ::Equal(QQN::NewsSettings()->Name, $title);
            $objChangesArray = NewsSettings::queryArray($objCondition);

            return count($objChangesArray) > 0;
        }

        // NOTE: Remember that when introducing a new custom function,
        // you must specify types for the function parameters as well as for the function return type!

        // Override or Create New load/count methods
        // (For obvious reasons, these methods are commented out...
        // But feel free to use these as a starting point)
        /*

            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return an array of NewsSettings objects
                return NewsSettings::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::NewsSettings()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::NewsSettings()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single NewsSettings object
                return NewsSettings::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::NewsSettings()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::NewsSettings()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of NewsSettings objects
                return NewsSettings::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::NewsSettings()->Param1, $strParam1),
                        QQ::Equal(QQN::NewsSettings()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = NewsSettings::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `news_settings`.*
                    FROM
                        `news_settings` AS `news_settings`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return NewsSettings::instantiateDbResult($objDbResult);
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
