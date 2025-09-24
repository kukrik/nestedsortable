<?php

    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\QString;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/MenuContentGen.php');

    /**
     * The MenuContent class defined here contains any
     * customized code for the MenuContent class in the
     * Object Relational Model. It represents the "menu_content" table
     * in the database and extends from the code generated abstract MenuContentGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class MenuContent extends MenuContentGen
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
            return $this->getMenuText();
        }

        /**
         * Generates a hierarchical representation of the menu structure based on its depth.
         * Formats the output with a specific spacer for better visual representation.
         *
         * @return string a formatted string representing the menu hierarchy
         * @throws Caller
         * @throws InvalidCast
         */
        public function printHierarchy(): string
        {
            $spacer = html_entity_decode('&nbsp;'); //'&mdash;'); //'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');

            $strHtml = ($this->getMenu()->getParentId() == null) ? '' : str_repeat($spacer, $this->getMenu()->getDepth() * 3);
            $strHtml .= ' ' . $this->getMenuText();
            return $strHtml;
        }

        /**
         * Loads a MenuContent object by its ID from the menu content table.
         *
         * @param int $intId The unique identifier of the MenuContent object to load.
         * @param null|mixed $objOptionalClauses Optional clauses for the query, such as conditions or ordering.
         *
         * @return MenuContent|null The MenuContent object if found, or null if not found.
         * @throws Caller If an error occurs during execution, such as invalid query conditions.
         */
        public static function loadByIdFromMenuContent(int $intId, mixed $objOptionalClauses = null): ?MenuContent
        {
            // Call MenuContent::querySingle to perform the loadByIdFromMenuContent query
            try {
                return MenuContent::querySingle(
                    QQ::Equal(QQN::MenuContent()->Id, $intId),
                    $objOptionalClauses);
            } catch (Caller $objExc) {
                $objExc->incrementOffset();
                throw $objExc;
            }
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
            $objCondition = QQ::Equal(QQN::MenuContent()->MenuText, $title);
            $objMenuContentArray = MenuContent::queryArray($objCondition);

            return count($objMenuContentArray) > 0;
        }

        /**
         * Checks if the given content type exists in the database.
         *
         * @param string $intContentType The content type to check
         *
         * @return bool True if the content type exists, False otherwise
         * @throws Caller
         * @throws InvalidCast
         */
        public static function contentTypeExists(string $intContentType): bool
        {
            $objCondition = QQ::Equal(QQN::MenuContent()->ContentType, $intContentType);
            $objMenuContentArray = MenuContent::queryArray($objCondition);

            return count($objMenuContentArray) > 0;
        }

        /**
         * Checks if a given slug exists in the database, optionally excluding a specific ID.
         *
         * @param string $slug The slug to check for existence.
         * @param null|mixed $excludeId Optional ID to exclude from the check.
         *
         * @return bool True if the slug exists, otherwise false.
         * @throws Caller
         * @throws InvalidCast
         */
        private static function slugExists(string $slug, mixed $excludeId = null): bool
        {
            $objCondition = QQ::AndCondition(
                QQ::Equal(QQN::MenuContent()->RedirectUrl, $slug)
            );

            if ($excludeId) {
                $objCondition = QQ::AndCondition(
                    $objCondition,
                    QQ::NotEqual(QQN::MenuContent()->Id, $excludeId)
                );
            }

            $objMenuContentArray  = MenuContent::queryArray($objCondition);
            return count($objMenuContentArray ) > 0;
        }

        /**
         * Generates a unique slug by appending an incrementing number if the base slug already exists.
         *
         * @param string $baseSlug The initial slug that needs to be made unique.
         * @param null|mixed $excludeId An optional ID to exclude from the slug existence check.
         *
         * @return string A unique slug that does not conflict with any existing ones.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function generateUniqueSlug(string $baseSlug, mixed $excludeId = null): string
        {
            // Let's check if the original slug exists
            if (!self::slugExists($baseSlug, $excludeId)) {
                return $baseSlug; // If slug does not exist, we return immediately
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
         * Updates the menu content by modifying the title and the redirect URL slug.
         * Generates a unique slug if the new slug differs from the current one.
         *
         * @param string $title The new title of the menu item.
         * @param string $groupTitle The parent or group identifier to use when forming the slug.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function updateMenuContent(string $title, string $groupTitle): void
        {
            $newTitle = QString::sanitizeForUrl($title);
            $newSlug = $groupTitle . '/' . $newTitle;

            // If the new slug is the same as the current one, there is no need to regenerate
            if ($newSlug !== $this->RedirectUrl) {
                $uniqueSlug = self::generateUniqueSlug($newSlug, $this->Id);
                $this->RedirectUrl = $uniqueSlug;
                $this->Title = $title;
                $this->save();
            }
        }

        /**
         * Loads a MenuContent object based on the given SelectedPage ID.
         * Executes a query to retrieve the menu content associated with the specified page ID.
         *
         * @param int $intId The ID of the selected page to load the associated MenuContent object.
         * @param null|mixed $objOptionalClauses Optional clauses to customize the query.
         *
         * @return MenuContent|null The MenuContent object if found, or null if no matching record is found.
         * @throws Caller If an exception occurs during query execution.
         */
        public static function loadByIdFromSelectedPage(int $intId, mixed $objOptionalClauses = null): ?MenuContent
        {
            // Call MenuContent::querySingle to perform the loadByIdFromSelectedPage query
            try {
                return MenuContent::querySingle(
                    QQ::Equal(QQN::MenuContent()->SelectedPageId, $intId),
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
                // This will return an array of MenuContent objects
                return MenuContent::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::MenuContent()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::MenuContent()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single MenuContent object
                return MenuContent::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::MenuContent()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::MenuContent()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of MenuContent objects
                return MenuContent::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::MenuContent()->Param1, $strParam1),
                        QQ::Equal(QQN::MenuContent()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = MenuContent::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `menu_content`.*
                    FROM
                        `menu_content` AS `menu_content`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return MenuContent::instantiateDbResult($objDbResult);
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
