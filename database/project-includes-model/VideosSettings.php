<?php

    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/VideosSettingsGen.php');

    /**
     * The VideosSettings class defined here contains any
     * customized code for the VideosSettings class in the
     * Object Relational Model. It represents the "videos_settings" table
     * in the database and extends from the code generated abstract VideosSettingsGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class VideosSettings extends VideosSettingsGen
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
         * Loads a VideosSettings object by its associated ID.
         * Executes a query to retrieve a single VideosSettings record that matches the provided ID.
         *
         * @param int $intId The ID of the VideosSettings record to be loaded.
         * @param null|mixed $objOptionalClauses Additional optional query clauses for customizing the query.
         *
         * @return VideosSettings|null The loaded VideosSettings object if found, or null if no record matches the ID.
         * @throws Caller Exception thrown if an error occurs during query execution.
         */
        public static function loadByIdFromVideosSettings(int $intId, mixed $objOptionalClauses = null): ?VideosSettings
        {
            // Call VideosSettings::querySingle to perform the loadByIdFromVideosSettings query
            try {
                return VideosSettings::querySingle(
                    QQ::Equal(QQN::VideosSettings()->MenuContentId, $intId),
                    $objOptionalClauses);
            } catch (Caller $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }
        }

        /**
         * Associates the assigned editor's name with the given key if it is not already assigned.
         *
         * @param mixed $key The unique identifier for the editor to be assigned.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         */
        public function setAssignedEditorsNameById(mixed $key): void
        {
            if ($this->getAssignedByUserObject()->Id !== $key) {
                if (!$this->isUserAsVideosEditorsAssociatedByKey($key)) {
                    $this->associateUserAsVideosEditorsByKey($key);
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
            $objCondition = QQ::Equal(QQN::VideosSettings()->Title, $title);
            $objSettingsArray = VideosSettings::queryArray($objCondition);

            return count($objSettingsArray) > 0;
        }

        /**
         * Retrieves a single VideosSettings object based on the provided ID.
         *
         * @param int $intId The ID of the VideosSettings record to retrieve.
         * @param null|mixed $objOptionalClauses Optional clauses for modifying the query.
         *
         * @return VideosSettings|null The VideosSettings object if found, or null if no matching record exists.
         * @throws Caller If an error occurs during the query.
         */
        public static function selectedByIdFromVideosSettings(int $intId, mixed $objOptionalClauses = null): ?VideosSettings
        {
            // Call VideosSettings::querySingle to perform the selectedByIdFromVideosSettings query
            try {
                return VideosSettings::querySingle(
                    QQ::Equal(QQN::VideosSettings()->Id, $intId),
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
                // This will return an array of VideosSettings objects
                return VideosSettings::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::VideosSettings()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::VideosSettings()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single VideosSettings object
                return VideosSettings::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::VideosSettings()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::VideosSettings()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of VideosSettings objects
                return VideosSettings::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::VideosSettings()->Param1, $strParam1),
                        QQ::Equal(QQN::VideosSettings()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = VideosSettings::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `videos_settings`.*
                    FROM
                        `videos_settings` AS `videos_settings`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return VideosSettings::instantiateDbResult($objDbResult);
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
