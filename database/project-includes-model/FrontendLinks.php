<?php

    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/FrontendLinksGen.php');

    /**
     * The FrontendLinks class defined here contains any
     * customized code for the FrontendLinks class in the
     * Object Relational Model. It represents the "frontend_links" table
     * in the database and extends from the code generated abstract FrontendLinksGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class FrontendLinks extends FrontendLinksGen
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
            if ($this->getContentTypesManagamentId() === null) {
                return '';
            }

            return $this->getContentTypesManagamentId();
        }

        /**
         * Loads a FrontendLinks object based on the provided fronted link ID.
         *
         * @param int $intGroupId The ID of the fronted link to be used for the query.
         * @param null|mixed $objOptionalClauses Optional clauses for the query to customize its behavior.
         *
         * @return FrontendLinks|null The FrontendLinks object if found, or null if no matching record exists.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function loadByIdFromFrontedLinksId(int $intGroupId, mixed $objOptionalClauses = null): ?FrontendLinks
        {
            // Use QuerySingle to Perform the Query
            return FrontendLinks::querySingle(
                QQ::AndCondition(
                    QQ::Equal(QQN::FrontendLinks()->LinkedId, $intGroupId)
                ), $objOptionalClauses
            );
        }

        /**
         * Loads a FrontendLinks object based on the specific grouped ID.
         * This method queries the database to retrieve a single object that matches
         * the provided grouped ID criteria.
         *
         * @param int $intId The grouped ID used to filter the query.
         * @param null|mixed $objOptionalClauses Additional optional query parameters or clauses.
         *
         * @return FrontendLinks|null The matching FrontendLinks object, or null if no match is found.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function loadByIdFromFrontedGroupedId(int $intId, mixed $objOptionalClauses = null): ?FrontendLinks
        {
            // Use QuerySingle to Perform the Query
            return FrontendLinks::querySingle(
                QQ::AndCondition(
                    QQ::Equal(QQN::FrontendLinks()->GroupedId, $intId)
                ), $objOptionalClauses
            );
        }

        /**
         * Loads an instance of FrontendLinks by its ContentTypesManagamentId.
         * This method performs a query to retrieve the object matching the provided ID.
         *
         * @param int $intFrontendId The ContentTypesManagamentId to search for.
         * @param null|mixed $objOptionalClauses Optional query clauses to modify the behavior of the query.
         *
         * @return FrontendLinks|null The FrontendLinks object matching the given ID, or null if not found.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function loadByIdFromContentTypesManagamentId(int $intFrontendId, mixed $objOptionalClauses = null): ?FrontendLinks
        {
            // Use QuerySingle to Perform the Query
            return FrontendLinks::querySingle(
                QQ::AndCondition(
                    QQ::Equal(QQN::FrontendLinks()->ContentTypesManagamentId, $intFrontendId)
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
                // This will return an array of FrontendLinks objects
                return FrontendLinks::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::FrontendLinks()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::FrontendLinks()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single FrontendLinks object
                return FrontendLinks::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::FrontendLinks()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::FrontendLinks()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of FrontendLinks objects
                return FrontendLinks::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::FrontendLinks()->Param1, $strParam1),
                        QQ::Equal(QQN::FrontendLinks()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = FrontendLinks::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `frontend_links`.*
                    FROM
                        `frontend_links` AS `frontend_links`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return FrontendLinks::instantiateDbResult($objDbResult);
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
