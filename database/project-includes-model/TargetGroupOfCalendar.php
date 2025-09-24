<?php

    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/TargetGroupOfCalendarGen.php');

    /**
     * The TargetGroupOfCalendar class defined here contains any
     * customized code for the TargetGroupOfCalendar class in the
     * Object Relational Model. It represents the "target_group_of_calendar" table
     * in the database and extends from the code generated abstract TargetGroupOfCalendarGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class TargetGroupOfCalendar extends TargetGroupOfCalendarGen
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
            $objCondition = QQ::Equal(QQN::TargetGroupOfCalendar()->Name, $title);
            $objGroupArray = TargetGroupOfCalendar::queryArray($objCondition);

            return count($objGroupArray) > 0;
        }

        /**
         * Retrieves the lock status of the object represented as a string with a specific format.
         * The status is determined based on the count of events associated with a target group ID.
         * If there are events, it returns a "Locked" status; otherwise, it returns a "Free" status.
         *
         * @return string a formatted string indicating the lock status of the object
         * @throws Caller
         * @throws InvalidCast
         */
        public function getLockStatusObject(): string
        {
            $count = EventsCalendar::countByTargetGroupId($this->Id);

            if ($count > 0) {
                return '<i class="fa fa-circle fa-lg" style="color:#ff0000;line-height:0.1;"></i> ' . t('Locked');
            } else {
                return '<i class="fa fa-circle fa-lg" style="color:#449d44;line-height:0.1;"></i> ' . t('Free');
            }
        }

        /**
         * Updates the lock states of all target groups in the database.
         * Target groups associated with events are marked as locked,
         * while those not associated with any events are marked as unlocked.
         *
         * @return void
         * @throws Caller
         */
        public static function updateAllTargetGroupLockStates(): void
        {
            $db = static::getDatabase();
            $db->NonQuery("
                UPDATE target_group_of_calendar 
                SET target_locked = 1 
                WHERE id IN (
                    SELECT DISTINCT target_group_id FROM events_calendar WHERE target_group_id IS NOT NULL
                )
            ");

            $db->NonQuery("
                UPDATE target_group_of_calendar 
                SET target_locked = 0
                WHERE id NOT IN (
                    SELECT DISTINCT target_group_id FROM events_calendar WHERE target_group_id IS NOT NULL
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
                // This will return an array of TargetGroupOfCalendar objects
                return TargetGroupOfCalendar::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::TargetGroupOfCalendar()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::TargetGroupOfCalendar()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single TargetGroupOfCalendar object
                return TargetGroupOfCalendar::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::TargetGroupOfCalendar()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::TargetGroupOfCalendar()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of TargetGroupOfCalendar objects
                return TargetGroupOfCalendar::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::TargetGroupOfCalendar()->Param1, $strParam1),
                        QQ::Equal(QQN::TargetGroupOfCalendar()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = TargetGroupOfCalendar::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `target_group_of_calendar`.*
                    FROM
                        `target_group_of_calendar` AS `target_group_of_calendar`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return TargetGroupOfCalendar::instantiateDbResult($objDbResult);
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
