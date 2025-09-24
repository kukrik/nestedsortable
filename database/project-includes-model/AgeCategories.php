<?php

    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/AgeCategoriesGen.php');

    /**
     * The AgeCategories class defined here contains any
     * customized code for the AgeCategories class in the
     * Object Relational Model. It represents the "age_categories" table
     * in the database and extends from the code generated abstract AgeCategoriesGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class AgeCategories extends AgeCategoriesGen
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
            return t($this->getClassName());
        }

        /**
         * Sets the assigned editor's name by the provided identifier. If the editor's identifier does not match
         * the current assigned user, it checks whether the user is already associated as an editor. If not,
         * it associates the user as an editor with the given key.
         *
         * @param mixed $key The identifier of the user to be assigned as an editor.
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
         * Checks if a class exists in the AgeCategories table based on its name.
         *
         * @param string $className The name of the class to check for existence.
         *
         * @return bool True if the class exists, false otherwise.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function classExists(string $className): bool
        {
            $objCondition = QQ::Equal(QQN::AgeCategories()->ClassName, $className);
            $objArray = AgeCategories::QueryArray($objCondition);

            return count($objArray) > 0;
        }

        /**
         * Loads a single AgeCategories object based on its ID.
         * Executes a query to find an AgeCategories object with the specified ID.
         *
         * @param int $intId The ID of the AgeCategories object to load.
         * @param null|mixed $objOptionalClauses Optional clauses for the query, such as conditions or ordering.
         *
         * @return AgeCategories|null The AgeCategories object if found, otherwise null.
         * @throws Caller If there is an issue with the query or parameters.
         */
        public static function loadByIdFromAgeCategories(int $intId, mixed $objOptionalClauses = null): ?AgeCategories
        {
            // Call AgeCategories::QuerySingle to perform the loadByIdFromAgeCategories query
            try {
                return AgeCategories::QuerySingle(
                    QQ::Equal(QQN::AgeCategories()->Id, $intId),
                    $objOptionalClauses);
            } catch (Caller $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }
        }

        /**
         * Checks if there are any records where the MaxAge field is null.
         *
         * This method queries the AgeCategories table to determine if any entries
         * have a null value in the MaxAge field.
         *
         * @return bool true if there are existing records with a null MaxAge, false otherwise
         * @throws Caller
         * @throws InvalidCast
         */
        public static function hasExistingNullMaxAge(): bool
        {
            $list = AgeCategories::queryArray(
                QQ::isNull(QQN::AgeCategories()->MaxAge)
            );
            return count($list) > 0;
        }

        /**
         * Determines if the provided age is the maximum minimum age in the AgeCategories.
         *
         * This method checks if the specified minimum age matches the maximum minimum
         * age value in the AgeCategories. If no records exist in the AgeCategories,
         * the method will return false.
         *
         * @param int $intMinAge The minimum age to compare against the maximum minimum age.
         *
         * @return bool True if the provided minimum age matches the maximum minimum age, otherwise false.
         * @throws Caller
         */
        public static function isMaxMinAge(int $intMinAge): bool
        {
            $maxCategory = AgeCategories::querySingle(
                QQ::all(),
                [QQ::maximum(QQN::AgeCategories()->MinAge, 'MinAge')]
            );

            // If there are no rows, then there can be no largest
            if (!$maxCategory) {
                return false;
            }

            // Compare whether the parameter is maximum
            return ($maxCategory->MinAge === $intMinAge);
        }

        // NOTE: Remember that when introducing a new custom function,
        // you must specify types for the function parameters as well as for the function return type!

        // Override or Create New load/count methods
        // (For obvious reasons, these methods are commented out...
        // But feel free to use these as a starting point)
        /*

            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return an array of AgeCategories objects
                return AgeCategories::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::AgeCategories()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::AgeCategories()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single AgeCategories object
                return AgeCategories::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::AgeCategories()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::AgeCategories()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of AgeCategories objects
                return AgeCategories::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::AgeCategories()->Param1, $strParam1),
                        QQ::Equal(QQN::AgeCategories()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = AgeCategories::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `age_categories`.*
                    FROM
                        `age_categories` AS `age_categories`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return AgeCategories::instantiateDbResult($objDbResult);
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
