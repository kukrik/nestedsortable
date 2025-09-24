<?php

    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\QString;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/EventsCalendarGen.php');

    /**
     * The EventsCalendar class defined here contains any
     * customized code for the EventsCalendar class in the
     * Object Relational Model. It represents the "events_calendar" table
     * in the database and extends from the code generated abstract EventsCalendarGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class EventsCalendar extends EventsCalendarGen
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
            return $this->getAssignedByUserObject()->getFirstname() . ' ' .  $this->getAssignedByUserObject()->getLastName();
        }

        /**
         * Loads an EventsCalendar object based on a given MenuContentGroupId.
         *
         * @param int $intMenuContentId The MenuContentGroupId to search for.
         * @param null|mixed $objOptionalClauses Optional clauses for customizing the query (e.g., sorting, limiting).
         *
         * @return EventsCalendar|null The matching EventsCalendar object if found, or null if no match is found.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function loadByIdFromContentId(int $intMenuContentId, mixed $objOptionalClauses = null): ?EventsCalendar
        {
            // Use QuerySingle to Perform the Query
            return EventsCalendar::querySingle(
                QQ::AndCondition(
                    QQ::Equal(QQN::EventsCalendar()->MenuContentGroupId, $intMenuContentId)
                ), $objOptionalClauses
            );
        }

        /**
         * Sets the assigned editor's name by their ID. Checks if the provided ID matches the
         * current assigned editor's ID, and associates the user with the editor role if they are not
         * already associated.
         *
         * @param mixed $key The ID of the editor to set as assigned.
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
         * Checks whether a given slug already exists in the EventsCalendar database.
         * Optionally, an ID can be excluded from the check.
         *
         * @param string $slug The slug to check for existence.
         * @param mixed|null $excludeId Optional ID to exclude from the query.
         *
         * @return bool True if the slug exists, otherwise false.
         * @throws Caller
         * @throws InvalidCast
         */
        private static function slugExists(string $slug, mixed $excludeId = null): bool
        {
            $objCondition = QQ::AndCondition(
                QQ::Equal(QQN::EventsCalendar()->TitleSlug, $slug)
            );

            if ($excludeId) {
                $objCondition = QQ::AndCondition(
                    $objCondition,
                    QQ::NotEqual(QQN::EventsCalendar()->Id, $excludeId)
                );
            }

            $objNewsArray = EventsCalendar::queryArray($objCondition);
            return count($objNewsArray) > 0;
        }

        /**
         * @param string $baseSlug The initial slug to be checked for uniqueness.
         * @param mixed|null $excludeId An optional ID to exclude from the uniqueness check.
         *
         * @return string A unique slug, ensuring no conflicts with existing slugs.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function generateUniqueSlug(string $baseSlug, mixed $excludeId = null): string
        {
            // Let's check if the original slug exists
            if (!self::slugExists($baseSlug, $excludeId)) {
                return $baseSlug; // If a slug does not exist, we return immediately
            }

            $originalSlug = $baseSlug;
            $inc = 1;

            // If the slug already exists, we add the indexes
            while (self::slugExists($baseSlug, $excludeId)) {
                $baseSlug = $originalSlug . '-' . $inc;
                $inc++;
            }

            return $baseSlug;
        }

        /**
         * Saves an event with a generated unique slug based on the provided parameters.
         *
         * @param int $year The year associated with the event.
         * @param string $title The title of the event.
         * @param string $groupTitle The title of the group or category related to the event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function saveEvent(int $year, string $title, string $groupTitle): void
        {
            $newTitle = QString::sanitizeForUrl($title);
            $newSlug = $groupTitle . '/' .  $year. '/' . $newTitle;

            $uniqueSlug = self::generateUniqueSlug($newSlug);

            $this->TitleSlug = $uniqueSlug;
            $this->save();
        }

        /**
         * Updates the event details and regenerates the slug if necessary.
         * Modifies the event title, group title, and optionally updates
         * its position to generate a new unique slug and persist changes.
         *
         * @param int $year The year associated with the event.
         * @param string $title The title of the event to update.
         * @param string $groupTitle The title of the group the event belongs to.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function updateEvent(int $year, string $title, string $groupTitle): void
        {
            $newTitle = QString::sanitizeForUrl($title);
            $newSlug = $groupTitle . '/' .  $year. '/' . $newTitle;

            // If the new slug is the same as the current one, there is no need to regenerate
            if ($newSlug !== $this->TitleSlug) {
                $uniqueSlug = self::generateUniqueSlug($newSlug, $this->Id);
                $this->TitleSlug = $uniqueSlug;
                $this->save();
            }
        }

        // NOTE: Remember that when introducing a new custom function,
        // you must specify types for the function parameters as well as for the function return type!

        // Override or Create New load/count methods
        // (For obvious reasons, these methods are commented out...
        // But feel free to use these as a starting point)
        /*

            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return an array of EventsCalendar objects
                return EventsCalendar::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::EventsCalendar()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::EventsCalendar()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single EventsCalendar object
                return EventsCalendar::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::EventsCalendar()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::EventsCalendar()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of EventsCalendar objects
                return EventsCalendar::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::EventsCalendar()->Param1, $strParam1),
                        QQ::Equal(QQN::EventsCalendar()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = EventsCalendar::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `events_calendar`.*
                    FROM
                        `events_calendar` AS `events_calendar`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return EventsCalendar::instantiateDbResult($objDbResult);
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
