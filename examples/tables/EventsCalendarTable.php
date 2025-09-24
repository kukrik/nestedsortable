<?php

    use QCubed\Plugin\Control\VauuTable;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\Condition\ConditionInterface as QQCondition;
    use QCubed\Type;
    use QCubed\Query\QQ;

    /**
     * Class EventsCalendarTable
     *
     * This class represents a table display for events data from the EventsCalendar model.
     * It extends the VauuTable class and includes functionalities for data binding,
     * creating table columns, rendering event date information, and custom table behavior.
     *
     * Features:
     * - Dynamically creates columns for event details such as year, event group, title, and more.
     * - Supports custom rendering for event dates using the DateEvent_render method.
     * - Automatically handles data binding with filter conditions and clauses.
     * - Supports pagination and sorting for event records.
     * - Integrates with the QQN query system for database interaction.
     */
    class EventsCalendarTable extends VauuTable
    {
        protected ?object $objCondition = null;
        protected ?array $objClauses = null;

        public object $colYear;
        public object $colEventGroup;
        public object $colTitle;
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
         * @param mixed $objParent The parent object that controls this control.
         * @param string|null $strControlId An optional control ID for identifying this instance.
         *
         * @return void
         * @throws Caller
         */
        public function __construct(mixed $objParent, ?string $strControlId = null)
        {
            parent::__construct($objParent, $strControlId);
            $this->setDataBinder('bindData', $this);
            $this->watch(QQN::EventsCalendar());
        }

        /**
         * Configures and creates the necessary columns for the events calendar.
         *
         * This method initializes columns with specific names, widths, and properties
         * to properly display and format data for the events calendar.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function createColumns(): void
        {
            $this->colYear = $this->createNodeColumn(t("Year"), QQN::EventsCalendar()->Year);

            $this->colEventGroup = $this->createNodeColumn(t("Event group"), QQN::EventsCalendar()->MenuContentGroup);

            $this->colTitle = $this->createNodeColumn("Title", QQN::EventsCalendar()->Title);

            $this->colDateEvent = $this->createCallableColumn(t('Date of event'), [$this, 'DateEvent_render']);
            $this->colDateEvent->OrderByClause = QQ::orderBy(QQN::EventsCalendar()->BeginningEvent);
            $this->colDateEvent->ReverseOrderByClause = QQ::orderBy(QQN::EventsCalendar()->BeginningEvent, false);
            $this->colDateEvent->HtmlEntities = false;

            $this->colChange = $this->createNodeColumn("Update", QQN::EventsCalendar()->EventsChanges);

            $this->colStatusObject = $this->createNodeColumn(t("Status"), QQN::EventsCalendar()->StatusObject);
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

            $this->colAuthor = $this->createNodeColumn(t("Author"), QQN::EventsCalendar()->Author);
        }

        /**
         * Renders the formatted date for an event in the events calendar.
         *
         * This method returns a formatted date string based on whether the event
         * has changes. If changes exist, it uses the post-update date; otherwise,
         * it uses the original post-date.
         *
         * @param EventsCalendar $objEventsCalendar The events calendar object containing event data.
         *
         * @return string|null The formatted date string or null if the date is not available.
         * @throws Caller
         */
        public function Date_render(EventsCalendar $objEventsCalendar): ?string
        {
            if ($objEventsCalendar->getEventsChangesId()) {
                return $objEventsCalendar->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm');
            } else {
                return $objEventsCalendar->getPostDate()->qFormat('DD.MM.YYYY hhhh:mm');
            }
        }

        /**
         * Formats and renders the date and time information for an event.
         *
         * This method processes the beginning and end dates, as well as the start and end times,
         * to generate a human-readable string representation of the event's schedule.
         *
         * @param EventsCalendar $objEventsCalendar The event calendar object containing date and time details.
         *
         * @return string The formatted date and time string for the event.
         */
        public function DateEvent_render(EventsCalendar $objEventsCalendar): string
        {
            if (($objEventsCalendar->BeginningEvent && !$objEventsCalendar->StartTime) &&
                (!$objEventsCalendar->EndEvent && !$objEventsCalendar->EndTime)) {
                return $objEventsCalendar->BeginningEvent->qFormat('DD.MM.YYYY');

            } elseif (($objEventsCalendar->BeginningEvent && $objEventsCalendar->StartTime) &&
                (!$objEventsCalendar->EndEvent && !$objEventsCalendar->EndTime)) {
                return $objEventsCalendar->BeginningEvent->qFormat('DD.MM.YYYY')  . ' ' .
                    $objEventsCalendar->StartTime->qFormat('hhhh:mm');

            } elseif (($objEventsCalendar->BeginningEvent && $objEventsCalendar->EndEvent) &&
                (!$objEventsCalendar->StartTime && !$objEventsCalendar->EndTime)) {
                return $objEventsCalendar->BeginningEvent->qFormat('DD.MM.YYYY') . ' - ' .
                    $objEventsCalendar->EndEvent->qFormat('DD.MM.YYYY');

            } elseif (($objEventsCalendar->BeginningEvent && $objEventsCalendar->StartTime) &&
                ($objEventsCalendar->EndEvent && !$objEventsCalendar->EndTime)) {
                return $objEventsCalendar->BeginningEvent->qFormat('DD.MM.YYYY') . ' ' .
                    $objEventsCalendar->StartTime->qFormat('hhhh:mm') . ' - ' .
                    $objEventsCalendar->EndEvent->qFormat('DD.MM.YYYY');

            } elseif (($objEventsCalendar->BeginningEvent && !$objEventsCalendar->StartTime) &&
                ($objEventsCalendar->EndEvent && $objEventsCalendar->EndTime)) {
                return $objEventsCalendar->BeginningEvent->qFormat('DD.MM.YYYY') . ' - ' .
                    $objEventsCalendar->EndEvent->qFormat('DD.MM.YYYY') . ' ' .
                    $objEventsCalendar->EndTime->qFormat('hhhh:mm');

            } elseif (($objEventsCalendar->BeginningEvent && $objEventsCalendar->StartTime) &&
                ($objEventsCalendar->EndEvent && $objEventsCalendar->EndTime)) {
                return $objEventsCalendar->BeginningEvent->qFormat('DD.MM.YYYY') . ' ' .
                    $objEventsCalendar->StartTime->qFormat('hhhh:mm') . ' - ' .
                    $objEventsCalendar->EndEvent->qFormat('DD.MM.YYYY') . ' ' .
                    $objEventsCalendar->EndTime->qFormat('hhhh:mm');
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
                $this->TotalItemCount = EventsCalendar::queryCount($objCondition, $objClauses);
            }

            $this->DataSource = EventsCalendar::queryArray($objCondition, $objClauses);
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
