<?php

namespace App\Models;

use PDO;
use \Core\View;
use \App\Auth;


class Incomes extends \Core\Model
{
    /**
     * Error messages
     *
     * @var array
     */
    public $errors = [];
    public $info = [];

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

            /*
            $sql = "SELECT incomes_category_assigned_to_users.id FROM incomes_category_assigned_to_users, users
            WHERE incomes_category_assigned_to_users.user_id = users.id AND user_id = '$userID' AND name='$inputCategoryOfIncome'";
*/
            $db = static::getDB();
            /*
            $stmt = $db->prepare($sql);
            $stmt->execute();

            $inputIncomeId = $stmt->fetchColumn();
*/
            $sql = "INSERT INTO incomes VALUES
            (NULL, :userId, :incomeCategory, :amount, :dateOfIncome, :comment)";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':userId', $userID, PDO::PARAM_INT);
            $stmt->bindValue(':incomeCategory', $inputCategoryOfIncome, PDO::PARAM_INT);
            $stmt->bindValue(':amount', $inputAmount, PDO::PARAM_STR);
            $stmt->bindValue(':dateOfIncome', $inputDate, PDO::PARAM_STR);
            $stmt->bindValue(':comment', $inputComment, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    public function addCategory()
    {

        $this->validateAddCategory();

        if (empty($this->info['addName'])) {

            $userID = $_SESSION['user_id'];
            $inputCategoryOfIncome = $_POST['addIncomeCategory'];

            $db = static::getDB();

            $sql = "INSERT INTO incomes_category_assigned_to_users
            VALUES (NULL, :userId, :incomeCategoryName)";
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':userId', $userID, PDO::PARAM_INT);
            $stmt->bindValue(':incomeCategoryName', $inputCategoryOfIncome, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    public function deleteCategory()
    {

        if (isset($_POST['deleteCategoryId'])) {
            $deleteFlag = true;
            $userID = $_SESSION['user_id'];
            $inputCategoryOfIncomeID = $_POST['deleteCategoryId'];

            $db = static::getDB();

            /**Delete records from incomes */
            $sql = "DELETE FROM incomes
            WHERE income_category_assigned_to_user_id = :incomeCategoryId
            AND user_id = :userId";
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':userId', $userID, PDO::PARAM_INT);
            $stmt->bindValue(':incomeCategoryId', $inputCategoryOfIncomeID, PDO::PARAM_INT);

            $stmt->execute();

            /**Delete incomes category */
            if ($stmt == true) {
                $sql = "DELETE FROM incomes_category_assigned_to_users
            WHERE id = :incomeCategoryId
            AND user_id = :userId";
                $stmt = $db->prepare($sql);

                $stmt->bindValue(':userId', $userID, PDO::PARAM_INT);
                $stmt->bindValue(':incomeCategoryId', $inputCategoryOfIncomeID, PDO::PARAM_INT);

                $stmt->execute();
                $this->info['deleteStatus'] = true;
                return $this->info['deleteStatus'];
            }
        }

        $this->info['deleteStatus'] = false;
        return  $this->info['deleteStatus'];
    }

    public function edit()
    {
        $this->validateEditCategory();

        if (empty($this->info['name'])) {

            $userID = $_SESSION['user_id'];
            $inputCategoryOfIncomeID = $_POST['editCategoryId'];
            $inputCategoryOfIncome = $_POST['editCategoryName'];

            $db = static::getDB();

            $sql = "UPDATE incomes_category_assigned_to_users
            SET name = :incomeCategoryName
            WHERE id = :incomeCategoryId
            AND user_id = :userId";
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':userId', $userID, PDO::PARAM_INT);
            $stmt->bindValue(':incomeCategoryId', $inputCategoryOfIncomeID, PDO::PARAM_INT);
            $stmt->bindValue(':incomeCategoryName', $inputCategoryOfIncome, PDO::PARAM_STR);

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

    public function validateEditCategory()
    {

        if (isset($_POST['editCategoryName'])) {
            $this->info['inputName'] = $_POST['editCategoryName'];
            $inputCategoryOfIncome = $_POST['editCategoryName'];

            $inputCategoryOfIncome = mb_strtolower($inputCategoryOfIncome, 'UTF-8');
            $existingIncomeCategories = static::getIncomeCategoriesAssignedToUser();

            foreach ($existingIncomeCategories as $incomesData) {
                foreach ($incomesData as $name['name']) {

                    $name['name'] = mb_strtolower($name['name'], 'UTF-8');

                    if ($name['name'] == $inputCategoryOfIncome) {
                        $this->info['name'] = "Kategoria o tej nazwie już istnieje!";
                    }
                }
            }

            $existingIncomeCategories = static::changeFromPolishToEnglish($existingIncomeCategories);

            foreach ($existingIncomeCategories as $incomesData) {
                foreach ($incomesData as $name['name']) {

                    $name['name'] = mb_strtolower($name['name'], 'UTF-8');

                    if ($name['name'] == $inputCategoryOfIncome) {
                        $this->info['name'] = "Kategoria o tej nazwie już istnieje!";
                    }
                }
            }

            if ((strlen($inputCategoryOfIncome) > 35) || (strlen($inputCategoryOfIncome) < 2)) {
                $this->info['name'] = "Nazwa kategorii powinna zawierać więcej niż 1 znak i mniej niż 35 znaków";
            }
        }
    }

    public function validateAddCategory()
    {

        if (isset($_POST['addIncomeCategory'])) {
            $this->info['inputName'] = $_POST['addIncomeCategory'];
            $inputCategoryOfIncome = $_POST['addIncomeCategory'];

            $inputCategoryOfIncome = mb_strtolower($inputCategoryOfIncome, 'UTF-8');
            $existingIncomeCategories = static::getIncomeCategoriesAssignedToUser();

            foreach ($existingIncomeCategories as $incomesData) {
                foreach ($incomesData as $name['name']) {

                    $name['name'] = mb_strtolower($name['name'], 'UTF-8');

                    if ($name['name'] == $inputCategoryOfIncome) {
                        $this->info['addName'] = "Kategoria o tej nazwie już istnieje!";
                    }
                }
            }

            $existingIncomeCategories = static::changeFromPolishToEnglish($existingIncomeCategories);

            foreach ($existingIncomeCategories as $incomesData) {
                foreach ($incomesData as $name['name']) {

                    $name['name'] = mb_strtolower($name['name'], 'UTF-8');

                    if ($name['name'] == $inputCategoryOfIncome) {
                        $this->info['addName'] = "Kategoria o tej nazwie już istnieje!";
                    }
                }
            }

            if ((strlen($inputCategoryOfIncome) > 35) || (strlen($inputCategoryOfIncome) < 2)) {
                $this->info['addName'] = "Nazwa kategorii powinna zawierać więcej niż 1 znak i mniej niż 35 znaków";
            }
        }
    }

    public function currentMonthIncomes($user_ID)
    {
        $sql = "SELECT incomes_category_assigned_to_users.name, incomes.amount, incomes.date_of_income, incomes.income_comment
            FROM incomes, incomes_category_assigned_to_users
            WHERE incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id
            AND incomes.user_id = incomes_category_assigned_to_users.user_id
            AND incomes.user_id = '$user_ID'
            AND MONTH(date_of_income) = MONTH(CURRENT_DATE)
            AND YEAR(date_of_income) = YEAR(CURRENT_DATE)
            ORDER BY DATE(date_of_income) DESC";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $incomes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return static::changeFromEnglishToPolish($incomes);
    }

    public function lastMonthIncomes($user_ID)
    {

        $sql = "SELECT incomes_category_assigned_to_users.name, incomes.amount, incomes.date_of_income, incomes.income_comment
            FROM incomes, incomes_category_assigned_to_users
            WHERE incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id
            AND incomes.user_id = incomes_category_assigned_to_users.user_id
            AND incomes.user_id = '$user_ID'
            AND MONTH(date_of_income) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
            AND YEAR(date_of_income) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
            ORDER BY DATE(date_of_income) DESC";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $incomes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return static::changeFromEnglishToPolish($incomes);
    }

    public function customIncomes($user_ID, $userInputDateStart, $userInputDateEnd)
    {

        $sql = "SELECT incomes_category_assigned_to_users.name, incomes.amount, incomes.date_of_income, incomes.income_comment
            FROM incomes, incomes_category_assigned_to_users
            WHERE incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id
            AND incomes.user_id = incomes_category_assigned_to_users.user_id
            AND incomes.user_id = '$user_ID'
            AND date_of_income BETWEEN '$userInputDateStart' AND '$userInputDateEnd'
            ORDER BY DATE(date_of_income) DESC";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $incomes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return static::changeFromEnglishToPolish($incomes);
    }

    public function currentMonthIncomesByCategory($user_ID)
    {

        $sql = "SELECT SUM(incomes.amount) AS amount, incomes_category_assigned_to_users.name
        FROM incomes, incomes_category_assigned_to_users
        WHERE incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id
        AND incomes.user_id = incomes_category_assigned_to_users.user_id
        AND incomes.user_id = '$user_ID'
        AND MONTH(date_of_income) = MONTH(CURRENT_DATE)
        AND YEAR(date_of_income) = YEAR(CURRENT_DATE)
        GROUP BY incomes_category_assigned_to_users.name
        ORDER BY amount DESC";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $incomes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return static::changeFromEnglishToPolish($incomes);
    }

    public function lastMonthIncomesByCategory($user_ID)
    {

        $sql = "SELECT SUM(incomes.amount) AS amount, incomes_category_assigned_to_users.name
        FROM incomes, incomes_category_assigned_to_users
        WHERE incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id
        AND incomes.user_id = incomes_category_assigned_to_users.user_id
        AND incomes.user_id = '$user_ID'
        AND MONTH(date_of_income) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
        AND YEAR(date_of_income) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
        GROUP BY incomes_category_assigned_to_users.name
        ORDER BY amount DESC";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $incomes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return static::changeFromEnglishToPolish($incomes);
    }

    public function customIncomesByCategory($user_ID, $userInputDateStart, $userInputDateEnd)
    {

        $sql = "SELECT SUM(incomes.amount) AS amount, incomes_category_assigned_to_users.name
        FROM incomes, incomes_category_assigned_to_users
        WHERE incomes.income_category_assigned_to_user_id = incomes_category_assigned_to_users.id
        AND incomes.user_id = incomes_category_assigned_to_users.user_id
        AND incomes.user_id = '$user_ID'
        AND date_of_income BETWEEN '$userInputDateStart' AND '$userInputDateEnd'
        GROUP BY incomes_category_assigned_to_users.name
        ORDER BY amount DESC";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $incomes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return static::changeFromEnglishToPolish($incomes);
    }

    public static function changeFromEnglishToPolish($incomesDetailedData)
    {

        if (!$incomesDetailedData) return false;


        foreach ($incomesDetailedData as &$incomesDetailed) {
            foreach ($incomesDetailed as &$name['name']) {

                switch ($name['name']) {
                    case "Salary":
                        $name['name'] = "Wynagrodzenie";
                        break;
                    case "Interest":
                        $name['name'] = "Odsetki bankowe";
                        break;
                    case "Allegro":
                        $name['name'] = "Sprzedaż na allegro";
                        break;
                    case "Another":
                        $name['name'] = "Inne";
                        break;
                }
            }
        }

        return $incomesDetailedData;
    }

    public static function changeFromPolishToEnglish($incomesDetailedData)
    {
        if (!$incomesDetailedData) return false;


        foreach ($incomesDetailedData as &$incomesDetailed) {
            foreach ($incomesDetailed as &$name['name']) {

                switch ($name['name']) {
                    case "Wynagrodzenie":
                        $name['name'] = "Salary";
                        break;
                    case "Odsetki bankowe":
                        $name['name'] = "Interest";
                        break;
                    case "Sprzedaż na allegro":
                        $name['name'] = "Allegro";
                        break;
                    case "Inne":
                        $name['name'] = "Another";
                        break;
                }
            }
        }

        return $incomesDetailedData;
    }

    public static function getIncome($user_ID, $choice = "", $dateStart = "", $dateEnd = "")
    {
        $income = new Incomes;

        if (($choice == 'currentMonth') || ($choice == "")) {
            return $income->currentMonthIncomes($user_ID);
        } else if ($choice == 'lastMonth') {
            return $income->lastMonthIncomes($user_ID);
        } else if ($choice == 'custom') {
            return $income->customIncomes($user_ID, $dateStart, $dateEnd);
        }
    }

    public static function getIncomeByCategory($user_ID, $choice = "", $dateStart = "", $dateEnd = "")
    {
        $incomeByCategory = new Incomes;

        if (($choice == 'currentMonth') || ($choice == "")) {
            return $incomeByCategory->currentMonthIncomesByCategory($user_ID);
        } else if ($choice == 'lastMonth') {
            return $incomeByCategory->lastMonthIncomesByCategory($user_ID);
        } else if ($choice == 'custom') {
            return $incomeByCategory->customIncomesByCategory($user_ID, $dateStart, $dateEnd);
        }
    }

    public static function getIncomeByCategoryPieChartData()
    {
        if (isset($_SESSION['incomeByCategory'])) {

            if (($_SESSION['incomeByCategory'])) {
                $incomeByCategory = $_SESSION['incomeByCategory'];
                $dataPoints = [];
                $incomeSum = 0;

                foreach ($incomeByCategory as $income) {

                    $incomeSum += $income['amount'];
                }

                foreach ($incomeByCategory as $data) {

                    $incomePercentage = $data['amount'] / $incomeSum * 100;
                    $newArray = array("label" => $data['name'], "y" => $incomePercentage);
                    array_push($dataPoints, $newArray);
                }
                return $dataPoints;
            }
        }
    }

    public static function getIncomeCategoriesAssignedToUser()
    {
        $user = Auth::getUser();

        if ($user) {

            $user_ID = $user->id;

            $sql = "SELECT id, name
        FROM incomes_category_assigned_to_users
        WHERE incomes_category_assigned_to_users.user_id = '$user_ID'";

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->execute();

            $incomes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return static::changeFromEnglishToPolish($incomes);
        }
    }
}
