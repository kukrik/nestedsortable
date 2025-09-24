<?php

    use QCubed\Plugin\Control\VauuTable;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\Condition\All;
    use QCubed\Query\Condition\AndCondition;
    use QCubed\Query\Condition\ConditionInterface as QQCondition;
    use QCubed\Query\QQ;
    use QCubed\Type;

    /**
     * Class EventsChangesTable
     *
     * Represents a table for displaying and manipulating data related to "EventsChanges".
     * This class extends VauuTable and provides methods for creating table columns,
     * binding data, and managing conditions and clauses for data operations.
     */
    class EventsChangesTable extends VauuTable
    {
        protected ?object $objCondition = null;
        protected ?array $objClauses = null;

        public object $colId;
        public object $colTitle;
        public object $colStatus;
        public object $colPostDate;
        public object $colPostUpdateDate;

        /**
         * Constructor method for the class.
         *
         * @param mixed $objParent The parent object to which this instance belongs.
         * @param string|null $strControlId Optional control ID for this instance.
         *
         * @return void
         * @throws Caller
         */
        public function __construct(mixed $objParent, ?string $strControlId = null)
        {
            parent::__construct($objParent, $strControlId);
            $this->setDataBinder('bindData', $this);
            $this->watch(QQN::EventsChanges());
        }

        /**
         * Creates and initializes the columns for the current table with appropriate labels, formatting, and attributes.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function createColumns(): void
        {
            $this->colId = $this->createNodeColumn(t("Id"), QQN::EventsChanges()->Id);
            $this->colTitle = $this->createNodeColumn(t("Title"), QQN::EventsChanges()->Title);
            $this->colStatus = $this->createNodeColumn(t("Is activated"), QQN::EventsChanges()->StatusObject);
            $this->colStatus->HtmlEntities = false;
            $this->colPostDate = $this->createNodeColumn(t("Created"), QQN::EventsChanges()->PostDate);
            $this->colPostDate->Format = 'DD.MM.YYYY hhhh:mm';
            $this->colPostUpdateDate = $this->createNodeColumn(t("Modified"), QQN::EventsChanges()->PostUpdateDate);
            $this->colPostUpdateDate->Format = 'DD.MM.YYYY hhhh:mm';
        }

        /**
         * Binds data to the current control by applying conditions and clauses and fetching
         * the appropriate records from the data source.
         *
         * @param QQCondition|null $objAdditionalCondition An optional additional condition to modify the query.
         * @param null|mixed $objAdditionalClauses Optional additional clauses to refine the query further.
         *
         * @return void
         * @throws Caller
         */
        public function bindData(?QQCondition $objAdditionalCondition = null, mixed $objAdditionalClauses = null): void
        {
            $objCondition = $this->getCondition($objAdditionalCondition);
            $objClauses = $this->getClauses($objAdditionalClauses);

            if ($this->Paginator) {
                $this->TotalItemCount = EventsChanges::queryCount($objCondition, $objClauses);
            }

            if ($objClause = $this->OrderByClause) {
                $objClauses[] = $objClause;
            }

            if ($objClause = $this->LimitClause) {
                $objClauses[] = $objClause;
            }

            $this->DataSource = EventsChanges::queryArray($objCondition, $objClauses);
        }


        /**
         * Retrieves the condition to be applied, merging any existing conditions with the provided additional condition.
         *
         * @param QQCondition|null $objAdditionalCondition An optional additional condition to be combined with the current condition.
         *
         * @return QQCondition|All|AndCondition|null  The resulting condition after combining the existing and additional conditions.
         * @throws Caller
         */
        protected function getCondition(?QQCondition $objAdditionalCondition = null): QQCondition|All|AndCondition|null
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
         * Retrieves and merges a set of clauses for query configuration.
         *
         * This method combines any additional clauses provided with the existing
         * clauses stored in the object, ensuring a unified set of clauses
         * for query generation or manipulation.
         *
         * @param null|mixed $objAdditionalClauses Additional clauses to merge with the existing clauses. Can be null.
         *
         * @return array The resulting array of clauses after merging additional clauses and existing clauses.
         */
        protected function getClauses(mixed $objAdditionalClauses = null): array
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
         * Magic method to retrieve the value of a property by its name.
         *
         * @param string $strName The name of the property to retrieve.
         *
         * @return mixed The value of the requested property.
         * @throws Caller Thrown if the property does not exist.
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
         * Magic method for setting the value of a property. Handles specific cases for "Condition" and "Clauses" while delegating
         * to the parent implementation for other properties.
         *
         * @param string $strName The name of the property to set.
         * @param mixed $mixValue The value to assign to the property.
         *
         * @return void
         * @throws Caller|Throwable Thrown if the property name is invalid or if the provided value cannot be cast to the expected type.
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