<?php

    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/MembersSettingsGen.php');

    /**
     * The MembersSettings class defined here contains any
     * customized code for the MembersSettings class in the
     * Object Relational Model. It represents the "members_settings" table
     * in the database and extends from the code generated abstract MembersSettingsGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class MembersSettings extends MembersSettingsGen
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
         * Loads a single MembersSettings object by its MenuContentId.
         * This method performs a query to retrieve the corresponding object
         * based on the provided identifier.
         *
         * @param int $intId The unique identifier of the MenuContentId to search for.
         * @param null|mixed $objOptionalClauses Optional clauses to customize the query.
         *
         * @return MembersSettings|null The loaded MembersSettings object or null if not found.
         * @throws Caller If an issue occurs during the query execution.
         */
        public static function loadByIdFromMembersSettings(int $intId, mixed $objOptionalClauses = null): ?MembersSettings
        {
            // Call MembersSettings::QuerySingle to perform the loadByIdFromMembersSettings query
            try {
                return MembersSettings::querySingle(
                    QQ::Equal(QQN::MembersSettings()->MenuContentId, $intId),
                    $objOptionalClauses);
            } catch (Caller $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }
        }

        /**
         * Assigns an editor by their ID to the current object, ensuring the association
         * exists if it is not already established.
         *
         * @param mixed $key The unique identifier of the editor to be assigned.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         */
        public function setAssignedEditorsNameById(mixed $key): void
        {
            if ($this->getAssignedByUserObject()->Id !== $key) {
                if (!$this->isUserAsMembersEditorsAssociatedByKey($key)) {
                    $this->associateUserAsMembersEditorsByKey($key);
                }
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
            $objCondition = QQ::Equal(QQN::BoardsSettings()->Title, $title);
            $objSettingsArray = BoardsSettings::queryArray($objCondition);

            return count($objSettingsArray) > 0;
        }

        /**
         * Retrieves a single MembersSettings object by its ID.
         * Executes a query to find and return the record from MembersSettings where the ID matches the given parameter.
         *
         * @param int $intId The ID of the MembersSettings record to retrieve.
         * @param null|mixed $objOptionalClauses Optional query clauses to customize the query behavior.
         *
         * @return MembersSettings|null The MembersSettings object corresponding to the given ID, or null if no match is found.
         * @throws Caller If an error occurs during execution or invalid parameters are provided.
         */
        public static function selectedByIdFromMembersSettings(int $intId, mixed $objOptionalClauses = null): ?MembersSettings
        {
            // Call MembersSettings::querySingle to perform the selectedByIdFromMembersSettings query
            try {
                return MembersSettings::querySingle(
                    QQ::Equal(QQN::MembersSettings()->Id, $intId),
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
                // This will return an array of MembersSettings objects
                return MembersSettings::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::MembersSettings()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::MembersSettings()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single MembersSettings object
                return MembersSettings::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::MembersSettings()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::MembersSettings()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of MembersSettings objects
                return MembersSettings::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::MembersSettings()->Param1, $strParam1),
                        QQ::Equal(QQN::MembersSettings()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = MembersSettings::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `members_settings`.*
                    FROM
                        `members_settings` AS `members_settings`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return MembersSettings::instantiateDbResult($objDbResult);
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
