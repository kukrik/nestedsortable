<?php

    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/SportsAreasCompetitionAreasGen.php');

    /**
     * The SportsAreasCompetitionAreas class defined here contains any
     * customized code for the SportsAreasCompetitionAreas class in the
     * Object Relational Model. It represents the "sports_areas_competition_areas" table
     * in the database and extends from the code generated abstract SportsAreasCompetitionAreasGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class SportsAreasCompetitionAreas extends SportsAreasCompetitionAreasGen
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
            return $this->getSportsAreasId();
        }

        /**
         * Checks if a pairing between a sport area and a competition area exists.
         *
         * @param string $sportArea The name of the sport area to check.
         * @param string $competitionArea The name of the competition area to check.
         *
         * @return bool True if the pairing exists, false otherwise.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function pairExists(string $sportArea, string $competitionArea): bool
        {
            $objCondition = QQ::andCondition(
                QQ::equal(QQN::SportsAreasCompetitionAreas()->SportsAreasId, $sportArea),
                QQ::equal(QQN::SportsAreasCompetitionAreas()->SportsCompetitionAreasId, $competitionArea)
            );

            return SportsAreasCompetitionAreas::queryCount($objCondition) > 0;
        }

        /**
         * Retrieves the ID associated with the given pair of sport area ID and competition area ID.
         *
         * @param int $sportAreaId The ID of the sport area.
         * @param int $competitionAreaId The ID of the competition area.
         *
         * @return int|null The ID corresponding to the pair if found, or null if no match is found.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function getIdByPair(int $sportAreaId, int $competitionAreaId): ?int
        {
            $objCondition = QQ::andCondition(
                QQ::equal(QQN::SportsAreasCompetitionAreas()->SportsAreasId, $sportAreaId),
                QQ::equal(QQN::SportsAreasCompetitionAreas()->SportsCompetitionAreasId, $competitionAreaId)
            );

            $objResult = SportsAreasCompetitionAreas::querySingle($objCondition);

            return $objResult?->Id;
        }

        /**
         * Loads a single SportsAreasCompetitionAreas object based on the SportsAreasId.
         *
         * @param int $sportsAreasId The ID of the sports area to retrieve the competition area for.
         * @param mixed|null $objOptionalClauses Additional optional clauses for the query.
         *
         * @return SportsAreasCompetitionAreas|null The loaded SportsAreasCompetitionAreas object if found, or null if not found.
         * @throws Caller If an exception occurs during the query execution.
         */
        public static function loadByIdFromSportsAreasCompetitionAreas(int $sportsAreasId, mixed $objOptionalClauses = null): ?SportsAreasCompetitionAreas
        {
            // Call SportsAreasCompetitionAreas::QuerySingle to perform the loadByIdFromSportsAreasCompetitionAreas query
            try {
                return SportsAreasCompetitionAreas::querySingle(
                    QQ::Equal(QQN::SportsAreasCompetitionAreas()->Id, $sportsAreasId),
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
                // This will return an array of SportsAreasCompetitionAreas objects
                return SportsAreasCompetitionAreas::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::SportsAreasCompetitionAreas()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::SportsAreasCompetitionAreas()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single SportsAreasCompetitionAreas object
                return SportsAreasCompetitionAreas::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::SportsAreasCompetitionAreas()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::SportsAreasCompetitionAreas()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of SportsAreasCompetitionAreas objects
                return SportsAreasCompetitionAreas::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::SportsAreasCompetitionAreas()->Param1, $strParam1),
                        QQ::Equal(QQN::SportsAreasCompetitionAreas()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = SportsAreasCompetitionAreas::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `sports_areas_competition_areas`.*
                    FROM
                        `sports_areas_competition_areas` AS `sports_areas_competition_areas`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return SportsAreasCompetitionAreas::instantiateDbResult($objDbResult);
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
