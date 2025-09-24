<?php

    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/EventsSettingsGen.php');

    /**
     * The EventsSettings class defined here contains any
     * customized code for the EventsSettings class in the
     * Object Relational Model. It represents the "events_settings" table
     * in the database and extends from the code generated abstract EventsSettingsGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class EventsSettings extends EventsSettingsGen
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
         * Loads a single EventsSettings object based on the given ID and optional clauses.
         * This method performs a query to fetch an EventsSettings object where the MenuContentId matches the provided ID.
         *
         * @param int $intId The ID to match against the MenuContentId field in the EventsSettings table.
         * @param null|mixed $objOptionalClauses Optional clauses for customizing the query (e.g., QQ::Expand(), QQ::OrderBy()).
         *
         * @return EventsSettings|null The matching EventsSettings object if found; otherwise, null.
         * @throws Caller If an error occurs during the query execution.
         */
        public static function loadByIdFromEventsSettings(int $intId, mixed $objOptionalClauses = null): ?EventsSettings
        {
            // Call EventsSettings::QuerySingle to perform the loadArrayByIdFromEventsSettings query
            try {
                return EventsSettings::querySingle(
                    QQ::Equal(QQN::EventsSettings()->MenuContentId, $intId),
                    $objOptionalClauses);
            } catch (Caller $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }
        }

        /**
         * Retrieves a single record from the EventsSettings table based on the provided ID.
         *
         * @param int $intId The unique identifier for the desired record in the EventsSettings table.
         * @param null|mixed $objOptionalClauses Optional clauses to customize the query (e.g., sorting, conditions).
         *
         * @return EventsSettings|null The EventsSettings object corresponding to the given ID, or null if not found.
         * @throws Caller If an error occurs during query execution or invalid arguments are provided.
         */
        public static function selectedByIdFromEventsSettings(int $intId, mixed $objOptionalClauses = null): ?EventsSettings
        {
            // Call EventsSettings::QuerySingle to perform the loadArrayByIdFromEventsSettings query
            try {
                return EventsSettings::querySingle(
                    QQ::Equal(QQN::EventsSettings()->Id, $intId),
                    $objOptionalClauses);
            } catch (Caller $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }
        }

        /**
         * Checks if a specific title exists within the EventsSettings records.
         *
         * @param string $title The title to search for in the EventsSettings database.
         *
         * @return bool True if the title exists, otherwise false.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function titleExists(string $title): bool
        {
            $objCondition = QQ::Equal(QQN::EventsSettings()->Name, $title);
            $objChangesArray = EventsSettings::queryArray($objCondition);

            return count($objChangesArray) > 0;
        }

        // NOTE: Remember that when introducing a new custom function,
        // you must specify types for the function parameters as well as for the function return type!

        // Override or Create New load/count methods
        // (For obvious reasons, these methods are commented out...
        // But feel free to use these as a starting point)
        /*

            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return an array of EventsSettings objects
                return EventsSettings::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::EventsSettings()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::EventsSettings()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single EventsSettings object
                return EventsSettings::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::EventsSettings()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::EventsSettings()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of EventsSettings objects
                return EventsSettings::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::EventsSettings()->Param1, $strParam1),
                        QQ::Equal(QQN::EventsSettings()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = EventsSettings::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `events_settings`.*
                    FROM
                        `events_settings` AS `events_settings`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return EventsSettings::instantiateDbResult($objDbResult);
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
