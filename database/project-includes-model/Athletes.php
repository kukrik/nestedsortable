<?php

    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\QDateTime;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/AthletesGen.php');

    /**
     * The Athletes class defined here contains any
     * customized code for the Athletes class in the
     * Object Relational Model. It represents the "athletes" table
     * in the database and extends from the code generated abstract AthletesGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class Athletes extends AthletesGen
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
            return $this->getFirstName() . ' ' . $this->getLastName();
        }

        /**
         * Assigns an editor's name to the instance by their ID if conditions are met.
         * Checks if the provided ID matches the current assigned editor's ID. If not, and if the editor
         * is not already associated with the given key, associates the editor by the provided key.
         *
         * @param mixed $key Identifier of the editor to be assigned.
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
         * Checks if an athlete with the given first name, last name, and optionally birthdate exists in the database.
         *
         * @param string $firstName The first name of the athlete to search for.
         * @param string $lastName The last name of the athlete to search for.
         * @param QDateTime|null $birthDate An optional birthdate of the athlete. If provided, it will also be used in the search.
         *
         * @return bool True if a matching athlete exists, false otherwise.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function namesExists(string $firstName, string $lastName, ?QDateTime $birthDate = null): bool
        {
            $arrCond = [
                QQ::Equal(QQN::Athletes()->FirstName, $firstName),
                QQ::Equal(QQN::Athletes()->LastName, $lastName)
            ];

            if ($birthDate !== null) {
                $arrCond[] = QQ::Equal(QQN::Athletes()->BirthDate, $birthDate);
            }

            $objCondition = QQ::AndCondition(...$arrCond);

            $objNamesArray = Athletes::QueryArray($objCondition);

            return count($objNamesArray) > 0;
        }


        /**
         * Retrieves the lock status of the object in a formatted string representation that includes
         * an icon and corresponding label for "Locked" or "Free".
         *
         * @return string the formatted lock status with an icon and text
         */
        public function getLockStatusObject(): string
        {
            if ($this->IsLocked == 2) {
                return '<i class="fa fa-circle fa-lg" style="color:#ff0000;line-height:0.1;"></i> ' . t('Locked');
            } else {
                return '<i class="fa fa-circle fa-lg" style="color:#449d44;line-height:0.1;"></i> ' . t('Free');
            }
        }

        // NOTE: Remember that when introducing a new custom function,
        // you must specify types for the function parameters as well as for the function return type!

        // Override or Create New load/count methods
        // (For obvious reasons, these methods are commented out...
        // But feel free to use these as a starting point)
        /*

            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return an array of Athletes objects
                return Athletes::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Athletes()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::Athletes()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single Athletes object
                return Athletes::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Athletes()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::Athletes()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of Athletes objects
                return Athletes::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Athletes()->Param1, $strParam1),
                        QQ::Equal(QQN::Athletes()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = Athletes::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `athletes`.*
                    FROM
                        `athletes` AS `athletes`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return Athletes::instantiateDbResult($objDbResult);
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
