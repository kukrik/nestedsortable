<?php

    use QCubed\Plugin\Control\VauuTable;
    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\Condition\ConditionInterface as QQCondition;
    use QCubed\Type;
    use QCubed\Query\QQ;

    /**
     * Class NewsTable
     *
     * Represents a table for displaying and managing news entries. It extends the functionality of VauuTable
     * to provide specific behavior suitable for handling news data.
     */
    class NewsTable extends VauuTable
    {
        protected ?object $objCondition = null;
        protected ?array $objClauses = null;
        protected string $strTempUrl = APP_UPLOADS_TEMP_URL;

        public object $colPicture;
        public object $colNewsGroup;
        public object $colTitle;
        public object $colChanges;
        public object $colStatusObject;
        public object $colCombinedDate;
        public object $colAvailableFrom;
        public object $colExpiryDate;
        public object $colAuthor;

        /**
         * Constructor for initializing the control with a specified parent and control ID.
         *
         * Sets up the data binder to associate data-binding logic and establishes a watch
         * on the News object for any relevant changes or updates.
         * Inherits properties and methods from the parent constructor.
         *
         * @param mixed $objParent The parent object to which this control belongs.
         * @param string|null $strControlId An optional ID to uniquely identify this control.
         *
         * @return void
         * @throws Caller
         */
        public function __construct(mixed $objParent, ?string $strControlId = null)
        {
            parent::__construct($objParent, $strControlId);
            $this->setDataBinder('bindData', $this);
            $this->watch(QQN::News());
        }

        /**
         * Creates and configures various columns for display and functionality.
         *
         * Configures columns including a picture, newsgroup, title, category,
         * status, post-date, available from date, expiry date, and author.
         * Sets properties such as width, order by clauses, formatting,
         * and other specific configurations to enhance visual representation and usage.
         *
         * @return void
         * @throws Caller
         * @throws InvalidCast
         */
        public function createColumns(): void
        {
            $this->colPicture = $this->createCallableColumn(t('Picture'), [$this, 'View_render']);
            $this->colPicture->OrderByClause = QQ::orderBy(QQN::news()->PictureId, false);
            $this->colPicture->ReverseOrderByClause = QQ::orderBy(QQN::News()->PictureId, true);
            $this->colPicture->HtmlEntities = false;
            $this->colPicture->CellStyler->Width = '4%';

            $this->colNewsGroup = $this->createNodeColumn(t("Newsgroup"), QQN::News()->GroupTitle);
            $this->colNewsGroup->CellStyler->Width = '10%';

            $this->colTitle = $this->createNodeColumn(t("Title"), QQN::News()->Title);
            $this->colTitle->CellStyler->Width = '25%';

            $this->colChanges = $this->createNodeColumn("Update", QQN::News()->Changes);

            $this->colStatusObject = $this->createNodeColumn(t("Status"), QQN::News()->StatusObject);
            $this->colStatusObject->HtmlEntities = false;

            $this->colCombinedDate = $this->createCallableColumn(t("Date"), [$this, 'Date_render']);
            $this->colCombinedDate->OrderByClause = QQ::orderBy(
                QQ::subSql("
                    CASE
                        WHEN available_from IS NOT NULL THEN available_from
                        WHEN changes_id IS NOT NULL THEN post_update_date
                        ELSE post_date
                    END DESC
                ")
            );
            $this->colCombinedDate->ReverseOrderByClause = QQ::orderBy(
                QQ::subSql("
                    CASE
                        WHEN available_from IS NOT NULL THEN available_from
                        WHEN changes_id IS NOT NULL THEN post_update_date
                        ELSE post_date
                    END ASC
                ")
            );

            $this->colAvailableFrom = $this->createNodeColumn(t("Available from"), QQN::News()->AvailableFrom);
            $this->colAvailableFrom->Format = 'DD.MM.YYYY hhhh:mm';

            $this->colExpiryDate = $this->createNodeColumn(t("Expiry date"), QQN::News()->ExpiryDate);
            $this->colExpiryDate->Format = 'DD.MM.YYYY hhhh:mm';

            $this->colAuthor = $this->createNodeColumn(t("Author"), QQN::News()->Author);
        }

        /**
         * Renders the view for a News item by generating an HTML representation, including a thumbnail image if a picture is associated with the news.
         *
         * Fetches and displays a preview of the news item using its associated picture, if available. The preview is wrapped in a styled HTML structure.
         *
         * @param News $objNews The news object containing properties such as the picture ID for rendering the preview.
         *
         * @return string|null The HTML string for the rendered view if a picture is available, or null if no picture is associated with the news item.
         * @throws Caller
         * @throws InvalidCast
         */
        public function View_render(News $objNews): ?string
        {
            if ($objNews->getPictureId()) {
                $objFiles = Files::load($objNews->getPictureId());

                $strHtm = '<span class="news-preview">';
                $strHtm .= '<img src="' . $this->strTempUrl . '/_files/thumbnail' . $objFiles->getPath() . '">';
                $strHtm .= '</span>';
                return $strHtm;
            }
            return null;
        }

        /**
         * Renders a formatted date string based on the specified attributes of a News object.
         *
         * This method determines which date to display based on the availability of
         * certain attributes in the provided News object. It checks for the "Available From"
         * date, changes ID with post-update date, or the post-date, formatting whichever is applicable.
         *
         * @param News $objNews The News object containing date-related attributes to render.
         *
         * @return string|null The formatted date string in 'DD.MM.YYYY hhhh:mm' format, or null if no date is
         *     available.
         * @throws Caller
         */
        public function Date_render(News $objNews): ?string
        {
            if ($objNews->getAvailableFrom()) {
                return $objNews->getAvailableFrom()->qFormat('DD.MM.YYYY hhhh:mm');
            } else if ($objNews->getChangesId() && $objNews->getPostUpdateDate()) {
                return $objNews->getPostUpdateDate()->qFormat('DD.MM.YYYY hhhh:mm');
            } else if ($objNews->getPostDate()) {
                return $objNews->getPostDate()->qFormat('DD.MM.YYYY hhhh:mm');
            }
            return null;
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
                            WHEN available_from IS NOT NULL THEN available_from
                            WHEN changes_id IS NOT NULL THEN post_update_date
                            ELSE post_date
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
                $this->TotalItemCount = News::queryCount($objCondition, $objClauses);
            }

            $this->DataSource = News::queryArray($objCondition, $objClauses);
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