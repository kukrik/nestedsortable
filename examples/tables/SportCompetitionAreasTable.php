<?php

use QCubed\Query\Condition\ConditionInterface as QQCondition;
use QCubed\Query\Clause\ClauseInterface as QQClause;
use QCubed\Table\NodeColumn;
use QCubed\Project\Control\ControlBase;
use QCubed\Project\Control\FormBase;
use QCubed\Type;
use QCubed\Exception\Caller;
use QCubed\Query\QQ;
use QCubed\Project\Application;


class CompetitionAreasTable extends \QCubed\Plugin\Control\VauuTable
{
	protected $objCondition;
	protected $objClauses;

	public $colName;
    public $colUnit;
    public $colIsDetailedResult;
	public $colIsEnabled;
    public $colPostDate;
    public $colPostUpdateDate;

    public $colResult;

	public function __construct($objParent, $strControlId = null)
	{
		parent::__construct($objParent, $strControlId);
		$this->setDataBinder('bindData', $this);
		$this->watch(QQN::SportsCompetitionAreas());
	}

	public function createColumns()
	{
        $this->colName = $this->createNodeColumn(t("Sport area"), QQN::SportsCompetitionAreas()->Name);
        $this->colName->OrderByClause = QQ::orderBy(QQN::SportsCompetitionAreas()->Name, false);
        $this->colName->ReverseOrderByClause = QQ::orderBy(QQN::SportsCompetitionAreas()->Name, true);
		$this->colName->CellStyler->Width = '25%';
        $this->colUnit = $this->createNodeColumn(t("Unit"), QQN::SportsCompetitionAreas()->Unit);
        $this->colUnit->CellStyler->Width = '15%';
        $this->colIsDetailedResult = $this->createNodeColumn(t("Is detailed result"), QQN::SportsCompetitionAreas()->IsDetailedResultObject);
        $this->colIsDetailedResult->HtmlEntities = false;
        $this->colIsDetailedResult->CellStyler->Width = '15%';
		$this->colIsEnabled = $this->createNodeColumn(t("Is enabled"), QQN::SportsCompetitionAreas()->IsEnabledObject);
		$this->colIsEnabled->HtmlEntities = false;
		$this->colIsEnabled->CellStyler->Width = '15%';
        $this->colPostDate = $this->createNodeColumn(t("Created"), QQN::SportsCompetitionAreas()->PostDate);
        $this->colPostDate->Format = 'DD.MM.YYYY hhhh:mm';
		$this->colPostDate->CellStyler->Width = '15%';
        $this->colPostUpdateDate = $this->createNodeColumn(t("Modified"), QQN::SportsCompetitionAreas()->PostUpdateDate);
        $this->colPostUpdateDate->Format = 'DD.MM.YYYY hhhh:mm';
		$this->colPostUpdateDate->CellStyler->Width = '15%';
    }

	public function bindData(?QQCondition $objAdditionalCondition = null, $objAdditionalClauses = null)
	{
		$objCondition = $this->getCondition($objAdditionalCondition);
		$objClauses = $this->getClauses($objAdditionalClauses);

		if ($this->Paginator) {
			$this->TotalItemCount = SportsCompetitionAreas::queryCount($objCondition, $objClauses);
		}

		if ($objClause = $this->OrderByClause) {
			$objClauses[] = $objClause;
		}

		if ($objClause = $this->LimitClause) {
			$objClauses[] = $objClause;
		}

        $this->DataSource = SportsCompetitionAreas::queryArray($objCondition, $objClauses);
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
