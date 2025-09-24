<?php

    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/LinksGen.php');

    /**
     * The Links class defined here contains any
     * customized code for the Links class in the
     * Object Relational Model. It represents the "links" table
     * in the database and extends from the code generated abstract LinksGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class Links extends LinksGen
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
            return 'Links Object ' . $this->primaryKey();
        }

        /**
         * Loads a Links object by its associated SettingsId.
         * Utilizes the QuerySingle method to perform the query.
         *
         * @param int $intId The identifier for the SettingsId to load the Links object.
         * @param null|mixed $objOptionalClauses Optional clauses for the query, such as conditions or ordering.
         *
         * @return Links|null The Links object corresponding to the given SettingsId, or null if not found.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function loadByIdFromLinksId(int $intId, mixed $objOptionalClauses = null): ?Links
        {
            // Use QuerySingle to Perform the Query
            return Links::querySingle(
                QQ::AndCondition(
                    QQ::Equal(QQN::Links()->SettingsId, $intId)
                ), $objOptionalClauses
            );
        }

        /**
         * Generates the next order value based on the largest existing order value for a given group ID.
         *
         * @param int $groupId The ID of the group for which the next order value is to be generated.
         *
         * @return int The next order value, incremented from the largest existing order value, or 1 if none exists.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function generateOrder(int $groupId): int
        {
            // Find the largest "order" value based on a given group ID
            $objLink = Links::querySingle(
                QQ::equal(QQN::Links()->SettingsId, $groupId),
                [QQ::maximum(QQN::Links()->Order, 'max_order')]
            );

            // Get the largest order value if it exists, otherwise start from zero
            $maxOrder = $objLink ? $objLink->getVirtualAttribute('max_order') : 0;

            // Return the next value
            return $maxOrder + 1;
        }

        // NOTE: Remember that when introducing a new custom function,
        // you must specify types for the function parameters as well as for the function return type!

        // Override or Create New load/count methods
        // (For obvious reasons, these methods are commented out...
        // But feel free to use these as a starting point)
        /*

            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return an array of Links objects
                return Links::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Links()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::Links()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single Links object
                return Links::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Links()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::Links()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of Links objects
                return Links::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Links()->Param1, $strParam1),
                        QQ::Equal(QQN::Links()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = Links::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `links`.*
                    FROM
                        `links` AS `links`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return Links::instantiateDbResult($objDbResult);
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
