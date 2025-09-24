<?php

    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/BoardGen.php');

    /**
     * The Board class defined here contains any
     * customized code for the Board class in the
     * Object Relational Model. It represents the "board" table
     * in the database and extends from the code generated abstract BoardGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class Board extends BoardGen
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
            return 'Board Object ' . $this->primaryKey();
        }

        /**
         * Loads a Board object by its ID from the board table using the provided Board ID.
         *
         * @param int $intId The unique identifier of the board to load.
         * @param null|mixed $objOptionalClauses Additional optional query clauses to customize the query execution.
         *
         * @return Board|null The Board object if found, or null if no match is found.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function loadByIdFromBoardId(int $intId, mixed $objOptionalClauses = null): ?Board
        {
            // Use QuerySingle to Perform the Query
            return Board::querySingle(
                QQ::AndCondition(
                    QQ::Equal(QQN::Board()->BoardId, $intId)
                ), $objOptionalClauses
            );
        }

        /**
         * Loads a Board object based on a grouped ID.
         * This method performs a query to retrieve a single Board object where
         * the MenuContentGroupId matches the provided ID.
         *
         * @param int $intId The ID to filter the Board objects by MenuContentGroupId.
         * @param null|mixed $objOptionalClauses Optional clauses for customizing the query.
         *
         * @return Board|null The matching Board object, or null if no match is found.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function loadByIdFromGroupedId(int $intId, mixed $objOptionalClauses = null): ?Board
        {
            // Use QuerySingle to Perform the Query
            return Board::querySingle(
                QQ::AndCondition(
                    QQ::Equal(QQN::Board()->MenuContentGroupId, $intId)
                ), $objOptionalClauses
            );
        }

        /**
         * Generates the next order value for a given group ID.
         * The method calculates the largest existing order value within a group
         * and increments it by one. If no records are found for the group, it
         * starts from zero.
         *
         * @param int $groupId The ID of the group for which the order is to be generated.
         *
         * @return int The next order value for the specified group.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function generateOrder(int $groupId): int
        {
            // Find the largest "order" value based on a given group ID
            $objBoard = Board::querySingle(
                QQ::equal(QQN::board()->BoardId, $groupId),
                [QQ::maximum(QQN::board()->Order, 'max_order')]
            );

            // Get the largest order value if it exists, otherwise start from zero
            $maxOrder = $objBoard ? $objBoard->getVirtualAttribute('max_order') : 0;

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
                // This will return an array of Board objects
                return Board::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Board()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::Board()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single Board object
                return Board::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Board()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::Board()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of Board objects
                return Board::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Board()->Param1, $strParam1),
                        QQ::Equal(QQN::Board()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = Board::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `board`.*
                    FROM
                        `board` AS `board`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return Board::instantiateDbResult($objDbResult);
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
