<?php

use QCubed\Query\Condition\ConditionInterface as QQCondition;
use QCubed\Query\Clause\ClauseInterface as QQClause;
use QCubed\Table\NodeColumn;
use QCubed\Table\CallableColumn;
use QCubed\Project\Control\ControlBase as QControl;
use QCubed\Project\Control\FormBase as QForm;
use QCubed\Type;
use QCubed\Exception\Caller;
use QCubed\Query\QQ;
use QCubed\Project\Application;

class StatisticsTable extends \QCubed\Plugin\Control\VauuTable
{
	protected $objCondition;
	protected $objClauses;

    public $colGroupTitle;
    public $colTitle;
    public $colAuthor;
    public $colStatusObject;
    public $colPostDate;
    public $colPostUpdateDate;

    public function __construct($objParent, $strControlId = null)
	{
		parent::__construct($objParent, $strControlId);
		$this->setDataBinder('bindData', $this);
        $this->watch(QQN::StatisticsSettings());
	}

	public function createColumns()
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

	public function bindData(?QQCondition $objAdditionalCondition = null, $objAdditionalClauses = null)
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
