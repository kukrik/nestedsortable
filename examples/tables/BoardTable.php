<?php

    use QCubed\Plugin\Control\VauuTable;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\Condition\ConditionInterface as QQCondition;
    use QCubed\Type;
    use QCubed\Query\QQ;

    /**
     * Class BoardTable
     *
     * Represents a table control for displaying board-related data. Extends the base VauuTable class and includes
     * mechanisms for condition-driven data binding and dynamic column creation.
     */
    class BoardTable extends VauuTable
    {
        protected ?object $objCondition = null;
        protected ?array $objClauses = null;

        public object $colGroupTitle;
        public object $colTitle;
        public object $colAuthor;
        public object $colStatusObject;
        public object $colImageUploadObject;
        public object $colPostDate;
        public object $colPostUpdateDate;

        /**
         * Constructor method for initializing the object.
         *
         * @param mixed $objParent The parent object that this control is attached to.
         * @param string|null $strControlId Optional control ID to uniquely identify the control.
         *
         * @return void
         * @throws Caller
         */
        public function __construct(mixed $objParent, ?string $strControlId = null)
        {
            parent::__construct($objParent, $strControlId);
            $this->setDataBinder('bindData', $this);
            $this->watch(QQN::BoardsSettings());
        }

        /**
         * Creates and configures the columns for the board settings interface.
         *
         * This method initializes and formats various columns including group title, title,
         * status, creation date, modification date, and author. Each column is assigned
         * specific properties such as width, formatting, and ordering behavior.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function createColumns(): void
        {
            $this->colGroupTitle = $this->createNodeColumn("Board group", QQN::BoardsSettings()->Name);
            $this->colGroupTitle->CellStyler->Width = '12%';

            $this->colTitle = $this->createNodeColumn(t("Title"), QQN::BoardsSettings()->Title);
            $this->colTitle->CellStyler->Width = '27%';

            $this->colStatusObject = $this->createNodeColumn(t("Status"), QQN::BoardsSettings()->StatusObject);
            $this->colStatusObject->HtmlEntities = false;
            $this->colStatusObject->CellStyler->Width = '12%';

            $this->colImageUploadObject = $this->createNodeColumn(t("Image upload"), QQN::BoardsSettings()->AllowedUploadingObject);
            $this->colImageUploadObject->HtmlEntities = false;
            $this->colImageUploadObject->CellStyler->Width = '12%';

            $this->colPostDate = $this->createNodeColumn(t("Created"), QQN::BoardsSettings()->PostDate);
            $this->colPostDate->OrderByClause = QQ::orderBy(QQN::BoardsSettings()->PostDate, false);
            $this->colPostDate->ReverseOrderByClause = QQ::orderBy(QQN::BoardsSettings()->PostDate, true);
            $this->colPostDate->Format = 'DD.MM.YYYY hhhh:mm';

            $this->colPostUpdateDate = $this->createNodeColumn(t("Modified"), QQN::BoardsSettings()->PostUpdateDate);
            $this->colPostUpdateDate->Format = 'DD.MM.YYYY hhhh:mm';

            $this->colAuthor = $this->createNodeColumn(t("Author"), QQN::BoardsSettings()->Author);
        }

        /**
         * Binds data to the data source based on specified conditions and clauses.
         *
         * Constructs a query condition and combines it with any additional conditions
         * or clauses provided. Calculates the total item count if a paginator is set
         * and applies ordering and limit clauses. Retrieves the matching data and sets
         * it as the data source.
         *
         * @param QQCondition|null $objAdditionalCondition An optional additional condition to refine the query.
         * @param mixed $objAdditionalClauses An optional set of additional clauses, such as ordering or limiting.
         *
         * @return void
         * @throws Caller
         */
        public function bindData(?QQCondition $objAdditionalCondition = null, mixed $objAdditionalClauses = null): void
        {
            $objCondition = $this->getCondition($objAdditionalCondition);
            $objClauses = $this->getClauses($objAdditionalClauses);

            if ($this->Paginator) {
                $this->TotalItemCount = BoardsSettings::queryCount($objCondition, $objClauses);
            }

            if ($objClause = $this->OrderByClause) {
                $objClauses[] = $objClause;
            }

            if ($objClause = $this->LimitClause) {
                $objClauses[] = $objClause;
            }

            $this->DataSource = BoardsSettings::queryArray($objCondition, $objClauses);
        }

        /**
         * Retrieves and constructs the condition to be applied in a query.
         *
         * Combines the provided additional condition with the object's internal condition,
         * or defaults to including all records if no condition is supplied. If both
         * conditions are provided, it returns a conjunction of these conditions.
         *
         * @param QQCondition|null $objAdditionalCondition An optional additional condition to combine.
         *
         * @return null|QQCondition The resulting condition to be used in a query.
         * @throws Caller
         */
        protected function getCondition(?QQCondition $objAdditionalCondition = null): ?QQCondition
        {
            $objCondition = $objAdditionalCondition;

            if (!$objCondition) {
                $objCondition = QQ::all();
            }

            if ($this->objCondition) {
                $objCondition = QQ::andCondition($objCondition, $this->objCondition);
            }

            return $objCondition;
        }

        /**
         * Combines provided clauses with existing stored clauses to create a unified array of clauses.
         *
         * This method ensures that additional clauses are combined with pre-stored clauses to form
         * a complete list of applicable clauses. If no additional clauses are provided, an empty array is used.
         *
         * @param null|array $objAdditionalClauses An optional array of additional clauses to be merged
         *                                         with the existing stored clauses.
         *
         * @return null|array The resulting array of combined clauses.
         */
        protected function getClauses(?array $objAdditionalClauses = null): ?array
        {
            $objClauses = $objAdditionalClauses;
            if (!$objClauses) {
                $objClauses = [];
            }

            if ($this->objClauses) {
                $objClauses = array_merge($objClauses, $this->objClauses);
            }

            return $objClauses;
        }

        /**
         * Magic method to retrieve the value of a property via its name.
         *
         * Handles specific cases for 'Condition' and 'Clauses', returning their
         * respective values. For other property names, it delegates to the parent
         * implementation if available. If an invalid property name is provided,
         * it throws an exception.
         *
         * @param string $strName The name of the property to retrieve.
         *
         * @return mixed The value of the requested property.
         * @throws Caller If the property name is invalid or cannot be retrieved.
         */
        public function __get(string $strName): mixed
        {
            switch ($strName) {
                case 'Condition':
                    return $this->objCondition;
                case 'Clauses':
                    return $this->objClauses;
                default:
                    try {
                        return parent::__get($strName);
                    } catch (Caller $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
            }
        }

        /**
         * Magic method to set the value of a property dynamically.
         *
         * Sets specific properties such as 'Condition' and 'Clauses' by validating
         * and casting their values. Marks the object as modified upon successful
         * assignment. For unrecognized properties, it defers to the parent class's
         * implementation.
         *
         * @param string $strName The name of the property being set.
         * @param mixed $mixValue The value to assign to the property.
         *
         * @return void
         * @throws Caller Thrown if the property name or value is invalid.
         * @throws InvalidCast
         * @throws Throwable
         */
        public function __set(string $strName, mixed $mixValue): void
        {
            switch ($strName) {
                case 'Condition':
                    try {
                        $this->objCondition = Type::cast($mixValue, '\QCubed\Query\Condition\ConditionInterface');
                        $this->markAsModified();
                    } catch (Caller $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                    break;
                case 'Clauses':
                    try {
                        $this->objClauses = Type::cast($mixValue, Type::ARRAY_TYPE);
                        $this->markAsModified();
                    } catch (Caller $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
                    break;
                default:
                    try {
                        parent::__set($strName, $mixValue);
                        break;
                    } catch (Caller $objExc) {
                        $objExc->incrementOffset();
                        throw $objExc;
                    }
            }
        }
    }