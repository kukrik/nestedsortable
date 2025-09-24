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
     * Class StatisticsTable
     *
     * Represents a data table for displaying and managing statistics.
     * Extends the base functionality of VauuTable to define specific configurations
     * for columns, data binding, and query conditions related to the StatisticsTable entity.
     */
    class StatisticsTable extends VauuTable
    {
        protected ?object $objCondition = null;
        protected ?array $objClauses = null;

        public object $colGroupTitle;
        public object $colTitle;
        public object $colAuthor;
        public object $colStatusObject;
        public object $colPostDate;
        public object $colPostUpdateDate;

        /**
         * Constructor method for initializing the object.
         *
         * @param mixed $objParent The parent object to which this control is attached.
         * @param string|null $strControlId An optional string to specify the control's a unique identifier.
         *
         * @return void
         * @throws Caller
         */
        public function __construct(mixed $objParent, ?string $strControlId = null)
        {
            parent::__construct($objParent, $strControlId);
            $this->setDataBinder('bindData', $this);
            $this->watch(QQN::StatisticsSettings());
        }

        /**
         * Creates and configures the columns used in the display or management interface.
         *
         * This method initializes column objects with properties, such as title, width, formatting,
         * sorting options, and more. The method is tailored for specific data fields
         * and their respective configurations.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function createColumns(): void
        {
            $this->colGroupTitle = $this->createNodeColumn("Links group", QQN::StatisticsSettings()->Name);
            $this->colGroupTitle->CellStyler->Width = '15%';

            $this->colTitle = $this->createNodeColumn(t("Title"), QQN::StatisticsSettings()->Title);
            $this->colTitle->CellStyler->Width = '35%';

            $this->colStatusObject = $this->createNodeColumn(t("Status"), QQN::StatisticsSettings()->StatusObject);
            $this->colStatusObject->HtmlEntities = false;
            $this->colStatusObject->CellStyler->Width = '12%';

            $this->colPostDate = $this->createNodeColumn(t("Created"), QQN::StatisticsSettings()->PostDate);
            $this->colPostDate->OrderByClause = QQ::orderBy(QQN::StatisticsSettings()->PostDate, false);
            $this->colPostDate->ReverseOrderByClause = QQ::orderBy(QQN::StatisticsSettings()->PostDate, true);
            $this->colPostDate->Format = 'DD.MM.YYYY hhhh:mm';

            $this->colPostUpdateDate = $this->createNodeColumn(t("Modified"), QQN::StatisticsSettings()->PostUpdateDate);
            $this->colPostUpdateDate->Format = 'DD.MM.YYYY hhhh:mm';
            $this->colAuthor = $this->createNodeColumn(t("Author"), QQN::StatisticsSettings()->Author);
        }

        /**
         * Binds data to the data source by applying specified conditions and clauses.
         *
         * This method constructs query conditions and clauses to retrieve a data set
         * from the `NewsSettings` class. It supports pagination, ordering, and limiting
         * the results as required.
         *
         * @param QQCondition|null $objAdditionalCondition An optional additional condition
         *        to be merged with the primary condition for data retrieval.
         * @param null|mixed $objAdditionalClauses Additional clauses such as sorting or grouping
         *        to be applied to the query.
         *
         * @return void
         * @throws Caller
         */
        public function bindData(?QQCondition $objAdditionalCondition = null, mixed $objAdditionalClauses = null): void
        {
            $objCondition = $this->getCondition($objAdditionalCondition);
            $objClauses = $this->getClauses($objAdditionalClauses);

            if ($this->Paginator) {
                $this->TotalItemCount = StatisticsSettings::queryCount($objCondition, $objClauses);
            }

            if ($objClause = $this->OrderByClause) {
                $objClauses[] = $objClause;
            }

            if ($objClause = $this->LimitClause) {
                $objClauses[] = $objClause;
            }

            $this->DataSource = StatisticsSettings::queryArray($objCondition, $objClauses);
        }

        /**
         * Retrieves and aggregates a condition object for database queries.
         *
         * This method combines the provided condition with an existing predefined condition,
         * returning a composite condition for query execution. If no condition is provided,
         * a default condition encompassing all records is used.
         *
         * @param QQCondition|null $objAdditionalCondition An optional additional condition to include in the query.
         *
         * @return QQCondition|All|AndCondition|null The resulting composite condition for the query.
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
         * Magic method to retrieve the value of a property.
         *
         * This method provides access to specific properties or delegates the
         * retrieval to the parent class if the property is not directly handled.
         * Properties include 'Condition' and 'Clauses', returning their respective
         * objects if requested.
         *
         * @param string $strName The name of the property to retrieve.
         *
         * @return mixed The value of the requested property.
         * @throws Caller If the property does not exist or cannot be retrieved.
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
         * This method is used to assign values to specific properties, such as
         * `Condition` and `Clauses`, while ensuring the provided value meets
         * the expected type constraints. Throws an exception if the property
         * name is unrecognized or the value cannot be cast to the required type.
         *
         * @param string $strName The name of the property to set.
         * @param mixed $mixValue The value to assign to the property.
         *
         * @return void
         * @throws Caller|Throwable Thrown if the property name is invalid or an error occurs during value casting.
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