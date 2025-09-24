<?php

    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/NewsChangesGen.php');

    /**
     * The NewsChanges class defined here contains any
     * customized code for the NewsChanges class in the
     * Object Relational Model. It represents the "news_changes" table
     * in the database and extends from the code generated abstract NewsChangesGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class NewsChanges extends NewsChangesGen
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
            $objCondition = QQ::Equal(QQN::NewsChanges()->Title, $title);
            $objChangesArray = NewsChanges::queryArray($objCondition);

            return count($objChangesArray) > 0;
        }

        /**
         * Updates the lock states of all change records in the database.
         * Locks records in the `news_changes` table that are associated with any records in the `news` table via a
         * non-null `changes_id`. Unlocks records in the `news_changes` table that are not associated with any records
         * in the `news` table.
         *
         * @return void
         * @throws Caller
         */
        public static function updateAllChangeLockStates(): void
        {
            $db = static::getDatabase();
            $db->NonQuery("
                UPDATE news_changes 
                SET news_change_locked = 1 
                WHERE id IN (
                    SELECT DISTINCT changes_id FROM news WHERE changes_id IS NOT NULL
                )
            ");

            $db->NonQuery("
                UPDATE news_changes 
                SET news_change_locked = 0
                WHERE id NOT IN (
                    SELECT DISTINCT changes_id FROM news WHERE changes_id IS NOT NULL
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
                // This will return an array of NewsChanges objects
                return NewsChanges::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::NewsChanges()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::NewsChanges()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single NewsChanges object
                return NewsChanges::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::NewsChanges()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::NewsChanges()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of NewsChanges objects
                return NewsChanges::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::NewsChanges()->Param1, $strParam1),
                        QQ::Equal(QQN::NewsChanges()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = NewsChanges::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `news_changes`.*
                    FROM
                        `news_changes` AS `news_changes`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return NewsChanges::instantiateDbResult($objDbResult);
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
