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

class SportsCalendarTable extends \QCubed\Plugin\Control\VauuTable
{
    protected $objCondition;
    protected $objClauses;

    public $colYear;
    public $colSportsCalendarGroup;
    public $colTitle;
    public $colSportsArea;
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
        $this->watch(QQN::SportsCalendar());
    }

    public function createColumns()
    {
        $this->colYear = $this->createNodeColumn(t("Year"), QQN::SportsCalendar()->Year);
        $this->colYear->CellStyler->Width = '5%';
        $this->colSportsCalendarGroup = $this->createNodeColumn(t("Sports calendar group"), QQN::SportsCalendar()->MenuContentGroupName);
        $this->colSportsCalendarGroup->OrderByClause = QQ::orderBy(QQN::SportsCalendar()->MenuContentGroupName, false);
        $this->colSportsCalendarGroup->ReverseOrderByClause = QQ::orderBy(QQN::SportsCalendar()->MenuContentGroupName, true);
        $this->colSportsCalendarGroup->CellStyler->Width = '15%';
        $this->colTitle = $this->createNodeColumn("Title", QQN::SportsCalendar()->Title);
        $this->colTitle->CellStyler->Width = '25%';
        $this->colSportsArea = $this->createNodeColumn(t("Sport area"), QQN::SportsCalendar()->SportsAreas);
        $this->colSportsArea->CellStyler->Width = '10%';
        //$this->colChange = $this->createNodeColumn("Change", QQN::SportsCalendar()->EventsChanges);
        //$this->colChange->CellStyler->Width = '8%';
        $this->colDateEvent = $this->createCallableColumn(t('Date of event'), [$this, 'DateEvent_render']);
        $this->colDateEvent->HtmlEntities = false;
        $this->colDateEvent->CellStyler->Width = '15%';
        $this->colStatusObject = $this->createNodeColumn(t("Status"), QQN::SportsCalendar()->StatusObject);
        $this->colStatusObject->HtmlEntities = false;
        //$this->colStatusObject->CellStyler->Width = '10%';
        $this->colPostDate = $this->createNodeColumn(t("Created"), QQN::SportsCalendar()->PostDate);
        $this->colPostDate->Format = 'DD.MM.YYYY hhhh:mm';
        $this->colPostDate->CellStyler->Width = '15%';
        $this->colAuthor = $this->createNodeColumn(t("Author"), QQN::SportsCalendar()->Author);
        //$this->colAuthor->CellStyler->Width = '15%';
    }

    public function DateEvent_render(SportsCalendar $objSportsCalendar)
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
    }

    public function bindData(?QQCondition $objAdditionalCondition = null, $objAdditionalClauses = null)
    {
        $objCondition = $this->getCondition($objAdditionalCondition);
        $objClauses = $this->getClauses($objAdditionalClauses);

        if ($this->Paginator) {
            $this->TotalItemCount = SportsCalendar::queryCount($objCondition, $objClauses);
        }

        if ($objClause = $this->OrderByClause) {
            $objClauses[] = $objClause;
        }

        if ($objClause = $this->LimitClause) {
            $objClauses[] = $objClause;
        }

        $this->DataSource = SportsCalendar::queryArray($objCondition, $objClauses);
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
