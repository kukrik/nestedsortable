<?php

    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/SportsAreasGen.php');

    /**
     * The SportsAreas class defined here contains any
     * customized code for the SportsAreas class in the
     * Object Relational Model. It represents the "sports_areas" table
     * in the database and extends from the code generated abstract SportsAreasGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class SportsAreas extends SportsAreasGen
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
            return t($this->getName());
        }

        /**
         * Checks if the given name exists in the database.
         *
         * @param string $title The name to check
         *
         * @return bool True if the name exists, False otherwise
         * @throws Caller
         * @throws InvalidCast
         */
        public static function titleExists(string $title): bool
        {
            $objCondition = QQ::Equal(QQN::SportsAreas()->Name, $title);
            $objChangesArray = SportsAreas::queryArray($objCondition);

            return count($objChangesArray) > 0;
        }

        /**
         * Updates the "is_locked" states for all entries in the sports_areas table.
         * Determines the "is_locked" value based on whether related records exist
         * in the sports_calendar or sports_areas_competition_areas tables.
         *
         * @return void
         * @throws Caller
         */
        public static function updateAllIsLockStates(): void
        {
            $db = static::getDatabase();
            $db->NonQuery("
                UPDATE sports_areas sa
                SET is_locked = CASE
                    WHEN EXISTS (SELECT 1 FROM sports_calendar sc WHERE sc.sports_areas_id = sa.id)
                        OR EXISTS (SELECT 1 FROM sports_areas_competition_areas saca WHERE saca.sports_areas_id = sa.id)
                    THEN 2
                    ELSE 1
                END
            ");
        }

        // NOTE: Remember that when introducing a new custom function,
        // you must specify types for the function parameters as well as for the function return type!

        // Override or Create New load/count methods
        // (For obvious reasons, these methods are commented out...
        // But feel free to use these as a starting point)
        /*

            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return an array of SportsAreas objects
                return SportsAreas::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::SportsAreas()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::SportsAreas()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single SportsAreas object
                return SportsAreas::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::SportsAreas()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::SportsAreas()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of SportsAreas objects
                return SportsAreas::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::SportsAreas()->Param1, $strParam1),
                        QQ::Equal(QQN::SportsAreas()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = SportsAreas::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `sports_areas`.*
                    FROM
                        `sports_areas` AS `sports_areas`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return SportsAreas::instantiateDbResult($objDbResult);
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
