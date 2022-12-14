<?php

namespace App\Models;

use PDO;
use \Core\View;


class Incomes extends \Core\Model
{
    /**
     * Error messages
     *
     * @var array
     */
    public $errors = [];

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    public function save()
    {
        $this->validate();

        if (empty($this->errors)) {

            $userID = $_SESSION['user_id'];
            $inputCategoryOfIncome = $_POST['categoryOfIncome'];
            $inputAmount = $_POST['amount'];
            $inputDate = $_POST['date'];
            $inputComment = $_POST['comment'];


            $sql = "SELECT incomes_category_assigned_to_users.id FROM incomes_category_assigned_to_users, users
            WHERE incomes_category_assigned_to_users.user_id = users.id AND user_id = '$userID' AND name='$inputCategoryOfIncome'";

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->execute();

            $inputIncomeId = $stmt->fetchColumn();

            $sql = "INSERT INTO incomes VALUES
            (NULL, :userId, :incomeCategory, :amount, :dateOfIncome, :comment)";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':userId', $userID, PDO::PARAM_INT);
            $stmt->bindValue(':incomeCategory', $inputIncomeId, PDO::PARAM_INT);
            $stmt->bindValue(':amount', $inputAmount, PDO::PARAM_STR);
            $stmt->bindValue(':dateOfIncome', $inputDate, PDO::PARAM_STR);
            $stmt->bindValue(':comment', $inputComment, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    /**
     * Validate current property values, adding valiation error messages to the errors array property
     *
     * @return void
     */
    public function validate()
    {
        // Amount
        if (!isset($this->amount)) {
            $this->errors['amount'] = 'Należy wprowadzić kwotę!';
        }

        if (($this->amount) > 999999999) {
            $this->errors['amount'] = 'Należy wprowadzić rzeczywistą kwotę!';
        }

        // Date
        $minDate = "2000-01-01";
        $maxDate = date('Y-m-t');

        if (($_POST['date'] < $minDate) || ($_POST['date'] > $maxDate)) {
            $this->errors['date'] = "Data musi zawierać się między 01.01.2000 a ostatnim dniem bierzącego miesiąca tego roku!";
        }

        // Category of income
        if (!isset($this->categoryOfIncome)) {
            $this->errors['categoryOfIncome'] = 'Należy wybrać kategorię przychodu!';
        }

        // comment
        if ((strlen($this->comment)) > 50) {
            $this->errors['comment'] = 'Treść komentarza nie może przekroczyć 50 znaków!';
        }
    }
}
