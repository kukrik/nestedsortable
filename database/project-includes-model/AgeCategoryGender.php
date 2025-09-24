<?php

    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/AgeCategoryGenderGen.php');

    /**
     * The AgeCategoryGender class defined here contains any
     * customized code for the AgeCategoryGender class in the
     * Object Relational Model. It represents the "age_category_gender" table
     * in the database and extends from the code generated abstract AgeCategoryGenderGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class AgeCategoryGender extends AgeCategoryGenderGen
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
            return 'AgeCategoryGender Object ' . $this->PrimaryKey();
        }

        /**
         * Retrieves the ID of an AgeCategoryGender object based on the provided Age Category ID and Gender ID.
         *
         * @param int $intAgeCategoryId The ID of the Age Category to match.
         * @param int $intGenderId The ID of the Gender to match.
         *
         * @return int|null The ID of the matching AgeCategoryGender object if found, or null if no match is found.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function getIdByPair(int $intAgeCategoryId, int $intGenderId): ?int
        {
            $objCondition = QQ::andCondition(
                QQ::equal(QQN::AgeCategoryGender()->AgeCategoryId, $intAgeCategoryId),
                QQ::equal(QQN::AgeCategoryGender()->AthleteGenderId, $intGenderId)
            );

            $objResult = AgeCategoryGender::querySingle($objCondition);

            return $objResult?->Id;
        }

        /**
         * Assigns an editor's name to a specific ID if it is not already assigned or associated.
         * This method checks if the provided key matches the current assigned editor's ID,
         * and ensures the editor is associated with the given key if not already associated.
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
                if (!$this->isUserAsEditorsAssociatedByKey($key)) {
                    $this->associateUserAsEditorsByKey($key);
                }
            }
        }

        // NOTE: Remember that when introducing a new custom function,
        // you must specify types for the function parameters as well as for the function return type!

        // Override or Create New load/count methods
        // (For obvious reasons, these methods are commented out...
        // But feel free to use these as a starting point)
        /*

            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return an array of AgeCategoryGender objects
                return AgeCategoryGender::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::AgeCategoryGender()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::AgeCategoryGender()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single AgeCategoryGender object
                return AgeCategoryGender::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::AgeCategoryGender()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::AgeCategoryGender()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of AgeCategoryGender objects
                return AgeCategoryGender::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::AgeCategoryGender()->Param1, $strParam1),
                        QQ::Equal(QQN::AgeCategoryGender()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = AgeCategoryGender::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `age_category_gender`.*
                    FROM
                        `age_category_gender` AS `age_category_gender`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return AgeCategoryGender::instantiateDbResult($objDbResult);
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
