<?php

use QCubed\Query\Condition\ConditionInterface as QQCondition;
use QCubed\Query\Clause\ClauseInterface as QQClause;
use QCubed\Table\NodeColumn;
use QCubed\Table\DataColumn;
use QCubed\Project\Control\ControlBase as QControl;
use QCubed\Project\Control\FormBase as QForm;
use QCubed\Exception\Caller;
use QCubed\Project\Application;
use QCubed\Type;
use QCubed\Query\QQ;
use QCubed\QDateTime;

class EventsCalendarTable extends \QCubed\Plugin\Control\VauuTable
{
    protected $objCondition;
    protected $objClauses;

    public $colYear;
    public $colEventGroup;
    public $colTitle;
    public $colChange;

    public $colDateEvent;
    public $colBeginningEvent;
    public $colEndEvent;

    public $colStatusObject;
    public $colPostDate;
    public $colPostUpdateDate;
    public $colAuthor;

    protected $intGroup;

    public function __construct($objParent, $strControlId = null)
    {
        parent::__construct($objParent, $strControlId);
        $this->setDataBinder('bindData', $this);
        $this->watch(QQN::EventsCalendar());
    }

    public function createColumns()
    {
        $this->colYear = $this->createNodeColumn(t("Year"), QQN::EventsCalendar()->Year);
        $this->colYear->CellStyler->Width = '5%';
        $this->colEventGroup = $this->createNodeColumn(t("Event group"), QQN::EventsCalendar()->EventsGroupName);
        $this->colEventGroup->CellStyler->Width = '12%';
        $this->colTitle = $this->createNodeColumn("Title", QQN::EventsCalendar()->Title);
        $this->colTitle->CellStyler->Width = '25%';
        $this->colChange = $this->createNodeColumn("Change", QQN::EventsCalendar()->EventsChanges);
        //$this->colChange->CellStyler->Width = '25%';
        $this->colDateEvent = $this->createCallableColumn(t('Date of event'), [$this, 'DateEvent_render']);
        $this->colDateEvent->OrderByClause = QQ::orderBy(QQN::EventsCalendar()->BeginningEvent, false);
        $this->colDateEvent->ReverseOrderByClause = QQ::orderBy(QQN::EventsCalendar()->BeginningEvent, true);
        $this->colDateEvent->HtmlEntities = false;
        //$this->colDateEvent->CellStyler->Width = '15%';
        $this->colStatusObject = $this->createNodeColumn(t("Status"), QQN::EventsCalendar()->StatusObject);
        $this->colStatusObject->HtmlEntities = false;
        //$this->colStatusObject->CellStyler->Width = '10%';
        $this->colPostDate = $this->createNodeColumn(t("Created"), QQN::EventsCalendar()->PostDate);
        $this->colPostDate->OrderByClause = QQ::orderBy(QQN::EventsCalendar()->PostDate, false);
        $this->colPostDate->ReverseOrderByClause = QQ::orderBy(QQN::EventsCalendar()->PostDate, true);
        $this->colPostDate->Format = 'DD.MM.YYYY hhhh:mm';
        //$this->colPostDate->CellStyler->Width = '13%';

        //$this->colPostUpdateDate = $this->createNodeColumn(t("Post update date"), QQN::EventsCalendar()->PostUpdateDate);
        //$this->colPostUpdateDate->Format = 'DD.MM.YYYY hhhh:mm:ss';
        //$this->colPostUpdateDate->CellStyler->Width = '12%';

        $this->colAuthor = $this->createNodeColumn(t("Author"), QQN::EventsCalendar()->Author);
        //$this->colAuthor->CellStyler->Width = '15%';
    }

    public function DateEvent_render(EventsCalendar $objEventsCalendar)
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
    }

    public function bindData(?QQCondition $objAdditionalCondition = null, $objAdditionalClauses = null)
    {
        $objCondition = $this->getCondition($objAdditionalCondition);
        $objClauses = $this->getClauses($objAdditionalClauses);

        if ($this->Paginator) {
            $this->TotalItemCount = EventsCalendar::queryCount($objCondition, $objClauses);
        }

        if ($objClause = $this->OrderByClause) {
            $objClauses[] = $objClause;
        }

        if ($objClause = $this->LimitClause) {
            $objClauses[] = $objClause;
        }

        $this->DataSource = EventsCalendar::queryArray($objCondition, $objClauses);
    }

    protected function getCondition(?QQCondition $objAdditionalCondition = null)
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

    protected function getClauses($objAdditionalClauses = null)
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

    public function __get($strName)
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

    public function __set($strName, $mixValue)
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
