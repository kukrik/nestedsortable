<?php

    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\QString;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/GalleryListGen.php');

    /**
     * The GalleryList class defined here contains any
     * customized code for the GalleryList class in the
     * Object Relational Model. It represents the "gallery_list" table
     * in the database and extends from the code generated abstract GalleryListGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class GalleryList extends GalleryListGen
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
            return $this->getTitle();
        }

        /**
         * Sets the assigned editor's name by their ID.
         * If the provided ID does not match the current assigned user's ID,
         * it ensures that the user associated with the given ID is assigned
         * as an editor.
         *
         * @param mixed $key The ID of the editor to assign.
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
         * Loads a list of gallery items based on the given gallery list ID.
         *
         * @param int $intId The ID of the gallery list to search for.
         * @param mixed $objOptionalClauses Additional optional clauses for the query.
         *
         * @return array An array of gallery items matching the specified ID.
         * @throws Caller If an error occurs during the query execution.
         */
        public static function loadByGalleryListId(int $intId, mixed $objOptionalClauses = null): array
        {
            // Call GalleryList::QueryArray to perform the loadByGalleryListId query
            try {
                return GalleryList::queryArray(
                    QQ::Equal(QQN::GalleryList()->GalleryGroupTitleId, $intId),
                    $objOptionalClauses);
            } catch (Caller $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }
        }

        /**
         * Loads a single GalleryList object based on the specified ID.
         * Executes a query to retrieve an object from the GalleryList table
         * where the MenuContentGroupId matches the provided ID.
         *
         * @param int $intId The ID of the GalleryList to load.
         * @param null|mixed $objOptionalClauses Optional query clauses to customize the query (e.g., sorting, conditions).
         *
         * @return GalleryList|null The loaded GalleryList object, or null if no match is found.
         * @throws Caller If an error occurs during the query execution.
         */
        public static function loadByIdFromGalleryList(int $intId, mixed $objOptionalClauses = null): ?GalleryList
        {
            // Call GalleryList::QueryArray to perform the loadByGalleryListId query
            try {
                return GalleryList::querySingle(
                    QQ::Equal(QQN::GalleryList()->MenuContentGroupId, $intId),
                    $objOptionalClauses);
            } catch (Caller $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }
        }

        /**
         * Checks if a given slug already exists in the database, with an option to exclude a specific ID.
         *
         * @param string $slug The slug to check for existence.
         * @param mixed|null $excludeId An optional ID to exclude from the check.
         *
         * @return bool True if the slug exists, false otherwise.
         * @throws Caller
         * @throws InvalidCast
         */
        private static function slugExists(string $slug, mixed $excludeId = null): bool
        {
            $objCondition = QQ::AndCondition(
                QQ::Equal(QQN::GalleryList()->TitleSlug, $slug)
            );

            if ($excludeId) {
                $objCondition = QQ::AndCondition(
                    $objCondition,
                    QQ::NotEqual(QQN::GalleryList()->Id, $excludeId)
                );
            }

            $objGalleryListArray = GalleryList::queryArray($objCondition);
            return count($objGalleryListArray) > 0;
        }

        /**
         * Generates a unique slug by appending an incrementing index if the base slug already exists.
         *
         * @param string $baseSlug The initial slug to be used as a base for uniqueness checks.
         * @param mixed|null $excludeId Optional ID to exclude from the uniqueness check, typically used when updating an existing record.
         *
         * @return string A unique slug that does not conflict with existing slugs.
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
         * Updates the album's title and corresponding slug. If the new slug differs
         * from the current one, a unique slug is generated, and the album is updated
         * with the new title and slug.
         *
         * @param string $title The new title for the album.
         * @param string $groupTitle The group's title to be included as part of the slug.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function updateAlbum(string $title, string $groupTitle): void
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
         * Checks if a title exists for a specific gallery group.
         *
         * @param int $galleryGroupId The ID of the gallery group to check.
         * @param string $title The title to validate the existence for within the specified gallery group.
         *
         * @return bool True if the title exists, otherwise false.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function titleExists(int $galleryGroupId, string $title): bool
        {
            $objCondition = QQ::AndCondition(
                QQ::Equal(QQN::GalleryList()->GalleryGroupTitleId, $galleryGroupId),
                QQ::Equal(QQN::GalleryList()->Title, $title)
            );

            $objGalleryListArray = GalleryList::queryArray($objCondition);

            return count($objGalleryListArray) > 0;
        }


        // NOTE: Remember that when introducing a new custom function,
        // you must specify types for the function parameters as well as for the function return type!

        // Override or Create New load/count methods
        // (For obvious reasons, these methods are commented out...
        // But feel free to use these as a starting point)
        /*

            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return an array of GalleryList objects
                return GalleryList::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::GalleryList()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::GalleryList()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single GalleryList object
                return GalleryList::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::GalleryList()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::GalleryList()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of GalleryList objects
                return GalleryList::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::GalleryList()->Param1, $strParam1),
                        QQ::Equal(QQN::GalleryList()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = GalleryList::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `gallery_list`.*
                    FROM
                        `gallery_list` AS `gallery_list`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return GalleryList::instantiateDbResult($objDbResult);
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
