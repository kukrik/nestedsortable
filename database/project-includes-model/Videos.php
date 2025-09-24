<?php

    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/VideosGen.php');

    /**
     * The Videos class defined here contains any
     * customized code for the Videos class in the
     * Object Relational Model. It represents the "videos" table
     * in the database and extends from the code generated abstract VideosGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class Videos extends VideosGen
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
            return 'Videos Object ' . $this->primaryKey();
        }

        /**
         * Loads a Videos object by the given Settings ID.
         * Executes a query to fetch a single Videos object based on the provided Settings ID.
         *
         * @param int $intId The Settings ID to search for.
         * @param null|mixed $objOptionalClauses Optional clauses for customizing the query.
         *
         * @return Videos|null The Videos object matched by the query or null if no match is found.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function loadByIdFromVideosId(int $intId, mixed $objOptionalClauses = null): ?Videos
        {
            // Use QuerySingle to Perform the Query
            return Videos::querySingle(
                QQ::AndCondition(
                    QQ::Equal(QQN::Videos()->SettingsId, $intId)
                ), $objOptionalClauses
            );
        }

        /**
         * Generates the next order value based on the largest "order" value within a specified group.
         *
         * @param int $groupId The ID of the group for which the order value is being generated.
         *
         * @return int The next incremental order value for the specified group.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function generateOrder(int $groupId): int
        {
            // Find the largest "order" value based on a given group ID
            $objVideo = Videos::querySingle(
                QQ::equal(QQN::Videos()->SettingsId, $groupId),
                [QQ::maximum(QQN::Videos()->Order, 'max_order')]
            );

            // Get the largest order value if it exists, otherwise start from zero
            $maxOrder = $objVideo ? $objVideo->getVirtualAttribute('max_order') : 0;

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
                // This will return an array of Videos objects
                return Videos::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Videos()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::Videos()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single Videos object
                return Videos::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Videos()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::Videos()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of Videos objects
                return Videos::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Videos()->Param1, $strParam1),
                        QQ::Equal(QQN::Videos()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = Videos::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `videos`.*
                    FROM
                        `videos` AS `videos`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return Videos::instantiateDbResult($objDbResult);
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
