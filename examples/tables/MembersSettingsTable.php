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


class MembersSettingsTable extends \QCubed\Plugin\Control\VauuTable
{
	protected $objCondition;
	protected $objClauses;

	public $colName;
    public $colTitle;
	public $colIsReserved;
    public $colStatus;
	public $colPostDate;
	public $colPostUpdateDate;

	public function __construct($objParent, $strControlId = null)
	{
		parent::__construct($objParent, $strControlId);
		$this->setDataBinder('bindData', $this);
		$this->watch(QQN::MembersSettings());
	}

	public function createColumns()
	{
		$this->colName = $this->createNodeColumn(t("Menu text"), QQN::MembersSettings()->Name);
		$this->colName->CellStyler->Width = '15%';

        $this->colTitle = $this->createNodeColumn(t("Title"), QQN::MembersSettings()->Title);
        $this->colTitle->CellStyler->Width = '36%';

		$this->colIsReserved = $this->createNodeColumn(t("Is reserved"), QQN::MembersSettings()->IsReservedObject);
		$this->colIsReserved->HtmlEntities = false;
		$this->colIsReserved->CellStyler->Width = '12%';

        $this->colStatus = $this->createNodeColumn(t("Status"), QQN::MembersSettings()->StatusObject);
        $this->colStatus->HtmlEntities = false;
        $this->colStatus->CellStyler->Width = '12%';

        $this->colPostDate = $this->createNodeColumn(t("Created"), QQN::MembersSettings()->PostDate);
        $this->colPostDate->Format = 'DD.MM.YYYY hhhh:mm';
		$this->colPostDate->CellStyler->Width = '12%';

        $this->colPostUpdateDate = $this->createNodeColumn(t("Modified"), QQN::MembersSettings()->PostUpdateDate);
        $this->colPostUpdateDate->Format = 'DD.MM.YYYY hhhh:mm';
		$this->colPostUpdateDate->CellStyler->Width = '12%';
    }

	public function bindData(?QQCondition $objAdditionalCondition = null, $objAdditionalClauses = null)
	{
		$objCondition = $this->getCondition($objAdditionalCondition);
		$objClauses = $this->getClauses($objAdditionalClauses);

		if ($this->Paginator) {
			$this->TotalItemCount = MembersSettings::queryCount($objCondition, $objClauses);
		}

		if ($objClause = $this->OrderByClause) {
			$objClauses[] = $objClause;
		}

		if ($objClause = $this->LimitClause) {
			$objClauses[] = $objClause;
		}

		$this->DataSource = MembersSettings::queryArray($objCondition, $objClauses);
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
