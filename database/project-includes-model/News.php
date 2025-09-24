<?php

    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\QString;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/NewsGen.php');

    /**
     * The News class defined here contains any
     * customized code for the News class in the
     * Object Relational Model. It represents the "news" table
     * in the database and extends from the code generated abstract NewsGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class News extends NewsGen
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
         * Counts the number of News entries associated with a specific NewsChangesId.
         *
         * @param int $intChangesId The ID representing the changes related to news entries.
         *
         * @return int The total count of news entries linked to the provided NewsChangesId.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function countByNewsChangesId(int $intChangesId): int
        {
            // Call News::countByNewsChangesId to perform the countByNewsChangesId query
            return News::queryCount(
                QQ::Equal(QQN::News()->ChangesId, $intChangesId)
            );
        }

        /**
         * Loads a CategoryOfNews object by its ID from a specific category.
         * Performs a query to retrieve the object matching the provided criteria.
         *
         * @param integer $intNewsCategoryId The ID of the news category to look up.
         * @param null|mixed $objOptionalClauses Optional clauses to apply to the query.
         *
         * @return CategoryOfNews|null The CategoryOfNews object if found, or null if no match is found.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function loadByIdFromCategory(int $intNewsCategoryId, mixed $objOptionalClauses = null): ?CategoryOfNews
        {
            // Use querySingle to Perform the Query
            return CategoryOfNews::querySingle(
                QQ::AndCondition(
                    QQ::Equal(QQN::CategoryOfNews()->Id, $intNewsCategoryId)
                ), $objOptionalClauses
            );
        }

        /**
         * Sets the assigned editor's name by their unique identifier.
         * Verifies whether the current assigned editor ID matches the provided key.
         * If they do not match and the editor is not yet associated with the key,
         * it associates the user as an editor using the given key.
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
         * Checks if a given slug already exists in the News entries.
         * Optionally excludes a specific entry by its ID.
         *
         * @param string $slug The slug to check for existence.
         * @param null|mixed $excludeId An optional ID to exclude from the check.
         *
         * @return bool True if the slug exists, false otherwise.
         * @throws Caller
         * @throws InvalidCast
         */
        private static function slugExists(string $slug, mixed $excludeId = null): bool
        {
            $objCondition = QQ::AndCondition(
                QQ::Equal(QQN::News()->TitleSlug, $slug)
            );

            if ($excludeId) {
                $objCondition = QQ::AndCondition(
                    $objCondition,
                    QQ::NotEqual(QQN::News()->Id, $excludeId)
                );
            }

            $objNewsArray = News::queryArray($objCondition);
            return count($objNewsArray) > 0;
        }

        /**
         * Generates a unique slug by appending an incrementing index if the given slug already exists.
         *
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
         * Saves a news item with a sanitized and unique slug.
         * Prepares the title to be safely used in a URL format and ensures the slug is unique.
         *
         * @param string $title The title of the news item to be saved.
         * @param string $groupTitle The group or category title used as a prefix for the slug.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function saveNews(string $title, string $groupTitle): void
        {
            $newTitle = QString::sanitizeForUrl($title);
            $newSlug = $groupTitle . '/' . $newTitle;

            $uniqueSlug = self::generateUniqueSlug($newSlug);

            $this->TitleSlug = $uniqueSlug;
            $this->Title = $title;
            $this->save();
        }

        /**
         * Updates the news title and slug based on the provided parameters.
         * Regenerates a unique slug if the new one differs from the existing title slug.
         *
         * @param string $title The new title for the news item.
         * @param string $groupTitle The group title to be used as part of the slug.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function updateNews(string $title, string $groupTitle): void
        {
            $newTitle = QString::sanitizeForUrl($title);
            $newSlug = $groupTitle . '/' . $newTitle;

            // If the new slug is the same as the current one, there is no need to regenerate
            if ($newSlug !== $this->TitleSlug) {
                $uniqueSlug = self::generateUniqueSlug($newSlug, $this->Id);
                $this->TitleSlug = $uniqueSlug;
                $this->Title = $title;
                $this->save();
            }
        }

        /**
         * Loads front page news that is public (status=1).
         * Sorts first by update date (if any), then by posting date.
         *
         * @return array
         * @throws Caller
         * @throws InvalidCast
         */
        public static function loadFrontPageNews(): array
        {
            return News::queryArray(
                QQ::Equal(QQN::News()->Status, 1),
                [
                    QQ::orderBy(QQ::subSql("
                    CASE
                        WHEN changes_id IS NOT NULL AND changes_id != '' THEN post_update_date
                        ELSE post_date
                    END DESC
                "))
                ]
            );
        }

        // NOTE: Remember that when introducing a new custom function,
        // you must specify types for the function parameters as well as for the function return type!

        // Override or Create New load/count methods
        // (For obvious reasons, these methods are commented out...
        // But feel free to use these as a starting point)
        /*

            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return an array of News objects
                return News::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::News()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::News()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single News object
                return News::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::News()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::News()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of News objects
                return News::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::News()->Param1, $strParam1),
                        QQ::Equal(QQN::News()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = News::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `news`.*
                    FROM
                        `news` AS `news`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return News::instantiateDbResult($objDbResult);
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
