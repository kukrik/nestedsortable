<?php

namespace QCubed\Plugin\Control;

use QCubed\Application\t;
use QCubed\Bootstrap as Bs;
use QCubed\Exception\Caller;
use QCubed\Exception\InvalidCast;
use QCubed\Project\Application;
use QCubed\Project\Control\TextBox;
use QCubed\QDateTime;
use QCubed\Type;

/**
 * Class DateTimeTextBox
 *
 *  A specialized textbox control for handling date and time inputs.
 *  Includes functionality for format validation and
 *  the ability to save and retrieve dates as QDateTime objects.
 *
 *  There are a couple of ways to check the date format. The simplest is as follows:
 *
 *  In your formCreate():
 *  <code>
 *  $this->dtxDateTimeTextBox = new QCubed\Plugin\Control\DateTimeTextBox($this);
 *  $this->dtxDateTimeTextBox->Mode = 'date';
 *  $this->dtxDateTimeTextBox->DateTimeFormat = 'DD.MM.YYYY'; // Format setting
 *  $this->dtxDateTimeTextBox->Placeholder = 'dd.mm.yyyy';   // If you want a hint for input
 *  </code>
 *
 *  If you want to check the date format:
 *  <code>
 *  // We automatically check based on the format specified in the class
 *  if ($this->dtxDateTimeTextBox->validateFormat()) {
 *      Application::displayAlert('The date is in the correct format!'); // Success notification
 *  } else {
 *      Application::displayAlert('The date is in the wrong format!'); // Error notification
 *  }
 *  </code>
 *
 * Additional information! This validateFormat() gives you the ability to check, but it also gives you another way
 * to trigger other events you want, etc...
 *
 * @property string $Mode Possible values: 'date', 'datetime', 'time'.
 * @property string $DateTimeFormat - The format for date validation (e.g., "DD.MM.YYYY").
 * @property QDateTime $DateTime - The QDateTime object representing the entered date.
 * @property string $LabelForInvalid - A message to display when validation fails.
 */

class DateTimeTextBox extends Bs\TextBox
{
    protected $strMode = 'date'; // Possible values: 'date', 'datetime', 'time'
    protected $strDateTimeFormat = "DD.MM.YYYY";
    protected $dttDateTime = null;
    protected $strLabelForInvalid = 'Invalid format.';

    /**
     * Parses POST data to update the control's value if it is present in the POST request.
     *
     * @return void
     */
    public function parsePostData()
    {
        // Check to see if this Control's Value was passed in via the POST data
        if (array_key_exists($this->strControlId, $_POST)) {
            parent::parsePostData();
            $this->dttDateTime = self::parseForDateTimeValue($this->strText);
        }
    }

    /**
     * Parses the provided text for a datetime value and returns a QDateTime object.
     *
     * @param string|null $strText The input text to parse for a datetime value. Can be null or an empty string.
     * @return QDateTime|null Returns a QDateTime object if the text is successfully parsed, or null if the input is empty.
     */
    protected static function parseForDateTimeValue($strText)
    {
        if (empty($strText)) {
            return null;
        }

        $strText = trim($strText);
        return new QDateTime($strText);
    }

    /**
     * Validates the format of a date-time string based on a specified QCubed format.
     *
     * This method ensures that the provided date-time string adheres to the expected
     * QCubed date format by converting it to its PHP equivalent. It also handles
     * edge cases such as short year formats and verifies if a \DateTime object can be
     * correctly created from the input and format.
     *
     * @return bool Returns true if the date-time string is valid and matches the format; otherwise, false.
     */
    public function validateFormat(): bool
    {
        // If the date format or value is empty
        if (!$this->strDateTimeFormat || empty($this->strText)) {
            return false;
        }

        // Converting PHP format from QCubed format: "DD.MM.YY -> d.m.y" or "DD.MM.YYYY -> d.m.Y"
        $phpFormat = $this->convertQCubedFormatToPhpFormat($this->strDateTimeFormat);

        // If the format uses 'YY', update both the value and the format
        if (strpos($this->strDateTimeFormat, 'YY') !== false) {
            $inputValue = $this->expandShortYearFormat($this->strText, $phpFormat);
            $phpFormat = str_replace('y', 'Y', $phpFormat); //We are improving the format
        } else {
            $inputValue = trim($this->strText); // We use the input directly
        }

        // We verify that DateTime can create an object according to the format
        $date = \DateTime::createFromFormat($phpFormat, $inputValue);

        // Final check: whether the date format and input are correct
        return $date && $date->format($phpFormat) === $inputValue;
    }

    /**
     * Converts a QCubed date and time format string to a PHP-compatible format string.
     *
     * @param string $qcubedFormat The QCubed format string to be converted.
     * @return string The PHP-compatible format string.
     */
    protected function convertQCubedFormatToPhpFormat(string $qcubedFormat): string
    {
        return str_replace(['DD', 'MM', 'YYYY', 'YY', 'HH', 'mm'], ['d', 'm', 'Y', 'y', 'H', 'i'], $qcubedFormat);
    }

    /**
     * Expands a 2-digit year in a given input value to a 4-digit year based on the provided PHP date format.
     * If the 2-digit year is greater than or equal to 50, it assumes the year is in the 1900s.
     * Otherwise, it assumes the year is in the 2000s. The method tries to match and correct the year according
     * to the given format or common date patterns if applicable.
     *
     * @param mixed $inputValue The input value containing a potential short year to be expanded.
     * @param string $phpFormat The PHP date format used to interpret and expand the year.
     * @return string The input value with the expanded year formatted or the original input value if unable to process.
     */
    protected function expandShortYearFormat($inputValue, $phpFormat): string
    {
        // If input or format is empty or missing, return a safe empty string
        if (empty($inputValue) || empty($phpFormat)) {
            return (string)$inputValue;
        }

        // We check if the format contains 2-digit years ('y')
        if (strpos($phpFormat, 'y') !== false) {
            // Let's try to create a DateTime object initially
            $date = \DateTime::createFromFormat($phpFormat, $inputValue);

            if ($date) {
                // Successful: Converting the year
                $year = (int)$date->format('y');

                // If year is >= 50, then 19YY, otherwise 20YY
                $fullYear = ($year >= 50) ? 1900 + $year : 2000 + $year;

                // We format the input with the new replaced year back
                return str_replace($date->format('y'), $fullYear, $inputValue);
            } else {
                // If the format does not match, we will accept a verified correction.
                if (preg_match('#^(?<day>\d{2})[.\-/\/ ](?<month>\d{2})[.\-/\/ ](?<year>\d{2})$#', $inputValue, $matches)) {
                    $day = $matches['day'];
                    $month = $matches['month'];
                    $shortYear = (int)$matches['year'];

                    // Let's restore the YYYY format
                    $fullYear = ($shortYear >= 50) ? 1900 + $shortYear : 2000 + $shortYear;

                    // We return the corrected value
                    return sprintf('%02d.%02d.%04d', $day, $month, $fullYear);
                }
            }
        }

        // If other changes are not relevant, return the original input.
        return (string)$inputValue;
    }

    /**
     * Magic getter for properties.
     *
     * @param string $strName The property name.
     * @return mixed The property value.
     * @throws Caller Exception thrown if property is not found.
     */
    public function __get($strName)
    {
        switch ($strName) {
            case 'Mode': return $this->strMode;
            case 'DateTimeFormat': return $this->strDateTimeFormat;
            case 'DateTime': return $this->dttDateTime;
            case 'LabelForInvalid': return $this->strLabelForInvalid;

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
     * Magic setter for properties.
     *
     * Sets various properties for the control, such as `DateTimeFormat`, `DateTime`, and `LabelForInvalid`.
     *
     * @param string $strName The property name.
     * @param mixed $mixValue The property value.
     * @return void
     * @throws InvalidCast If the value cannot be correctly cast.
     */
    public function __set($strName, $mixValue)
    {
        $this->blnModified = true;

        switch ($strName) {
            case 'Mode':
                if (in_array($mixValue, ['date', 'datetime', 'time'], true)) {
                    $this->strMode = $mixValue;
                } else {
                    throw new \Exception("Invalid mode specified: $mixValue");
                }
                break;

            case 'DateTimeFormat':
                try {
                    $this->strDateTimeFormat = Type::cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            case 'DateTime':
                try {
                    $this->dttDateTime = Type::cast($mixValue, Type::DATE_TIME);

                    if (!$this->dttDateTime || !$this->strDateTimeFormat) {
                        parent::__set('Text', '');
                    } else {
                        // Uses full format with static format (we add links)
                        parent::__set('Text', $this->dttDateTime->qFormat($this->strDateTimeFormat));
                    }
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            case 'Text':
                // We analyze input formatted as text
                $this->dttDateTime = self::parseForDateTimeValue($mixValue);

                if (strpos($this->strDateTimeFormat, 'YY') !== false) {
                    // Uses abbreviated year search
                    $mixValue = $this->expandShortYearFormat($mixValue, $this->convertQCubedFormatToPhpFormat($this->strDateTimeFormat));
                }

                parent::__set('Text', $mixValue);
                break;

            case 'LabelForInvalid':
                try {
                    $this->strLabelForInvalid = Type::cast($mixValue, Type::STRING);
                } catch (InvalidCast $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;

            default:
                try {
                    parent::__set($strName, $mixValue);
                } catch (Caller $objExc) {
                    $objExc->incrementOffset();
                    throw $objExc;
                }
                break;
        }
    }
}
