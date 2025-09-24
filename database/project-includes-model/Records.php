<?php

    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/RecordsGen.php');

    /**
     * The Records class defined here contains any
     * customized code for the Records class in the
     * Object Relational Model. It represents the "records" table
     * in the database and extends from the code generated abstract RecordsGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class Records extends RecordsGen
    {
        /**
         * Default "to string" handler
         * Allows pages to _p()/echo()/print() this object, and to define the default
         * way this object would be outputted.
         *
         * @return string a nicely formatted string representation of this object
         * @throws Caller
         * @throws InvalidCast
         */
        public function __toString(): string
        {
            return $this->getAthlete();
        }

        /**
         * Sets the assigned editor's name by the given ID. If the provided ID does not
         * match the current assigned user or the association does not already exist,
         * it associates the user as an editor by the given key.
         *
         * @param mixed $key The unique identifier of the editor to be associated.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         */
        public function setAssignedEditorsNameById(mixed $key): void
        {
            if ($this->getAssignedByUserObject()->Id !== $key) {
                if (!$this->isUserAsEditorsAssociatedByKey($key)) {
                    $this->associateUserAsEditorsByKey($key);
                }
            }
        }

        /**
         * Checks if a record exists in the database based on the specified criteria.
         *
         * @param int $intGenderId The ID representing the athlete's gender.
         * @param int $intCompetitionAreaId The ID representing the competition area.
         * @param null|int $intAgeCategoryId (Optional) The ID representing the age category. Default is null.
         *
         * @return bool True if a record matching the criteria exists, false otherwise.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function recordExists(int $intGenderId, int $intCompetitionAreaId, ?int $intAgeCategoryId = null): bool
        {
            // We create conditions for filtering
            $objCondition = QQ::andCondition(
                QQ::equal(QQN::Records()->AthleteGenderId, $intGenderId),
                QQ::equal(QQN::Records()->CompetitionAreaId, $intCompetitionAreaId),
                QQ::equal(QQN::Records()->AgeCategoryId, $intAgeCategoryId)
            );

            //We check whether there is at least one record in the database, according to the conditions
            $recordCount = Records::queryCount($objCondition);

            // We return a boolean value according to the list of records
            return $recordCount > 0;
        }

        // NOTE: Remember that when introducing a new custom function,
        // you must specify types for the function parameters as well as for the function return type!

        // Override or Create New load/count methods
        // (For obvious reasons, these methods are commented out...
        // But feel free to use these as a starting point)
        /*

            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return an array of Records objects
                return Records::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Records()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::Records()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single Records object
                return Records::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Records()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::Records()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of Records objects
                return Records::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Records()->Param1, $strParam1),
                        QQ::Equal(QQN::Records()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = Records::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `records`.*
                    FROM
                        `records` AS `records`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return Records::instantiateDbResult($objDbResult);
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
