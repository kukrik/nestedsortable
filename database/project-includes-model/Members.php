<?php

    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/MembersGen.php');

    /**
     * The Members class defined here contains any
     * customized code for the Members class in the
     * Object Relational Model. It represents the "members" table
     * in the database and extends from the code generated abstract MembersGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class Members extends MembersGen
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
            return 'Members Object ' . $this->primaryKey();
        }

        /**
         * Loads a Members object based on the given MemberId.
         * Performs a database query to retrieve a single Members object
         * matching the specified criteria.
         *
         * @param int $intId The ID of the member to be loaded.
         * @param null|mixed $objOptionalClauses Optional clauses for the query such as filters or ordering.
         *
         * @return Members|null The loaded Members object, or null if no matching record is found.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function loadByIdFromMemberId(int $intId, mixed $objOptionalClauses = null): ?Members
        {
            // Use QuerySingle to Perform the Query
            return Members::querySingle(
                QQ::AndCondition(
                    QQ::Equal(QQN::Members()->MemberId, $intId)
                ), $objOptionalClauses
            );
        }

        /**
         * Generates the next order value for a given group ID.
         * Determines the largest existing "order" value in the group and increments it.
         *
         * @param int $groupId The ID of the group for which the order value should be generated.
         *
         * @return int The next order value based on the largest existing order value in the group.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function generateOrder(int $groupId): int
        {
            // Find the largest "order" value based on a given group ID
            $objMember = Members::querySingle(
                QQ::equal(QQN::Members()->MemberId, $groupId),
                [QQ::maximum(QQN::Members()->Order, 'max_order')]
            );

            // Get the largest order value if it exists, otherwise start from zero
            $maxOrder = $objMember ? $objMember->getVirtualAttribute('max_order') : 0;

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
                // This will return an array of Members objects
                return Members::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Members()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::Members()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single Members object
                return Members::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Members()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::Members()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of Members objects
                return Members::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Members()->Param1, $strParam1),
                        QQ::Equal(QQN::Members()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = Members::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `members`.*
                    FROM
                        `members` AS `members`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return Members::instantiateDbResult($objDbResult);
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
