<?php

    use QCubed\Database\Exception\UndefinedPrimaryKey;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\Clause\ClauseInterface as iClause;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/ArticleGen.php');

    /**
     * The Article class defined here contains any
     * customized code for the Article class in the
     * Object Relational Model. It represents the "article" table
     * in the database and extends from the code generated abstract ArticleGen
     * class, which contains all the basic CRUD-type functionality as well as
     * basic methods to handle relationships and index-based loading.
     *
     * @package My QCubed Application
     * @subpackage Model
     *
     */
    class Article extends ArticleGen
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
         * Loads an Article object based on the provided menu content ID.
         * Utilizes a query to fetch a single Article object associated with the given ID.
         *
         * @param int $intMenuContentId The ID of the menu content to load the Article from.
         * @param null|mixed $objOptionalClauses Optional clauses for the query (e.g., conditions, sorting).
         *
         * @return Article The Article object associated with the provided menu content ID.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function loadByIdFromContentId(int $intMenuContentId, mixed $objOptionalClauses = null): ?Article
        {
            // Use QuerySingle to Perform the Query
            return Article::querySingle(
                QQ::AndCondition(
                    QQ::Equal(QQN::Article()->MenuContentId, $intMenuContentId)
                ), $objOptionalClauses
            );
        }

        /**
         * Sets the assigned editor's name by the provided key if the key does not match the current assigned user's ID.
         * If the user is not already associated with an article editor by the given key, it associates the user.
         *
         * @param mixed $key The identifier key to match with the assigned editor's ID.
         *
         * @return void
         * @throws UndefinedPrimaryKey
         * @throws Caller
         * @throws InvalidCast
         */
        public function setAssignedEditorsNameById(mixed $key): void
        {
            if ($this->getAssignedByUserObject()->Id !== $key) {
                if (!$this->isUserAsArticlesEditorsAssociatedByKey($key)) {
                    $this->associateUserAsArticlesEditorsByKey($key);
                }
            }
        }

        /**
         * Loads a single CategoryOfArticle object by its ID within a category.
         *
         * @param int $intCategoryId The ID of the category to load the object from.
         * @param null|mixed $objOptionalClauses Optional clauses to customize the query.
         *
         * @return CategoryOfArticle|null The loaded CategoryOfArticle object, or null if not found.
         * @throws Caller
         * @throws InvalidCast
         */
        public static function loadByIdFromCategory(int $intCategoryId, mixed $objOptionalClauses = null): ?CategoryOfArticle
        {
            // Use QuerySingle to Perform the Query
            return CategoryOfArticle::querySingle(
                QQ::AndCondition(
                    QQ::Equal(QQN::CategoryOfArticle()->Id, $intCategoryId)
                ), $objOptionalClauses
            );
        }

        // NOTE: Remember that when introducing a new custom function,
        // you must specify types for the function parameters as well as for the function return type!

        // Override or Create New load/count methods
        // (For obvious reasons, these methods are commented out...
        // But feel free to use these as a starting point)
        /*

            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return an array of Article objects
                return Article::queryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Article()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::Article()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a single Article object
                return Article::querySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Article()->Param1, $strParam1),
                        QQ::GreaterThan(QQN::Article()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
                // This will return a count of Article objects
                return Article::queryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::Article()->Param1, $strParam1),
                        QQ::Equal(QQN::Article()->Param2, $intParam2)
                    ),
                    $objOptionalClauses
                );
            }


            public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
                // Performing the load manually (instead of using QCubed Query)

                // Get the Database Object for this Class
                $objDatabase = Article::getDatabase();

                // Properly Escape All Input Parameters using Database->SqlVariable()
                $strParam1 = $objDatabase->SqlVariable($strParam1);
                $intParam2 = $objDatabase->SqlVariable($intParam2);

                // Setup the SQL Query
                $strQuery = sprintf('
                    SELECT
                        `article`.*
                    FROM
                        `article` AS `article`
                    WHERE
                        param_1 = %s AND
                        param_2 < %s',
                    $strParam1, $intParam2);

                // Perform the Query and Instantiate the Result
                $objDbResult = $objDatabase->Query($strQuery);
                return Article::instantiateDbResult($objDbResult);
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
