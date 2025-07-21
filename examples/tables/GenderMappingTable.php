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

class GenderMappingTable extends \QCubed\Plugin\Control\VauuTable
{
	protected $objCondition;
	protected $objClauses;

	public $colAgeCategory;
    public $colAthleteGender;
    public $colGender;
    public $colLocking;
	public $colStatus;
    public $colPostDate;
    public $colPostUpdateDate;


	public function __construct($objParent, $strControlId = null)
	{
		parent::__construct($objParent, $strControlId);
		$this->setDataBinder('bindData', $this);
		$this->watch(QQN::AgeCategoryGender());
	}

	public function createColumns()
	{
        $this->colAgeCategory = $this->createNodeColumn(t("Age group"), QQN::AgeCategoryGender()->AgeCategory);
		$this->colAgeCategory->CellStyler->Width = '15%';

        $this->colAthleteGender = $this->createNodeColumn(t("Athlete gender"), QQN::AgeCategoryGender()->AthleteGender);
        $this->colAthleteGender->CellStyler->Width = '14%';

        $this->colGender = $this->createNodeColumn(t("Gender group"), QQN::AgeCategoryGender()->Gender);
        $this->colGender->CellStyler->Width = '15%';

        $this->colStatus = $this->createNodeColumn(t("Status"), QQN::AgeCategoryGender()->StatusObject);
        $this->colStatus->HtmlEntities = false;
        $this->colStatus->CellStyler->Width = '15%';

        $this->colLocking = $this->createNodeColumn("Is locked", QQN::AgeCategoryGender()->IsLockedObject);
        $this->colLocking->HtmlEntities = false;
        $this->colLocking->CellStyler->Width = '10%';

        $this->colPostDate = $this->createNodeColumn("Created", QQN::AgeCategoryGender()->PostDate);
        $this->colPostDate->Format = 'DD.MM.YYYY hhhh:mm';
        $this->colPostDate->CellStyler->Width = '15%';

        $this->colPostUpdateDate = $this->createNodeColumn("Modified", QQN::AgeCategoryGender()->PostUpdateDate);
        $this->colPostUpdateDate->Format = 'DD.MM.YYYY hhhh:mm';
        $this->colPostUpdateDate->CellStyler->Width = '15%';
    }

	public function bindData(?QQCondition $objAdditionalCondition = null, $objAdditionalClauses = null)
	{
		$objCondition = $this->getCondition($objAdditionalCondition);
		$objClauses = $this->getClauses($objAdditionalClauses);

		if ($this->Paginator) {
			$this->TotalItemCount = AgeCategoryGender::queryCount($objCondition, $objClauses);
		}

		if ($objClause = $this->OrderByClause) {
			$objClauses[] = $objClause;
		}

		if ($objClause = $this->LimitClause) {
			$objClauses[] = $objClause;
		}

		$this->DataSource = AgeCategoryGender::queryArray($objCondition, $objClauses);
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
