<?php

    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/SportsTablesGen.php');

    /**
     * The SportsTables class defined here contains any
     * customized code for the SportsTables class in the
     * Object Relational Model. It represents the "sports_tables" table
     * in the database and extends from the code generated abstract SportsTablesGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class SportsTables extends SportsTablesGen
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
            return $this->getTitle();
        }

        /**
         * Checks if a title exists in the SportsTables database.
         *
         * @param string $title The title to check for existence.
         *
         * @return bool True if the title exists, false otherwise.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function titleExists(string $title): bool
        {
            $objCondition = QQ::Equal(QQN::SportsTables()->Title, $title);
            $objAreasArray = SportsTables::queryArray($objCondition);

            return count($objAreasArray) > 0;
        }

        /**
         * Loads a single record from the SportsTables table based on the provided ID.
         *
         * @param int $intId The ID of the record to load.
         * @param null|mixed $objOptionalClauses Optional clauses for query customization.
         *                                       This may include conditions, orderings, or limits.
         *
         * @return SportsTables|null The loaded SportsTables object, or null if no record is found.
         * @throws Caller if an error occurs during the query execution.
         */
        public static function loadByIdFromSportsTables(int $intId, mixed $objOptionalClauses = null): ?SportsTables
        {
            // Call SportsTables::querySingle to perform the loadByIdFromSportsTables query
            try {
                return SportsTables::querySingle(
                    QQ::Equal(QQN::SportsTables()->SportsCalendarGroupId, $intId),
                    $objOptionalClauses);
            } catch (Caller $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }
        }

        /**
         * Counts the number of records in the SportsTables table with a specific SportsContentTypesId.
         *
         * @param int $intId The ID of the SportsContentTypes to filter the count query.
         *
         * @return int The number of records matching the specified SportsContentTypesId.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function countBySportsContentTypesId(int $intId): int
        {
            // Call SportsTables::queryCount to perform the countBySportsContentTypesId query
            return SportsTables::queryCount(
                QQ::Equal(QQN::SportsTables()->SportsContentTypesId, $intId)
            );
        }

        // NOTE: Remember that when introducing a new custom function,
        // you must specify types for the function parameters as well as for the function return type!

        // Override or Create New load/count methods
        // (For obvious reasons, these methods are commented out...
        // But feel free to use these as a starting point)
        /*

            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return an array of SportsTables objects
                return SportsTables::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::SportsTables()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::SportsTables()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single SportsTables object
                return SportsTables::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::SportsTables()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::SportsTables()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of SportsTables objects
                return SportsTables::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::SportsTables()->Param1, $strParam1),
                        QQ::Equal(QQN::SportsTables()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = SportsTables::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `sports_tables`.*
                    FROM
                        `sports_tables` AS `sports_tables`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return SportsTables::instantiateDbResult($objDbResult);
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
