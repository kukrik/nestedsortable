<?php

    use QCubed\Exception\Caller;
    use QCubed\Exception\InvalidCast;
    use QCubed\Query\QQ;

    require(QCUBED_PROJECT_MODEL_GEN_DIR . '/FrontendTemplateLockingGen.php');

/**
* The FrontendTemplateLocking class defined here contains any
* customized code for the FrontendTemplateLocking class in the
* Object Relational Model. It represents the "frontend_template_locking" table
* in the database and extends from the code generated abstract FrontendTemplateLockingGen
* class, which contains all the basic CRUD-type functionality as well as
* basic methods to handle relationships and index-based loading.
*
* @package My QCubed Application
* @subpackage Model
*
*/
class FrontendTemplateLocking extends FrontendTemplateLockingGen
{
    /**
     * Default "to string" handler
     * Allows pages to _p()/echo()/print() this object, and to define the default
     * way this object would be outputted.
     *
     * @return string a nicely formatted string representation of this object
     */
    public function __toString(): string
    {
        return 'FrontendTemplateLocking Object ' . $this->primaryKey();
    }

    /**
     * Loads a FrontendTemplateLocking object by the given FrontendTemplateLockedId.
     *
     * @param null|int $intId The ID of the FrontendTemplateLocked to fetch.
     * @param mixed|null $objOptionalClauses Additional optional conditions or clauses to apply to the query.
     *
     * @return FrontendTemplateLocking|null The FrontendTemplateLocking object if found, or null if not found.
     * @throws Caller
     * @throws InvalidCast
     */
    public static function loadByFrontendTemplateLockedIdFromId(?int $intId, mixed $objOptionalClauses = null): ?FrontendTemplateLocking
    {
        // Use querySingle and catch the result
        $obj = FrontendTemplateLocking::querySingle(
            QQ::AndCondition(
                QQ::Equal(QQN::FrontendTemplateLocking()->FrontendTemplateLockedId, $intId)
            ), $objOptionalClauses
        );
        // If there is no row, return zero!
        return $obj ?: null;
    }

    // NOTE: Remember that when introducing a new custom function,
    // you must specify types for the function parameters as well as for the function return type!

    // Override or Create New load/count methods
    // (For obvious reasons, these methods are commented out...
    // But feel free to use these as a starting point)
/*

    public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses = null) {
        // This will return an array of FrontendTemplateLocking objects
        return FrontendTemplateLocking::queryArray(
            QQ::AndCondition(
                QQ::Equal(QQN::FrontendTemplateLocking()->Param1, $strParam1),
                QQ::GreaterThan(QQN::FrontendTemplateLocking()->Param2, $intParam2)
            ),
            $objOptionalClauses
        );
    }


    public static function loadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
        // This will return a single FrontendTemplateLocking object
        return FrontendTemplateLocking::querySingle(
            QQ::AndCondition(
                QQ::Equal(QQN::FrontendTemplateLocking()->Param1, $strParam1),
                QQ::GreaterThan(QQN::FrontendTemplateLocking()->Param2, $intParam2)
            ),
            $objOptionalClauses
        );
    }


    public static function countBySample($strParam1, $intParam2, $objOptionalClauses = null) {
        // This will return a count of FrontendTemplateLocking objects
        return FrontendTemplateLocking::queryCount(
            QQ::AndCondition(
                QQ::Equal(QQN::FrontendTemplateLocking()->Param1, $strParam1),
                QQ::Equal(QQN::FrontendTemplateLocking()->Param2, $intParam2)
            ),
            $objOptionalClauses
        );
    }


    public static function loadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
        // Performing the load manually (instead of using QCubed Query)

        // Get the Database Object for this Class
        $objDatabase = FrontendTemplateLocking::getDatabase();

        // Properly Escape All Input Parameters using Database->SqlVariable()
        $strParam1 = $objDatabase->SqlVariable($strParam1);
        $intParam2 = $objDatabase->SqlVariable($intParam2);

        // Setup the SQL Query
        $strQuery = sprintf('
            SELECT
                `frontend_template_locking`.*
            FROM
                `frontend_template_locking` AS `frontend_template_locking`
            WHERE
                param_1 = %s AND
                param_2 < %s',
            $strParam1, $intParam2);

        // Perform the Query and Instantiate the Result
        $objDbResult = $objDatabase->Query($strQuery);
        return FrontendTemplateLocking::instantiateDbResult($objDbResult);
    }
*/

    // Override or Create New Properties and Variables
    // For performance reasons, these variables and __set and __get override methods
    // are commented out.  But if you wish to implement or override any
    // of the data-generated properties, please feel free to uncomment them.
/*
    protected $strSomeNewProperty;

    protected function __set(string $strName, mixed $mixValue): void
    {
        switch ($strName) {
            case 'SomeNewProperty': return $this->strSomeNewProperty;

            default:
                try {
                    return parent::__get($strName);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }

    public function __set(string $strName, mixed $mixValue): void
    {
        switch ($strName) {
            case 'SomeNewProperty':
                try {
                    return ($this->strSomeNewProperty = \QCubed\Type::Cast($mixValue, \QCubed\Type::String));
                } catch (QInvalidCastException $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }

            default:
                try {
                    return (parent::__set($strName, $mixValue));
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
        }
    }
*/

}
