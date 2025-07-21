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

class NewsTable extends \QCubed\Plugin\Control\VauuTable
{
	protected $objCondition;
	protected $objClauses;
    protected $strTempUrl = APP_UPLOADS_TEMP_URL;

	public $colPicture;
    public $colNewsGroup;
	public $colTitle;
    public $colChanges;
	public $colCategory;
    public $colStatusObject;
	public $colWrittenStatus;
	public $colPostDate;
	public $colAvailableFrom;
    public $colExpiryDate;
	public $colAuthor;

	public function __construct($objParent, $strControlId = null)
	{
		parent::__construct($objParent, $strControlId);
		$this->setDataBinder('bindData', $this);
		$this->watch(QQN::News());
	}

	public function createColumns() 
	{
        $this->colPicture = $this->createCallableColumn(t('Picture'), [$this, 'View_render']);
        $this->colPicture->HtmlEntities = false;
        $this->colPicture->CellStyler->Width = '4%';
        $this->colNewsGroup = $this->createNodeColumn(t("News group"), QQN::News()->GroupTitle);
        $this->colNewsGroup->CellStyler->Width = '10%';
		$this->colTitle = $this->createNodeColumn(t("Title"), QQN::News()->Title);
        $this->colTitle->CellStyler->Width = '25%';

        $this->colCategory = $this->createNodeColumn("Category", QQN::News()->Category);
        $this->colStatusObject = $this->createNodeColumn(t("Status"), QQN::News()->StatusObject);
        $this->colStatusObject->HtmlEntities = false;
		$this->colPostDate = $this->createNodeColumn(t("Created"), QQN::News()->PostDate);
		$this->colPostDate->OrderByClause = QQ::orderBy(QQN::news()->PostDate, false);
		$this->colPostDate->ReverseOrderByClause = QQ::orderBy(QQN::News()->PostDate, true);
		$this->colPostDate->Format = 'DD.MM.YYYY hhhh:mm';
		$this->colAvailableFrom = $this->createNodeColumn(t("Available from"), QQN::News()->AvailableFrom);
		$this->colAvailableFrom->Format = 'DD.MM.YYYY hhhh:mm';
        $this->colExpiryDate = $this->createNodeColumn(t("Expiry date"), QQN::News()->ExpiryDate);
        $this->colExpiryDate->Format = 'DD.MM.YYYY hhhh:mm';
		$this->colAuthor = $this->createNodeColumn(t("Author"), QQN::News()->Author);
	}

    public function View_render(News $objNews)
    {
        if ($objNews->getPictureId()) {
            $objFiles = Files::load($objNews->getPictureId());

            $strHtm = '<span class="news-preview">';
            $strHtm .= '<img src="' . $this->strTempUrl . '/_files/thumbnail' . $objFiles->getPath() . '">';
            $strHtm .= '</span>';
            return $strHtm;
        }
    }

	public function bindData(?QQCondition $objAdditionalCondition = null, $objAdditionalClauses = null)
	{
		$objCondition = $this->getCondition($objAdditionalCondition);
		$objClauses = $this->getClauses($objAdditionalClauses);

		if ($this->Paginator) {
			$this->TotalItemCount = News::queryCount($objCondition, $objClauses);
		}

		if ($objClause = $this->OrderByClause) {
			$objClauses[] = $objClause;
		}

		if ($objClause = $this->LimitClause) {
			$objClauses[] = $objClause;
		}

		$this->DataSource = News::queryArray($objCondition, $objClauses);
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