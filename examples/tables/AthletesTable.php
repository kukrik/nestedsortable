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
     * Class AthletesTable
     *
     * Represents a data table for displaying and managing athletes.
     * Extends the base functionality of VauuTable to define specific configurations
     * for columns, data binding, and query conditions related to the AthletesTable entity.
     */
    class AthletesTable extends VauuTable
    {
        protected ?object $objCondition = null;
        protected ?array $objClauses = null;

        public object $colFirstName;
        public object $colLastName;
        public object $colBirthDate;
        public object $colGender;
        public object $colLocking;
        public object $colStatus;
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
            $this->watch(QQN::Athletes());
        }

        /**
         * Initializes and configures the table columns for displaying athlete data.
         *
         * This method defines multiple columns including first name, last name, birthdate, gender, status, and locking status.
         * Column properties such as width, format, and HTML entity handling are configured to determine their appearance and behavior in the table.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function createColumns(): void
        {
            $this->colFirstName = $this->createNodeColumn(t("First name"), QQN::Athletes()->FirstName);
            //$this->colFirstName->CellStyler->Width = '20%';

            $this->colLastName = $this->createNodeColumn(t("Last name"), QQN::Athletes()->LastName);
            //$this->colLastName->CellStyler->Width = '20%';

            $this->colBirthDate = $this->createNodeColumn(t("Birth date"), QQN::Athletes()->BirthDate);
            $this->colBirthDate->Format = 'DD.MM.YYYY';
            //$this->colBirthDate->CellStyler->Width = '15%';

            $this->colGender = $this->createNodeColumn(t("Gender"), QQN::Athletes()->AthleteGender);
            //$this->colGender->CellStyler->Width = '15%';

            $this->colStatus = $this->createNodeColumn(t("Status"), QQN::Athletes()->StatusObject);
            $this->colStatus->HtmlEntities = false;
            //$this->colStatus->CellStyler->Width = '15%';

            $this->colLocking = $this->createNodeColumn(t("Lock status"), QQN::Athletes()->IsLockedObject);
            $this->colLocking->HtmlEntities = false;
            //$this->colLocking->CellStyler->Width = '15%';
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
                $this->TotalItemCount = Athletes::queryCount($objCondition, $objClauses);
            }

            if ($objClause = $this->OrderByClause) {
                $objClauses[] = $objClause;
            }

            if ($objClause = $this->LimitClause) {
                $objClauses[] = $objClause;
            }

            $this->DataSource = Athletes::queryArray($objCondition, $objClauses);
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