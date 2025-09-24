<?php

    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/LinksSettingsGen.php');

    /**
     * The LinksSettings class defined here contains any
     * customized code for the LinksSettings class in the
     * Object Relational Model. It represents the "links_settings" table
     * in the database and extends from the code generated abstract LinksSettingsGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class LinksSettings extends LinksSettingsGen
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
         * Loads a LinksSettings object based on a given ID.
         *
         * This method performs a query to retrieve a single LinksSettings object
         * by matching the provided MenuContentId with the given ID.
         *
         * @param int $intId The ID to query against the MenuContentId field.
         * @param null|mixed $objOptionalClauses Optional clauses for the query, such as sorting or conditions.
         *
         * @return LinksSettings|null The matching LinksSettings object if found, or null if no match is found.
         * @throws Caller If an error occurs during the query operation.
         */
        public static function loadByIdFromLinksSettings(int $intId, mixed $objOptionalClauses = null): ?LinksSettings
        {
            // Call LinksSettings::QuerySingle to perform the loadByIdFromLinksSettings query
            try {
                return LinksSettings::querySingle(
                    QQ::Equal(QQN::LinksSettings()->MenuContentId, $intId),
                    $objOptionalClauses);
            } catch (Caller $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }
        }

        /**
         * Sets the assigned editor's name by a given ID. If the provided key does not match
         * the current assigned user's ID and there is no existing association, it will associate
         * the user using the given key.
         *
         * @param mixed $key Identifier of the editor to be assigned.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         */
        public function setAssignedEditorsNameById(mixed $key): void
        {
            if ($this->getAssignedByUserObject()->Id !== $key) {
                if (!$this->isUserAsLinksEditorsAssociatedByKey($key)) {
                    $this->associateUserAsLinksEditorsByKey($key);
                }
            }
        }

        /**
         * Fetches a single LinksSettings object by its ID.
         * This method performs a query to retrieve the object matching the provided ID, optionally with additional query clauses.
         *
         * @param int $intId The unique ID of the LinksSettings object to retrieve.
         * @param null|mixed $objOptionalClauses Optional query clauses to specify additional conditions or ordering.
         *
         * @return LinksSettings|null The LinksSettings object if found, or null if no match is found.
         * @throws Caller If an exception occurs during the query execution.
         */
        public static function selectedByIdFromLinksSettings(int $intId, mixed $objOptionalClauses = null): ?LinksSettings
        {
            // Call LinksSettings::QuerySingle to perform the selectedByIdFromLinksSettings query
            try {
                return LinksSettings::querySingle(
                    QQ::Equal(QQN::LinksSettings()->Id, $intId),
                    $objOptionalClauses);
            } catch (Caller $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }
        }

        // NOTE: Remember that when introducing a new custom function,
        // you must specify types for the function parameters as well as for the function return type!

        // Override or Create New load/count methods
        // (For obvious reasons, these methods are commented out...
        // But feel free to use these as a starting point)
        /*

            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return an array of LinksSettings objects
                return LinksSettings::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::LinksSettings()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::LinksSettings()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single LinksSettings object
                return LinksSettings::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::LinksSettings()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::LinksSettings()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of LinksSettings objects
                return LinksSettings::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::LinksSettings()->Param1, $strParam1),
                        QQ::Equal(QQN::LinksSettings()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = LinksSettings::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `links_settings`.*
                    FROM
                        `links_settings` AS `links_settings`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return LinksSettings::instantiateDbResult($objDbResult);
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
