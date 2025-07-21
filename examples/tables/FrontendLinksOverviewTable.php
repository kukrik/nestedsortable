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


class FrontendLinksOverviewTable extends \QCubed\Plugin\Control\VauuTable
{
	protected $objCondition;
	protected $objClauses;

	public $colId;
	public $colLinkedId;
    public $colCroupedId;
	public $colSelectTypeName;
    public $colClassName;
    public $colTemplatePath;
    public $colTitle;
	public $colFrontendTitleSlug;
	public $colIsActivated;


	public function __construct($objParent, $strControlId = null)
	{
		parent::__construct($objParent, $strControlId);
		$this->setDataBinder('bindData', $this);
		$this->watch(QQN::FrontendOptions());
	}

	public function createColumns()
	{
		$this->colId = $this->createNodeColumn(t("Id"), QQN::FrontendLinks()->Id);
		$this->colId->OrderByClause = QQ::orderBy(QQN::FrontendLinks()->Id, false);
		$this->colId->ReverseOrderByClause = QQ::orderBy(QQN::FrontendLinks()->Id, true);
		$this->colLinkedId = $this->createNodeColumn(t("Linked id"), QQN::FrontendLinks()->LinkedId);
        $this->colCroupedId = $this->createNodeColumn(t("Crouped id"), QQN::FrontendLinks()->GroupedId);

        //$this->colSelectTypeName = $this->createNodeColumn(t("Selected type name"), QQN::FrontendLinks()->ContentTypesManagament);
        $this->colClassName = $this->createNodeColumn(t("Selected class name"), QQN::FrontendLinks()->FrontendClassName);
        $this->colTemplatePath = $this->createNodeColumn(t("Selected template path"), QQN::FrontendLinks()->FrontendTemplatePath);
        $this->colTitle = $this->createNodeColumn(t("Title"), QQN::FrontendLinks()->Title);
        $this->colFrontendTitleSlug = $this->createNodeColumn(t("Frontend title slug"), QQN::FrontendLinks()->FrontendTitleSlug);
		$this->colIsActivated = $this->createNodeColumn(t("Is activated"), QQN::FrontendLinks()->IsActivatedObject);
		$this->colIsActivated->HtmlEntities = false;
        $this->colIsActivated->CellStyler->Width = '10%';
	}

	public function bindData(?QQCondition $objAdditionalCondition = null, $objAdditionalClauses = null)
	{
		$objCondition = $this->getCondition($objAdditionalCondition);
		$objClauses = $this->getClauses($objAdditionalClauses);

		if ($this->Paginator) {
			$this->TotalItemCount = FrontendLinks::queryCount($objCondition, $objClauses);
		}

		if ($objClause = $this->OrderByClause) {
			$objClauses[] = $objClause;
		}

		if ($objClause = $this->LimitClause) {
			$objClauses[] = $objClause;
		}

		$this->DataSource = FrontendLinks::queryArray($objCondition, $objClauses);
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
