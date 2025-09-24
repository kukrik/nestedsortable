<?php

    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\QString;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/SportsCalendarGen.php');

    /**
     * The SportsCalendar class defined here contains any
     * customized code for the SportsCalendar class in the
     * Object Relational Model. It represents the "sports_calendar" table
     * in the database and extends from the code generated abstract SportsCalendarGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class SportsCalendar extends SportsCalendarGen
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
         * Loads a SportsCalendar object based on the provided MenuContentId.
         * Executes a query to retrieve the object that matches the given identifier,
         * optionally applying additional clauses.
         *
         * @param int $intMenuContentId The ID of the menu content to search for.
         * @param null|mixed $objOptionalClauses Optional clauses to customize the query.
         *
         * @return SportsCalendar|null The matching SportsCalendar object, or null if no match is found.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function loadByIdFromContentId(int $intMenuContentId, mixed $objOptionalClauses = null): ?SportsCalendar
        {
            // Use querySingle to Perform the Query
            return SportsCalendar::querySingle(
                QQ::AndCondition(
                    QQ::Equal(QQN::SportsCalendar()->MenuContentGroupId, $intMenuContentId)
                ), $objOptionalClauses
            );
        }

        /**
         * Sets the assigned editor's name by their ID.
         * Checks if the given key corresponds to the current assigned editor. If not, it verifies
         * whether the user is already associated with an editor. If not associated, it associates
         * the user with the given key as an editor.
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

        /**
         * Checks whether a given slug already exists in the EventsCalendar database.
         * Optionally, an ID can be excluded from the check.
         *
         * @param string $slug The slug to check for existence.
         * @param null|mixed $excludeId Optional ID to exclude from the query.
         *
         * @return bool True if the slug exists, otherwise false.
         * @throws Caller
         * @throws InvalidCast
         */
        private static function slugExists(string $slug, mixed $excludeId = null): bool
        {
            $objCondition = QQ::AndCondition(
                QQ::Equal(QQN::SportsCalendar()->TitleSlug, $slug)
            );

            if ($excludeId) {
                $objCondition = QQ::AndCondition(
                    $objCondition,
                    QQ::NotEqual(QQN::SportsCalendar()->Id, $excludeId)
                );
            }

            $objNewsArray = SportsCalendar::queryArray($objCondition);
            return count($objNewsArray) > 0;
        }

        /**
         * @param string $baseSlug The initial slug to be checked for uniqueness.
         * @param null|mixed $excludeId An optional ID to exclude from the uniqueness check.
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
         * Saves a sports event with a unique slug based on the provided year, title, and group title.
         * The method generates a sanitized and unique TitleSlug for the sports event and saves it.
         *
         * @param int $year The year of the sports event.
         * @param string $title The title of the sports event.
         * @param string $groupTitle The group title or category for the sports event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function saveSportsEvent(int $year, string $title, string $groupTitle): void
        {
            $newTitle = QString::sanitizeForUrl($title);
            $newSlug = $groupTitle . '/' .  $year. '/' . $newTitle;

            $uniqueSlug = self::generateUniqueSlug($newSlug);

            $this->TitleSlug = $uniqueSlug;
            $this->save();
        }

        /**
         * Updates the sports event's title slug based on the given year, title, and group title.
         * Generates a new unique slug if the new slug differs from the current one.
         *
         * @param int $year The year of the sports event.
         * @param string $title The title of the sports event.
         * @param string $groupTitle The group title to associate with the event.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function updateSportsEvent(int $year, string $title, string $groupTitle): void
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
                // This will return an array of SportsCalendar objects
                return SportsCalendar::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::SportsCalendar()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::SportsCalendar()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single SportsCalendar object
                return SportsCalendar::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::SportsCalendar()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::SportsCalendar()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of SportsCalendar objects
                return SportsCalendar::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::SportsCalendar()->Param1, $strParam1),
                        QQ::Equal(QQN::SportsCalendar()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = SportsCalendar::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `sports_calendar`.*
                    FROM
                        `sports_calendar` AS `sports_calendar`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return SportsCalendar::instantiateDbResult($objDbResult);
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
