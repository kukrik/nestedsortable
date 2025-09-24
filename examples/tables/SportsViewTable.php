<?php

    use QCubed\Plugin\Control\VauuTable;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\Condition\ConditionInterface as QQCondition;
    use QCubed\Type;
    use QCubed\Query\QQ;

    /**
     * Class SportsViewTable
     *
     * Represents a table control for displaying sports-related data. Extends the base VauuTable class and includes
     * mechanisms for condition-driven data binding and dynamic column creation.
     */
    class SportsViewTable extends VauuTable
    {
        protected ?object $objCondition = null;
        protected ?array $objClauses = null;

        public object $colYear;
        public object $colSportsTables;
        public object $colContentTypes;
        public object $colShowDate;
        public object $colTitle;
        public object $colStatus;
        public object $colPostDate;
        public object $colPostUpdateDate;

        /**
         * Constructor method for initializing the object and setting up data binding and observers.
         *
         * @param mixed $objParent The parent object that this control belongs to.
         * @param string|null $strControlId An optional identifier for the control.
         *
         * @return void
         * @throws Caller
         */
        public function __construct(mixed $objParent, ?string $strControlId = null)
        {
            parent::__construct($objParent, $strControlId);
            $this->setDataBinder('bindData', $this);
            $this->watch(QQN::SportsTables());
        }

        /**
         * Configures and initializes columns for the sports table.
         * This method defines and styles various columns, such as year, sports area, content type, date, title, status, creation date, and modification date.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function createColumns(): void
        {
            $this->colYear = $this->createNodeColumn(t("Year"), QQN::SportsTables()->Year);
            $this->colYear->CellStyler->Width = '5%';

            $this->colSportsTables = $this->createNodeColumn(t("Sports area"), QQN::SportsTables()->SportsAreas);
            $this->colSportsTables->CellStyler->Width = '10%';

            $this->colContentTypes = $this->createNodeColumn(t("Content type"), QQN::SportsTables()->SportsContentTypes);
            $this->colContentTypes->CellStyler->Width = '10%';

            $this->colShowDate = $this->createNodeColumn(t("Date"), QQN::SportsTables()->ShowDate);
            $this->colShowDate->Format = 'DD.MM.YYYY';
            $this->colShowDate->CellStyler->Width = '10%';

            $this->colTitle = $this->createNodeColumn(t("Title"), QQN::SportsTables()->Title);
            $this->colTitle->CellStyler->Width = '31%';

            $this->colStatus = $this->createNodeColumn(t("Status"), QQN::SportsTables()->StatusObject);
            $this->colStatus->HtmlEntities = false;
            $this->colStatus->CellStyler->Width = '10%';

            $this->colPostDate = $this->createNodeColumn(t("Created"), QQN::SportsTables()->PostDate);
            $this->colPostDate->Format = 'DD.MM.YYYY hhhh:mm';
            $this->colPostDate->CellStyler->Width = '12%';

            $this->colPostUpdateDate = $this->createNodeColumn(t("Modified"), QQN::SportsTables()->PostUpdateDate);
            $this->colPostUpdateDate->Format = 'DD.MM.YYYY hhhh:mm';
            $this->colPostUpdateDate->CellStyler->Width = '12%';
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
            $objExtraCondition = QQ::equal(QQN::SportsTables()->SportsCalendarGroup->Status, 1);

            if ($objAdditionalCondition) {
                $objAdditionalCondition = QQ::andCondition($objAdditionalCondition, $objExtraCondition);
            } else {
                $objAdditionalCondition = $objExtraCondition;
            }

            $objCondition = $this->getCondition($objAdditionalCondition);
            $objClauses = $this->getClauses($objAdditionalClauses);

            if ($this->Paginator) {
                $this->TotalItemCount = SportsTables::queryCount($objCondition, $objClauses);
            }

            if ($objClause = $this->OrderByClause) {
                $objClauses[] = $objClause;
            }

            if ($objClause = $this->LimitClause) {
                $objClauses[] = $objClause;
            }

            $this->DataSource = SportsTables::queryArray($objCondition, $objClauses);
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