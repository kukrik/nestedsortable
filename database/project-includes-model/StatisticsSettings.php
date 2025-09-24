<?php

    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/StatisticsSettingsGen.php');

    /**
     * The StatisticsSettings class defined here contains any
     * customized code for the StatisticsSettings class in the
     * Object Relational Model. It represents the "statistics_settings" table
     * in the database and extends from the code generated abstract StatisticsSettingsGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class StatisticsSettings extends StatisticsSettingsGen
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
         * Loads a StatisticsSettings object by its MenuContentId.
         * This function queries the StatisticsSettings table for a single record
         * that matches the given MenuContentId. Optional clauses can also be applied
         * to the query if provided.
         *
         * @param int $intId The MenuContentId to search for in the StatisticsSettings table.
         * @param null|mixed $objOptionalClauses Additional optional query parameters or clauses.
         *
         * @return StatisticsSettings|null The matching StatisticsSettings object or null if no match is found.
         * @throws Caller If an error occurs during the query execution.
         */
        public static function loadByIdFromStatisticsSettings(int $intId, mixed $objOptionalClauses = null): ?StatisticsSettings
        {
            // Call SportsSettings::querySingle to perform the loadByIdFromSportsSettings query
            try {
                return StatisticsSettings::querySingle(
                    QQ::Equal(QQN::StatisticsSettings()->MenuContentId, $intId),
                    $objOptionalClauses);
            } catch (Caller $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }
        }

        /**
         * Assigns an editor to the statistics by their user ID if they are not already associated.
         * Ensures the provided user ID is linked as a statistics editor.
         *
         * @param mixed $key The unique identifier of the user to be assigned as a statistics editor.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         */
        public function setAssignedEditorsNameById(mixed $key): void
        {
            if ($this->getAssignedByUserObject()->Id !== $key) {
                if (!$this->isUserAsStatisticsEditorsAssociatedByKey($key)) {
                    $this->associateUserAsStatisticsEditorsByKey($key);
                }
            }
        }

        /**
         * Retrieves a single StatisticsSettings object based on its ID, applying optional query clauses if provided.
         *
         * @param int $intId The ID of the StatisticsSettings object to retrieve.
         * @param null|mixed $objOptionalClauses Optional clauses to customize the query, such as conditions or ordering.
         *
         * @return StatisticsSettings|null The matched StatisticsSettings object or null if no match is found.
         * @throws Caller If an invalid argument is passed or an error occurs during the query execution.
         */
        public static function selectedByIdFromStatisticsSettings(int $intId, mixed $objOptionalClauses = null): ?StatisticsSettings
        {
            // Call StatisticsSettings::querySingle to perform the selectedByIdFromStatisticsSettings query
            try {
                return StatisticsSettings::querySingle(
                    QQ::Equal(QQN::StatisticsSettings()->Id, $intId),
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
            $objCondition = QQ::Equal(QQN::StatisticsSettings()->Name, $title);
            $objChangesArray = StatisticsSettings::queryArray($objCondition);

            return count($objChangesArray) > 0;
        }

        // NOTE: Remember that when introducing a new custom function,
        // you must specify types for the function parameters as well as for the function return type!

        // Override or Create New load/count methods
        // (For obvious reasons, these methods are commented out...
        // But feel free to use these as a starting point)
        /*

            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return an array of StatisticsSettings objects
                return StatisticsSettings::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::StatisticsSettings()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::StatisticsSettings()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single StatisticsSettings object
                return StatisticsSettings::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::StatisticsSettings()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::StatisticsSettings()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of StatisticsSettings objects
                return StatisticsSettings::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::StatisticsSettings()->Param1, $strParam1),
                        QQ::Equal(QQN::StatisticsSettings()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = StatisticsSettings::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `statistics_settings`.*
                    FROM
                        `statistics_settings` AS `statistics_settings`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return StatisticsSettings::instantiateDbResult($objDbResult);
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
