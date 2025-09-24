<?php

    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/OrganizingInstitutionGen.php');

    /**
     * The OrganizingInstitution class defined here contains any
     * customized code for the OrganizingInstitution class in the
     * Object Relational Model. It represents the "organizing_institution" table
     * in the database and extends from the code generated abstract OrganizingInstitutionGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class OrganizingInstitution extends OrganizingInstitutionGen
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
            return $this->getName();
        }

        /**
         * Checks if the given title exists in the database.
         *
         * @param string $title The title to check
         *
         * @return bool True if the title exists, False otherwise
         * @throws Caller
         * @throws InvalidCast
         */
        public static function titleExists(string $title): bool
        {
            $objCondition = QQ::Equal(QQN::OrganizingInstitution()->Name, $title);
            $objChangesArray = OrganizingInstitution::queryArray($objCondition);

            return count($objChangesArray) > 0;
        }

        /**
         * Updates the "is_locked" state for all organizing institutions based on the presence of associated records
         * in the sports_calendar table. Institutions with associated records are locked with a value of 2, while those
         * without associated records are locked with a value of 1.
         *
         * @return void
         * @throws Caller
         */
        public static function updateAllIsLockStates(): void
        {
            $db = static::getDatabase();
            $db->NonQuery("
                UPDATE organizing_institution 
                SET is_locked = 2 
                WHERE id IN (
                    SELECT DISTINCT organizing_institution_id FROM sports_calendar WHERE organizing_institution_id IS NOT NULL
                )
            ");

            $db->NonQuery("
                UPDATE organizing_institution  
                SET is_locked = 1
                WHERE id NOT IN (
                    SELECT DISTINCT organizing_institution_id FROM sports_calendar WHERE organizing_institution_id IS NOT NULL
                )
            ");
        }

        // NOTE: Remember that when introducing a new custom function,
        // you must specify types for the function parameters as well as for the function return type!

        // Override or Create New load/count methods
        // (For obvious reasons, these methods are commented out...
        // But feel free to use these as a starting point)
        /*

            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return an array of OrganizingInstitution objects
                return OrganizingInstitution::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::OrganizingInstitution()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::OrganizingInstitution()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single OrganizingInstitution object
                return OrganizingInstitution::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::OrganizingInstitution()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::OrganizingInstitution()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of OrganizingInstitution objects
                return OrganizingInstitution::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::OrganizingInstitution()->Param1, $strParam1),
                        QQ::Equal(QQN::OrganizingInstitution()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = OrganizingInstitution::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `organizing_institution`.*
                    FROM
                        `organizing_institution` AS `organizing_institution`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return OrganizingInstitution::instantiateDbResult($objDbResult);
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
