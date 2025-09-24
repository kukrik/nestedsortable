<?php

    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/GendersGen.php');

    /**
     * The Genders class defined here contains any
     * customized code for the Genders class in the
     * Object Relational Model. It represents the "genders" table
     * in the database and extends from the code generated abstract GendersGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class Genders extends GendersGen
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
            return t($this->getName());
        }

        /**
         * Sets the assigned editor's name based on the provided ID. If the ID does not match
         * the current assigned editor and the user is not yet associated as an editor
         * by the given ID, it associates the user as an editor by the specified ID.
         *
         * @param mixed $key The ID used to identify and associate the user as an editor.
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
         * Checks if a name exists in the Genders database.
         *
         * @param string $name The name to check for existence.
         *
         * @return bool True if the name exists, false otherwise.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function nameExists(string $name): bool
        {
            $objCondition = QQ::Equal(QQN::Genders()->Name, $name);
            $objArray = Genders::queryArray($objCondition);

            return count($objArray) > 0;
        }

        /**
         * Loads a single Genders object by its ID.
         * This method queries the database for a Genders object with the specified ID,
         * optionally applying additional clauses to refine the query.
         *
         * @param int $intId The ID of the Genders object to retrieve.
         * @param null|mixed $objOptionalClauses Optional query clauses to refine the query.
         *
         * @return Genders|null The Genders object for the specified ID, or null if no match is found.
         * @throws Caller If an error occurs while executing the query.
         */
        public static function loadByIdFromGenders(int $intId, mixed $objOptionalClauses = null): ?Genders
        {
            // Call Genders::QuerySingle to perform the loadByIdFromGenders query
            try {
                return Genders::querySingle(
                    QQ::Equal(QQN::Genders()->Id, $intId),
                    $objOptionalClauses);
            } catch (Caller $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }
        }

        // NOTE: Remember that when introducing a new custom function,
        // you must specify types for the function parameters as well as for the function return type!

        // Override or Create New load/count methods
        // (For obvious reasons, these methods are commented out...
        // But feel free to use these as a starting point)
        /*

            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return an array of Genders objects
                return Genders::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Genders()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::Genders()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single Genders object
                return Genders::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Genders()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::Genders()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of Genders objects
                return Genders::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Genders()->Param1, $strParam1),
                        QQ::Equal(QQN::Genders()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = Genders::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `genders`.*
                    FROM
                        `genders` AS `genders`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return Genders::instantiateDbResult($objDbResult);
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
