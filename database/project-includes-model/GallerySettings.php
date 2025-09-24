<?php

    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/GallerySettingsGen.php');

    /**
     * The GallerySettings class defined here contains any
     * customized code for the GallerySettings class in the
     * Object Relational Model. It represents the "gallery_settings" table
     * in the database and extends from the code generated abstract GallerySettingsGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class GallerySettings extends GallerySettingsGen
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
         * Loads a single GallerySettings object by its GalleryGroupId using specified query clauses.
         *
         * @param int $intId The ID of the gallery group to load the settings for.
         * @param mixed|null $objOptionalClauses Optional query clauses to customize the query behavior.
         *
         * @return GallerySettings|null The loaded GallerySettings object, or null if no match is found.
         * @throws Caller If an error occurs during query execution.
         */
        public static function loadByIdFromGallerySettings(int $intId, mixed $objOptionalClauses = null): ?GallerySettings
        {
            // Call EventsSettings::QuerySingle to perform the loadArrayByIdFromEventsSettings query
            try {
                return GallerySettings::querySingle(
                    QQ::Equal(QQN::GallerySettings()->GalleryGroupId, $intId),
                    $objOptionalClauses);
            } catch (Caller $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }
        }

        /**
         * Retrieves a single record from the GallerySettings table based on the provided ID.
         * This method uses a query to select the record where the ID matches the given value
         * and allows optional clauses to modify the query.
         *
         * @param int $intId The ID of the GallerySettings record to retrieve.
         * @param null|mixed $objOptionalClauses Optional clauses to customize the query.
         *
         * @return GallerySettings|null The matching GallerySettings object, or null if no match is found.
         * @throws Caller If the method call is invalid, or an error occurs during the query process.
         */
        public static function selectedByIdFromGallerySettings(int $intId, mixed $objOptionalClauses = null): ?GallerySettings
        {
            // Call GallerySettings::QuerySingle to perform the selectedByIdFromGallerySettings query
            try {
                return GallerySettings::querySingle(
                    QQ::Equal(QQN::GallerySettings()->Id, $intId),
                    $objOptionalClauses);
            } catch (Caller $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }
        }

        /**
         * Loads an array of GallerySettings objects based on a given ID.
         *
         * @param int $intId The ID used to query GallerySettings records.
         * @param null|mixed $objOptionalClauses Optional clauses for query customization.
         *
         * @return GallerySettings[] An array of GallerySettings objects corresponding to the provided ID.
         * @throws Caller If an exception occurs during the query execution.
         */
        public static function loadArrayByIdFromGallerySettings(int $intId, mixed $objOptionalClauses = null): array
        {
            // Call GallerySettings::QueryArray to perform the loadArrayByIdGallerySettings query
            try {
                return GallerySettings::queryArray(
                    QQ::Equal(QQN::GallerySettings()->Id, $intId),
                    $objOptionalClauses);
            } catch (Caller $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }
        }

        /**
         * Checks if a specific title exists in the database.
         *
         * @param string $title The title to check for existence.
         *
         * @return bool True if the title exists, false otherwise.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function titleExists(string $title): bool
        {
            $objCondition = QQ::Equal(QQN::GallerySettings()->Name, $title);
            $objChangesArray = GallerySettings::queryArray($objCondition);

            return count($objChangesArray) > 0;
        }

        // NOTE: Remember that when introducing a new custom function,
        // you must specify types for the function parameters as well as for the function return type!

        // Override or Create New load/count methods
        // (For obvious reasons, these methods are commented out...
        // But feel free to use these as a starting point)
        /*

            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return an array of GallerySettings objects
                return GallerySettings::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::GallerySettings()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::GallerySettings()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single GallerySettings object
                return GallerySettings::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::GallerySettings()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::GallerySettings()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of GallerySettings objects
                return GallerySettings::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::GallerySettings()->Param1, $strParam1),
                        QQ::Equal(QQN::GallerySettings()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = GallerySettings::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `gallery_settings`.*
                    FROM
                        `gallery_settings` AS `gallery_settings`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return GallerySettings::instantiateDbResult($objDbResult);
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
