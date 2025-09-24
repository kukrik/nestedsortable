<?php

    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/BoardsSettingsGen.php');

    /**
     * The BoardsSettings class defined here contains any
     * customized code for the BoardsSettings class in the
     * Object Relational Model. It represents the "boards_settings" table
     * in the database and extends from the code generated abstract BoardsSettingsGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class BoardsSettings extends BoardsSettingsGen
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
         * Loads a record from the BoardsSettings table based on the provided ID.
         *
         * @param int $intId The ID of the record to load.
         * @param null|mixed $objOptionalClauses Optional clauses to customize the query behavior.
         *
         * @return null|\BoardsSettings The fetched record from BoardsSettings or null if no record is found.
         * @throws Caller If an error occurs during query execution.
         */
        public static function loadByIdFromBoardSettings(int $intId, mixed $objOptionalClauses = null): ?BoardsSettings
        {
            // Call BoardsSettings::QuerySingle to perform the loadByIdFromBoardSettings query
            try {
                return BoardsSettings::QuerySingle(
                    QQ::Equal(QQN::BoardsSettings()->MenuContentId, $intId),
                    $objOptionalClauses);
            } catch (Caller $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }
        }

        /**
         * Associates a user as a board editor by their ID if the user is different from the currently assigned one
         * and is not yet associated as a board editor.
         *
         * @param int|string $key The unique identifier of the user to be assigned as a board editor.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         */
        public function setAssignedEditorsNameById(int|string $key): void
        {
            if ($this->getAssignedByUserObject()->Id !== $key) {
                if (!$this->isUserAsBoardsEditorsAssociatedByKey($key)) {
                    $this->associateUserAsBoardsEditorsByKey($key);
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
            $objSettingsArray = BoardsSettings::QueryArray($objCondition);

            return count($objSettingsArray) > 0;
        }

        /**
         * Retrieves a single BoardsSettings object by its ID.
         * Executes a query to find a record in the BoardsSettings table that matches the specified ID.
         *
         * @param int $intId The unique identifier of the BoardsSettings record to be retrieved.
         * @param null|mixed $objOptionalClauses Optional query clauses for customizing the query.
         *
         * @return BoardsSettings|null The matching BoardsSettings object, or null if no record is found.
         * @throws Caller If an error occurs during query execution.
         */
        public static function selectedByIdFromBoardsSettings(int $intId, mixed $objOptionalClauses = null): ?BoardsSettings
        {
            // Call BoardsSettings::QuerySingle to perform the selectedByIdFromBoardsSettings query
            try {
                return BoardsSettings::QuerySingle(
                    QQ::Equal(QQN::BoardsSettings()->Id, $intId),
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
                // This will return an array of BoardsSettings objects
                return BoardsSettings::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::BoardsSettings()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::BoardsSettings()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single BoardsSettings object
                return BoardsSettings::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::BoardsSettings()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::BoardsSettings()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of BoardsSettings objects
                return BoardsSettings::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::BoardsSettings()->Param1, $strParam1),
                        QQ::Equal(QQN::BoardsSettings()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = BoardsSettings::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `boards_settings`.*
                    FROM
                        `boards_settings` AS `boards_settings`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return BoardsSettings::instantiateDbResult($objDbResult);
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
