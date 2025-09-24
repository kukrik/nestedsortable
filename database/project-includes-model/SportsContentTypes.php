<?php

    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/SportsContentTypesGen.php');

    /**
     * The SportsContentTypes class defined here contains any
     * customized code for the SportsContentTypes class in the
     * Object Relational Model. It represents the "sports_content_types" table
     * in the database and extends from the code generated abstract SportsContentTypesGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class SportsContentTypes extends SportsContentTypesGen
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
            $objCondition = QQ::Equal(QQN::SportsContentTypes()->Name, $title);
            $objChangesArray = SportsContentTypes::queryArray($objCondition);

            return count($objChangesArray) > 0;
        }

        /**
         * Updates the lock states for all sports content types based on related sports tables.
         * Sets `is_locked` to 2 for records with linked entries in sports_tables,
         * and to 1 for records without linked entries.
         *
         * @return void
         * @throws Caller
         */
        public static function updateAllIsLockStates(): void
        {
            $db = static::getDatabase();
            $db->NonQuery("
                UPDATE sports_content_types 
                SET type_locked = 2 
                WHERE id IN (
                    SELECT DISTINCT sports_content_types_id FROM sports_tables WHERE sports_content_types_id IS NOT NULL
                )
            ");

            $db->NonQuery("
                UPDATE sports_content_types  
                SET type_locked = 1
                WHERE id NOT IN (
                    SELECT DISTINCT sports_content_types_id FROM sports_tables WHERE sports_content_types_id IS NOT NULL
                )
            ");
        }

        // NOTE: Remember that when introducing a new custom function,
        // you must specify types for the function parameters as well as for the function return type!

        // Override or Create New load/count methods
        // (For obvious reasons, these methods are commented out...
        // But feel free to use these as a starting point)
        /*

            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return an array of SportsContentTypes objects
                return SportsContentTypes::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::SportsContentTypes()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::SportsContentTypes()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single SportsContentTypes object
                return SportsContentTypes::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::SportsContentTypes()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::SportsContentTypes()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of SportsContentTypes objects
                return SportsContentTypes::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::SportsContentTypes()->Param1, $strParam1),
                        QQ::Equal(QQN::SportsContentTypes()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = SportsContentTypes::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `sports_content_types`.*
                    FROM
                        `sports_content_types` AS `sports_content_types`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return SportsContentTypes::instantiateDbResult($objDbResult);
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
