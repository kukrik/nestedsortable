<?php

    use QCubed\Plugin\Control\VauuTable;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\Condition\ConditionInterface as QQCondition;
    use QCubed\Type;
    use QCubed\Query\QQ;

    /**
     * Class SportsCalendarTable
     *
     * Represents a specialized table for displaying sports calendar data.
     * This class is designed to handle sports event information and provides
     * methods for customizing columns, binding data, and managing conditions
     * and clauses for queries.
     *
     * Properties:
     * - objCondition: Query condition for filtering data displayed in the table.
     * - objClauses: Query clauses for modifying data queries.
     * - colYear: Column representing the year of the event.
     * - colSportsCalendarGroup: Column representing the sports calendar group.
     * - colTitle: Column representing the title of the event.
     * - colSportsArea: Column representing the sports area associated with the event.
     * - colChange: Column representing changes to events (commented out in the implementation).
     * - colDateEvent: Column representing the date of the event with custom rendering.
     * - colBeginningEvent: Column representing the beginning date of the event.
     * - colEndEvent: Column representing the end date of the event.
     * - colStatusObject: Column representing the status of the event.
     * - colPostDate: Column representing the creation date of the event.
     * - colPostUpdateDate: Column representing the last updated date of the event.
     * - colAuthor: Column representing the author of the event entry.
     *
     * Methods:
     * - __construct($objParent, $strControlId = null): Initializes the table and sets up data binding.
     * - createColumns(): Configures and initializes the columns in the table.
     * - DateEvent_render(SportsCalendar $objSportsCalendar): A custom renderer for the event date column. Formats date and time information based on available data.
     * - bindData(?QQCondition $objAdditionalCondition = null, $objAdditionalClauses = null): Binds data to the table's data source based on the specified conditions and clauses.
     * - getCondition(?QQCondition $objAdditionalCondition = null): Retrieves the effective query condition by combining the additional condition and internal conditions.
     * - getClauses($objAdditionalClauses = null): Retrieves the effective query clauses by combining the additional clauses and internal clauses.
     * - __get($strName): Magic method for retrieving properties such as Condition and Clauses.
     * - __set($strName, $mixValue): Magic method for setting properties such as Condition and Clauses.
     * 
     */
    class SportsCalendarTable extends VauuTable
    {
        protected ?object $objCondition = null;
        protected ?array $objClauses = null;

        public object $colYear;
        public object $colSportsCalendarGroup;
        public object $colTitle;
        public object $colSportsArea;
        public object $colChange;

        public object $colDateEvent;
        public object $colBeginningEvent;
        public object $colEndEvent;

        public object $colStatusObject;
        public object $colCombinedDate;

        public object $colPostDate;
        public object $colPostUpdateDate;
        public object $colAuthor;

        /**
         * Constructor method for the class.
         *
         * @param mixed $objParent The parent object that this object is bound to.
         * @param string|null $strControlId An optional control ID for the object.
         *
         * @return void
         * @throws Caller
         */
        public function __construct(mixed $objParent, ?string $strControlId = null)
        {
            parent::__construct($objParent, $strControlId);
            $this->setDataBinder('bindData', $this);
            $this->watch(QQN::SportsCalendar());
        }

        /**
         * Configures and creates various columns for the Sports Calendar interface.
         * This method initializes and assigns properties such as styling, ordering,
         * and formatting for columns such as year, title, status, and other related fields.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function createColumns(): void
        {
            $this->colYear = $this->createNodeColumn(t("Year"), QQN::SportsCalendar()->Year);

            $this->colSportsCalendarGroup = $this->createNodeColumn(t("Sports calendar group"), QQN::SportsCalendar()->MenuContentGroup);

            $this->colTitle = $this->createNodeColumn("Title", QQN::SportsCalendar()->Title);

            $this->colSportsArea = $this->createNodeColumn(t("Sport area"), QQN::SportsCalendar()->SportsAreas);

            $this->colDateEvent = $this->createCallableColumn(t('Date of event'), [$this, 'DateEvent_render']);
            $this->colDateEvent->OrderByClause = QQ::orderBy(QQN::SportsCalendar()->BeginningEvent);
            $this->colDateEvent->ReverseOrderByClause = QQ::orderBy(QQN::SportsCalendar()->BeginningEvent, false);
            $this->colDateEvent->HtmlEntities = false;

            $this->colChange = $this->createNodeColumn("Update", QQN::SportsCalendar()->EventsChanges);

            $this->colStatusObject = $this->createNodeColumn(t("Status"), QQN::SportsCalendar()->StatusObject);
            $this->colStatusObject->HtmlEntities = false;

            $this->colCombinedDate = $this->createCallableColumn(t("Date"), [$this, 'Date_render']);
            $this->colCombinedDate->OrderByClause = QQ::orderBy(
                QQ::subSql("
                    CASE
                        WHEN t0.events_changes_id IS NOT NULL THEN t0.post_update_date
                        ELSE t0.post_date
                    END DESC
                ")
            );
            $this->colCombinedDate->ReverseOrderByClause = QQ::orderBy(
                QQ::subSql("
                    CASE
                        WHEN t0.events_changes_id IS NOT NULL THEN t0.post_update_date
                        ELSE t0.post_date
                    END ASC
                ")
            );

            $this->colAuthor = $this->createNodeColumn(t("Author"), QQN::SportsCalendar()->Author);
        }

        /**
         * Renders and formats the date for a Sports Calendar event.
         * This method determines the appropriate date to display based on whether
         * event changes are present and formats it in a readable string format.
         *
         * @param SportsCalendar $objSportsCalendar The Sports Calendar object containing event data.
         *
         * @return string|null The formatted date string if available, or null if no date is set.
         * @throws Caller
         */
        public function Date_render(SportsCalendar $objSportsCalendar): ?string
        {
            if ($objSportsCalendar->getEventsChangesId()) {
                return $objSportsCalendar->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm');
            } else {
                return $objSportsCalendar->getPostDate()->qFormat('DD.MM.YYYY hhhh:mm');
            }
        }

        /**
         * Renders the date and time information for the event in the Sports Calendar.
         * Formats the dates and times based on the availability of beginning and end events,
         * as well as their associated times.
         *
         * @param SportsCalendar $objSportsCalendar The SportsCalendar object containing
         *        event start and end dates with optional time details.
         *
         * @return null|string A formatted string representing the event's date and time details,
         *         which may include the start/end dates and times depending on the object properties.
         */
        public function DateEvent_render(SportsCalendar $objSportsCalendar): ?string
        {
            if (($objSportsCalendar->BeginningEvent && !$objSportsCalendar->StartTime) &&
                (!$objSportsCalendar->EndEvent && !$objSportsCalendar->EndTime)) {
                return $objSportsCalendar->BeginningEvent->qFormat('DD.MM.YYYY');

            } elseif (($objSportsCalendar->BeginningEvent && $objSportsCalendar->StartTime) &&
                (!$objSportsCalendar->EndEvent && !$objSportsCalendar->EndTime)) {
                return $objSportsCalendar->BeginningEvent->qFormat('DD.MM.YYYY')  . ' ' .
                    $objSportsCalendar->StartTime->qFormat('hhhh:mm');

            } elseif (($objSportsCalendar->BeginningEvent && $objSportsCalendar->EndEvent) &&
                (!$objSportsCalendar->StartTime && !$objSportsCalendar->EndTime)) {
                return $objSportsCalendar->BeginningEvent->qFormat('DD.MM.YYYY') . ' - ' .
                    $objSportsCalendar->EndEvent->qFormat('DD.MM.YYYY');

            } elseif (($objSportsCalendar->BeginningEvent && $objSportsCalendar->StartTime) &&
                ($objSportsCalendar->EndEvent && !$objSportsCalendar->EndTime)) {
                return $objSportsCalendar->BeginningEvent->qFormat('DD.MM.YYYY') . ' ' .
                    $objSportsCalendar->StartTime->qFormat('hhhh:mm') . ' - ' .
                    $objSportsCalendar->EndEvent->qFormat('DD.MM.YYYY');

            } elseif (($objSportsCalendar->BeginningEvent && !$objSportsCalendar->StartTime) &&
                ($objSportsCalendar->EndEvent && $objSportsCalendar->EndTime)) {
                return $objSportsCalendar->BeginningEvent->qFormat('DD.MM.YYYY') . ' - ' .
                    $objSportsCalendar->EndEvent->qFormat('DD.MM.YYYY') . ' ' .
                    $objSportsCalendar->EndTime->qFormat('hhhh:mm');

            } elseif (($objSportsCalendar->BeginningEvent && $objSportsCalendar->StartTime) &&
                ($objSportsCalendar->EndEvent && $objSportsCalendar->EndTime)) {
                return $objSportsCalendar->BeginningEvent->qFormat('DD.MM.YYYY') . ' ' .
                    $objSportsCalendar->StartTime->qFormat('hhhh:mm') . ' - ' .
                    $objSportsCalendar->EndEvent->qFormat('DD.MM.YYYY') . ' ' .
                    $objSportsCalendar->EndTime->qFormat('hhhh:mm');
            }

            return '';
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

            if (!$this->OrderByClause) {
                $objClauses[] = QQ::orderBy(
                   QQ::subSql("
                        CASE
                            WHEN t0.events_changes_id IS NOT NULL THEN t0.post_update_date     
                            ELSE t0.post_date
                        END DESC
                    ")
                );
            } else {
                $objClauses[] = $this->OrderByClause;
            }
            
            if ($objClause = $this->LimitClause) {
                $objClauses[] = $objClause;
            }

            if ($this->Paginator) {
                $this->TotalItemCount = SportsCalendar::queryCount($objCondition, $objClauses);
            }

            $this->DataSource = SportsCalendar::queryArray($objCondition, $objClauses);
        }

        /**
         * Constructs and returns a combined condition for querying data.
         * This method accepts an optional base condition and combines it with
         * an existing condition, if defined. If no condition is provided,
         * a default condition is used.
         *
         * @param null|QQCondition $objAdditionalCondition An optional additional condition
         *                                                 to combine with the existing one.
         *
         * @return null|QQCondition The resulting combined condition for data queries.
         * @throws Caller
         */
        protected function getCondition(mixed $objAdditionalCondition = null): ?QQCondition
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