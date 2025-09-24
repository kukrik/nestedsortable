<?php

    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/MetadataGen.php');

    /**
     * The Metadata class defined here contains any
     * customized code for the Metadata class in the
     * Object Relational Model. It represents the "metadata" table
     * in the database and extends from the code generated abstract MetadataGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class Metadata extends MetadataGen
    {
        /**
         * Default "to string" handler
         * Allows pages to _p()/echo()/print() this object, and to define the default
         * way this object would be outputted.
         *
         * @return string a nicely formatted string representation of this object
         */
        public function __toString(): string
        {
            return 'Metadata Object ' . $this->primaryKey();
        }

        /**
         * Loads a Metadata object by its MenuContentId from the database.
         * Executes a query to retrieve a single Metadata object matching the given ID.
         *
         * @param int $intMenuContentId The unique identifier for the MenuContent.
         * @param null|mixed $objOptionalClauses Additional query clauses to apply (optional).
         *
         * @return Metadata|null The Metadata object if found, or null if no match is found.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function loadByIdFromMetadata(int $intMenuContentId, mixed $objOptionalClauses = null): ?Metadata
        {
            // Use querySingle to Perform the Query
            return Metadata::querySingle(
                QQ::AndCondition(
                    QQ::Equal(QQN::Metadata()->MenuContentId, $intMenuContentId)
                ), $objOptionalClauses
            );
        }

        // NOTE: Remember that when introducing a new custom function,
        // you must specify types for the function parameters as well as for the function return type!

        // Override or Create New load/count methods
        // (For obvious reasons, these methods are commented out...
        // But feel free to use these as a starting point)
        /*

            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return an array of Metadata objects
                return Metadata::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Metadata()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::Metadata()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single Metadata object
                return Metadata::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Metadata()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::Metadata()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of Metadata objects
                return Metadata::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Metadata()->Param1, $strParam1),
                        QQ::Equal(QQN::Metadata()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = Metadata::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `metadata`.*
                    FROM
                        `metadata` AS `metadata`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return Metadata::instantiateDbResult($objDbResult);
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
