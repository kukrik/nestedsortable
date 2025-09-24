<?php

    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/SlidersGen.php');

    /**
     * The Sliders class defined here contains any
     * customized code for the Sliders class in the
     * Object Relational Model.  It represents the "sliders" table
     * in the database and extends from the code generated abstract SlidersGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class Sliders extends SlidersGen {
        /**
         * Default "to string" handler
         * Allows pages to _p()/echo()/print() this object, and to define the default
         * way this object would be outputted.
         *
         * @return string a nicely formatted string representation of this object
         */
        public function __toString(): string
        {
            return $this->primaryKey();
        }

        /**
         * Updates the author's name based on the assigned user object if the provided key does not match
         * the ID of the assigned user. The author's name is set by concatenating the user's first and last names.
         *
         * @param mixed $key The identifier to check against the ID of the assigned user object.
         *
         * @return void
         * @throws Caller
         */
        public function setUserNameById(mixed $key): void
        {
            if ($this->getAssignedByUserObject()->Id !== $key) {
                $this->Author = $this->getAssignedByUserObject()->getFirstname() . ' ' .  $this->getAssignedByUserObject()->getLastName();
                $this->save();
            }
        }

        /**
         * Loads a Sliders object based on the given Group ID.
         * Executes a single query to retrieve the record corresponding to the specified Group ID.
         *
         * @param int $intId The ID of the sliders group to load.
         * @param null|mixed $objOptionalClauses Optional clauses for customizing the query, such as conditions or orders.
         *
         * @return Sliders|null The Sliders object if found, or null if no matching record is found.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function loadByIdFromSlidersGroupId(int $intId, mixed $objOptionalClauses = null): ?Sliders
        {
            // Use querySingle to Perform the Query
            return Sliders::querySingle(
                QQ::AndCondition(
                    QQ::Equal(QQN::Sliders()->GroupId, $intId)
                ), $objOptionalClauses
            );
        }

        /**
         * Counts the number of slider entries that match the given group ID and status.
         * Executes a query to count sliders based on provided criteria.
         *
         * @param int $intId The group ID to filter sliders by.
         * @param int $intStatus The status to filter sliders by.
         *
         * @return int The count of sliders that match the given criteria.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function countByStatusfromId(int $intId, int $intStatus): int
        {
            // Call Sliders::queryCount to perform the countByStatusfromId query

            return Sliders::queryCount(
                QQ::AndCondition(
                    QQ::Equal(QQN::Sliders()->GroupId, $intId),
                    QQ::Equal(QQN::Sliders()->Status, $intStatus)
                )
            );
        }

        // Override or Create New Load/Count methods
        // (For obvious reasons, these methods are commented out...
        // But feel free to use these as a starting point)
        /*
            public static function LoadArrayBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return an array of Sliders objects
                return Sliders::QueryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Sliders()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::Sliders()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }

            public static function LoadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single Sliders object
                return Sliders::QuerySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Sliders()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::Sliders()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }

            public static function CountBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of Sliders objects
                return Sliders::QueryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Sliders()->Param1, $strParam1),
                        QQ::Equal(QQN::Sliders()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }

            public static function LoadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = Sliders::GetDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `sliders`.*
                    FROM
                        `sliders` AS `sliders`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return Sliders::InstantiateDbResult($objDbResult);
            }
        */



        // Override or Create New Properties and Variables
        // For performance reasons, these variables and __set and __get override methods
        // are commented out.  But if you wish to implement or override any
        // of the data-generated properties, please feel free to uncomment them.
        /*
            protected $strSomeNewProperty;

            public function __get($strName) {
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

            public function __set($strName, $mixValue) {
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



        /*
            public function Initialize()
            {
                parent::Initialize();
                // You additional initializations here
            }
        */
    }
