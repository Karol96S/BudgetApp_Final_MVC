<?php

namespace App\Models;

use PDO;
use \Core\View;
use \App\Auth;


class Expenses extends \Core\Model
{
    /**
     * Error messages
     *
     * @var array
     */
    public $errors = [];
    public $info = [];
    public $status = [];

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
            $inputAmount = $_POST['amount'];
            $inputDate = $_POST['date'];
            $inputPaymentMethod = $_POST['paymentMethod'];
            $inputCategoryOfExpense = $_POST['categoryOfExpense'];
            $inputComment = $_POST['comment'];

            /*
            $sql = "SELECT expenses_category_assigned_to_users.id
            FROM expenses_category_assigned_to_users, users
            WHERE expenses_category_assigned_to_users.user_id = users.id
            AND user_id = '$userID'
            AND name='$inputCategoryOfExpense'";
*/
            $db = static::getDB();
            /*           $stmt = $db->prepare($sql);
            $stmt->execute();

            $inputExpenseId = $stmt->fetchColumn();

            $sql = "SELECT payment_methods_assigned_to_users.id FROM payment_methods_assigned_to_users, users
            WHERE payment_methods_assigned_to_users.user_id = users.id AND user_id = '$userID' AND name='$inputPaymentMethod'";
            $stmt = $db->prepare($sql);
            $stmt->execute();

            $inputPaymentId = $stmt->fetchColumn();
*/
            $sql = "INSERT INTO expenses VALUES
            (NULL, :userId, :expenseCategory, :paymentMethod, :amount, :dateOfExpense, :comment)";
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':userId', $userID, PDO::PARAM_INT);
            $stmt->bindValue(':expenseCategory', $inputCategoryOfExpense, PDO::PARAM_INT);
            $stmt->bindValue(':paymentMethod', $inputPaymentMethod);
            $stmt->bindValue(':amount', $inputAmount, PDO::PARAM_STR);
            $stmt->bindValue(':dateOfExpense', $inputDate, PDO::PARAM_STR);
            $stmt->bindValue(':comment', $inputComment, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    public function addCategory()
    {

        $this->validateAddCategory();

        if (empty($this->info['addName'])) {
            $this->status['add'] = true;

            $userID = $_SESSION['user_id'];
            $inputCategoryOfExpense = $_POST['addExpenseCategory'];

            if (!isset($_POST['addExpenseLimit']) || ($_POST['addExpenseLimit']) < 0 || ($_POST['addExpenseLimit'] == "")) {
                $inputExpenseLimit = "0.00";
            } else {
                $inputExpenseLimit = $_POST['addExpenseLimit'];
            }

            $db = static::getDB();

            $sql = "INSERT INTO expenses_category_assigned_to_users
            VALUES (NULL, :userId, :expenseCategoryName, :expense_limit)";
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':userId', $userID, PDO::PARAM_INT);
            $stmt->bindValue(':expenseCategoryName', $inputCategoryOfExpense, PDO::PARAM_STR);
            $stmt->bindValue(':expense_limit', $inputExpenseLimit, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    public function addPaymentCategory()
    {
        $this->validateAddPaymentCategory();

        if (empty($this->info['addPayment'])) {
            $this->status['addPayment'] = true;

            $userID = $_SESSION['user_id'];
            $inputCategoryOfPayment = $_POST['addPaymentCategory'];

            $db = static::getDB();

            $sql = "INSERT INTO payment_methods_assigned_to_users
            VALUES (NULL, :userId, :paymentCategoryName)";
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':userId', $userID, PDO::PARAM_INT);
            $stmt->bindValue(':paymentCategoryName', $inputCategoryOfPayment, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    public function deleteCategory()
    {

        if (isset($_POST['deleteExpenseCategoryId'])) {
            $this->status['delete'] = true;
            $deleteFlag = true;
            $userID = $_SESSION['user_id'];
            $inputCategoryOfexpenseID = $_POST['deleteExpenseCategoryId'];

            $db = static::getDB();

            /**Delete records from expenses */
            $sql = "DELETE FROM expenses
            WHERE expense_category_assigned_to_user_id = :expenseCategoryId
            AND user_id = :userId";
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':userId', $userID, PDO::PARAM_INT);
            $stmt->bindValue(':expenseCategoryId', $inputCategoryOfexpenseID, PDO::PARAM_INT);

            $stmt->execute();

            /**Delete expenses category */
            if ($stmt == true) {
                $sql = "DELETE FROM expenses_category_assigned_to_users
            WHERE id = :expenseCategoryId
            AND user_id = :userId";
                $stmt = $db->prepare($sql);

                $stmt->bindValue(':userId', $userID, PDO::PARAM_INT);
                $stmt->bindValue(':expenseCategoryId', $inputCategoryOfexpenseID, PDO::PARAM_INT);

                $stmt->execute();
                $this->info['deleteStatus'] = true;
                return $this->info['deleteStatus'];
            }
        }

        $this->info['deleteStatus'] = false;
        return  $this->info['deleteStatus'];
    }

    public function deletePayment()
    {

        if (isset($_POST['deletePaymentCategoryId'])) {
            $this->status['deletePayment'] = true;
            $deleteFlag = true;
            $userID = $_SESSION['user_id'];
            $inputCategoryOfPaymentID = $_POST['deletePaymentCategoryId'];

            $db = static::getDB();

            /**Delete records from expenses */
            $sql = "DELETE FROM expenses
            WHERE payment_method_assigned_to_user_id = :paymentCategoryId
            AND user_id = :userId";
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':userId', $userID, PDO::PARAM_INT);
            $stmt->bindValue(':paymentCategoryId', $inputCategoryOfPaymentID, PDO::PARAM_INT);

            $stmt->execute();

            /**Delete expenses category */
            if ($stmt == true) {
                $sql = "DELETE FROM payment_methods_assigned_to_users
            WHERE id = :paymentCategoryId
            AND user_id = :userId";
                $stmt = $db->prepare($sql);

                $stmt->bindValue(':userId', $userID, PDO::PARAM_INT);
                $stmt->bindValue(':paymentCategoryId', $inputCategoryOfPaymentID, PDO::PARAM_INT);

                $stmt->execute();
                $this->info['deleteStatus'] = true;
                return $this->info['deleteStatus'];
            }
        }

        $this->info['deleteStatus'] = false;
        return  $this->info['deleteStatus'];
    }

    public static function deleteExpenseRecordsAssignedToUser()
    {
        $userID = $_SESSION['user_id'];

        $db = static::getDB();

        /**Delete all user records from incomes */
        $sql = "DELETE FROM expenses
            WHERE user_id = :userId";
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':userId', $userID, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function edit()
    {
        if (isset($this->info['name'])) unset($this->info['name']);

        $this->validateEditCategory();

        if ((empty($this->info['name'])) && !isset($_POST['addExpenseLimit'])) {
            $this->status['edit'] = true;

            $userID = $_SESSION['user_id'];
            $inputCategoryOfExpenseID = $_POST['editExpenseCategoryId'];
            $inputCategoryOfExpense = $_POST['editExpenseCategoryName'];

            $db = static::getDB();

            $sql = "UPDATE expenses_category_assigned_to_users
            SET name = :expenseCategoryName
            WHERE id = :expenseCategoryId
            AND user_id = :userId";
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':userId', $userID, PDO::PARAM_INT);
            $stmt->bindValue(':expenseCategoryId', $inputCategoryOfExpenseID, PDO::PARAM_INT);
            $stmt->bindValue(':expenseCategoryName', $inputCategoryOfExpense, PDO::PARAM_STR);

            return $stmt->execute();
        } else if ((empty($this->info['name'])) && (isset($_POST['addExpenseLimit']))) {
            $this->status['edit'] = true;

            $userID = $_SESSION['user_id'];
            $inputCategoryOfExpenseID = $_POST['editExpenseCategoryId'];
            $inputCategoryOfExpense = $_POST['editExpenseCategoryName'];
            $limit = $_POST['addExpenseLimit'];

            $db = static::getDB();

            $sql = "UPDATE expenses_category_assigned_to_users
            SET name = :expenseCategoryName,
            expense_limit = :expenseLimit
            WHERE id = :expenseCategoryId
            AND user_id = :userId";
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':userId', $userID, PDO::PARAM_INT);
            $stmt->bindValue(':expenseCategoryId', $inputCategoryOfExpenseID, PDO::PARAM_INT);
            $stmt->bindValue(':expenseCategoryName', $inputCategoryOfExpense, PDO::PARAM_STR);
            $stmt->bindValue(':expenseLimit', $limit, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    public function editPayment()
    {
        $this->validateEditPaymentCategory();

        if (empty($this->info['editPaymentName'])) {
            $this->status['editPayment'] = true;

            $userID = $_SESSION['user_id'];
            $inputCategoryOfPaymentID = $_POST['editPaymentCategoryId'];
            $inputCategoryOfPayment = $_POST['editPaymentCategory'];

            $db = static::getDB();

            $sql = "UPDATE payment_methods_assigned_to_users
            SET name = :paymentCategoryName
            WHERE id = :paymentCategoryId
            AND user_id = :userId";
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':userId', $userID, PDO::PARAM_INT);
            $stmt->bindValue(':paymentCategoryId', $inputCategoryOfPaymentID, PDO::PARAM_INT);
            $stmt->bindValue(':paymentCategoryName', $inputCategoryOfPayment, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    public static function getSingleCommentInfo()
    {
        if(isset($_SESSION['singleExpenseComment'])) {
            $message['comment'] = $_SESSION['singleExpenseComment'];
            $message['id'] = $_SESSION['singleExpenseId'];
            unset($_SESSION['singleExpenseComment']);
            unset($_SESSION['singleExpenseId']);
            return $message;
        }

        else return null;
    }

    public function editSingleRecordComment()
    {
        unset($_SESSION['singleExpenseComment']);

        $_SESSION['editExpenseStatus'] = true;

        if (isset($_POST['editSingleExpenseCommentInput']) && isset($_POST['singleExpenseId'])) {
            if ((strlen($_POST['editSingleExpenseCommentInput'])) > 50) {
                $_SESSION['singleExpenseComment'] = "Treść komentarza nie może przekroczyć 50 znaków!";
                $_SESSION['singleExpenseId'] = $_POST['singleExpenseId'];
                $_SESSION['editExpenseStatus'] = false;
            }
        }

        if(!isset($_SESSION['singleExpenseComment']) && isset($_POST['singleExpenseId'])) {

        $userID = $_SESSION['user_id'];
        $singleExpenseID = $_POST['singleExpenseId'];
        $singleExpenseComment = $_POST['editSingleExpenseCommentInput'];

        $db = static::getDB();

        $sql = "UPDATE expenses
            SET expense_comment = :expenseComment
            WHERE id = :expenseId
            AND user_id = :userId";
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':userId', $userID, PDO::PARAM_INT);
        $stmt->bindValue(':expenseId', $singleExpenseID, PDO::PARAM_INT);
        $stmt->bindValue(':expenseComment', $singleExpenseComment, PDO::PARAM_STR);

        return $stmt->execute();
        }
    }

    public function deleteSingleRecord()
    {

        $_SESSION['deleteExpenseStatus'] = true;

        if(isset($_POST['singleExpenseRecordId'])) {

        $userID = $_SESSION['user_id'];
        $singleExpenseRecordID = $_POST['singleExpenseRecordId'];

        $db = static::getDB();

        $sql = "DELETE FROM expenses
            WHERE id = :expenseId
            AND user_id = :userId";
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':userId', $userID, PDO::PARAM_INT);
        $stmt->bindValue(':expenseId', $singleExpenseRecordID, PDO::PARAM_INT);

        return $stmt->execute();
        }
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

        //Payment method
        if (!isset($this->paymentMethod)) {
            $this->errors['paymentMethod'] = 'Należy wybrać metodę płatności!';
        }

        // Category of expense
        if (!isset($this->categoryOfExpense)) {
            $this->errors['categoryOfExpense'] = 'Należy wybrać kategorię przychodu!';
        }

        // comment
        if ((strlen($this->comment)) > 50) {
            $this->errors['comment'] = 'Treść komentarza nie może przekroczyć 50 znaków!';
        }
    }

    public function validateEditCategory()
    {

        if (isset($_POST['editExpenseCategoryName'])) {
            $this->info['inputName'] = $_POST['editExpenseCategoryName'];
            $inputCategoryOfExpense = $_POST['editExpenseCategoryName'];
            $inputCategoryOfExpenseID = $_POST['editExpenseCategoryId'];
            $inputExpensePosition = $_POST['editExpensePosition'];
            $counter = 0;
            $index = 0;
            $occurance = 0;

            $inputCategoryOfExpense = mb_strtolower($inputCategoryOfExpense, 'UTF-8');
            $existingExpenseCategories = static::getExpenseCategoriesAssignedToUser();
            $limit = static::getExpenseLimitAssignedToUser($inputCategoryOfExpenseID);
            $limit = number_format($limit['category_limit']);
            if (isset($_POST['addExpenseLimit'])) $inputLimit = $_POST['addExpenseLimit'];
            $inputLimit = number_format($inputLimit);


            foreach ($existingExpenseCategories as $expensesData) {
                foreach ($expensesData as $name['name']) {

                    $name['name'] = mb_strtolower($name['name'], 'UTF-8');

                    if ($name['name'] == $inputCategoryOfExpense) {
                        $this->info['name'] = "Kategoria o tej nazwie już istnieje!";
                    }
                }
            }

            $existingExpenseCategoriesPL = $existingExpenseCategories;
            $existingExpenseCategories = static::changeFromPolishToEnglish($existingExpenseCategories);

            foreach ($existingExpenseCategories as $expensesData) {
                foreach ($expensesData as $name['name']) {

                    $name['name'] = mb_strtolower($name['name'], 'UTF-8');

                    if ($name['name'] == $inputCategoryOfExpense) {
                        $this->info['name'] = "Kategoria o tej nazwie już istnieje!";
                    }
                }
            }

            if ((strlen($inputCategoryOfExpense) > 35) || (strlen($inputCategoryOfExpense) < 2)) {
                $this->info['name'] = "Nazwa kategorii powinna zawierać więcej niż 1 znak i mniej niż 35 znaków";
            }

            if (isset($_POST['addExpenseLimit'])) {
                if ($_POST['addExpenseLimit'] < 0) $this->info['addLimit'] = "Limit musi być większy od 0";
            }

            foreach ($existingExpenseCategoriesPL as $expensesData) {
                $counter++;
                foreach ($expensesData as $name['name']) {

                    $name['name'] = mb_strtolower($name['name'], 'UTF-8');

                    if (($name['name'] == $inputCategoryOfExpense)) $occurance++;
                    if (($name['name'] == $inputCategoryOfExpense) && ($inputLimit !== $limit)) {
                        $index = $counter;
                    }
                }
            }

            if (($index == $inputExpensePosition) && ($occurance == 1)) {
                $this->info['name'] = [];
            }

            if (($occurance == 1) && ($index != $inputExpensePosition)) {
                $this->info['name'] = "Kategoria o tej nazwie już istnieje!";
                $this->info['id'] = $inputExpensePosition;
            }
        }
    }

    public function validateEditPaymentCategory()
    {

        if (isset($_POST['editPaymentCategory'])) {

            $this->info['inputEditPaymentName'] = $_POST['editPaymentCategory'];
            $inputCategoryOfPayment = $_POST['editPaymentCategory'];
            //$inputCategoryOfPaymentID = $_POST['editPaymentCategoryId'];
            $inputPaymentPosition = $_POST['editPaymentPosition'];
            $counter = 0;
            $index = 0;
            $occurance = 0;




            $this->info['inputEditPaymentName'] = $_POST['editPaymentCategory'];
            $inputCategoryOfPayment = $_POST['editPaymentCategory'];

            $inputCategoryOfPayment = mb_strtolower($inputCategoryOfPayment, 'UTF-8');
            $existingPaymentCategories = static::getPaymentMethodsAssignedToUser();

            foreach ($existingPaymentCategories as $paymentsData) {
                foreach ($paymentsData as $name['payment_method']) {

                    $name['payment_method'] = mb_strtolower($name['payment_method'], 'UTF-8');

                    if ($name['payment_method'] == $inputCategoryOfPayment) {
                        $this->info['editPaymentName'] = "Kategoria o tej nazwie już istnieje!";
                        $this->info['paymentId'] = $inputPaymentPosition;
                    }
                }
            }

            $existingPaymentCategoriesPL = $existingPaymentCategories;
            $existingPaymentCategories = static::changeFromPolishToEnglish($existingPaymentCategories);

            foreach ($existingPaymentCategories as $paymentData) {
                foreach ($paymentData as $name['payment_method']) {

                    $name['payment_method'] = mb_strtolower($name['payment_method'], 'UTF-8');

                    if ($name['payment_method'] == $inputCategoryOfPayment) {
                        $this->info['editPaymentName'] = "Kategoria o tej nazwie już istnieje!";
                        $this->info['paymentId'] = $inputPaymentPosition;
                    }
                }
            }

            if ((strlen($inputCategoryOfPayment) > 35) || (strlen($inputCategoryOfPayment) < 2)) {
                $this->info['editPaymentName'] = "Nazwa kategorii powinna zawierać więcej niż 1 znak i mniej niż 35 znaków";
                $this->info['paymentId'] = $inputPaymentPosition;
            }

            foreach ($existingPaymentCategoriesPL as $paymentData) {
                $counter++;
                foreach ($paymentData as $name['payment_method']) {

                    $name['payment_method'] = mb_strtolower($name['payment_method'], 'UTF-8');

                    if (($name['payment_method'] == $inputCategoryOfPayment)) $occurance++;
                    if (($name['payment_method'] == $inputCategoryOfPayment)) {
                        $index = $counter;
                    }
                }
            }

            if (($occurance == 1) && ($index != $inputPaymentPosition)) {
                $this->info['editPaymentName'] = "Kategoria o tej nazwie już istnieje!";
                $this->info['paymentId'] = $inputPaymentPosition;
            }
        }
    }

    public function validateAddCategory()
    {

        if (isset($_POST['addExpenseCategory'])) {
            $this->info['inputName'] = $_POST['addExpenseCategory'];
            $inputCategoryOfExpense = $_POST['addExpenseCategory'];

            $inputCategoryOfExpense = mb_strtolower($inputCategoryOfExpense, 'UTF-8');
            $existingExpenseCategories = static::getExpenseCategoriesAssignedToUser();

            foreach ($existingExpenseCategories as $expensesData) {
                foreach ($expensesData as $name['name']) {

                    $name['name'] = mb_strtolower($name['name'], 'UTF-8');

                    if ($name['name'] == $inputCategoryOfExpense) {
                        $this->info['addName'] = "Kategoria o tej nazwie już istnieje!";
                    }
                }
            }

            $existingExpenseCategories = static::changeFromPolishToEnglish($existingExpenseCategories);

            foreach ($existingExpenseCategories as $expensesData) {
                foreach ($expensesData as $name['name']) {

                    $name['name'] = mb_strtolower($name['name'], 'UTF-8');

                    if ($name['name'] == $inputCategoryOfExpense) {
                        $this->info['addName'] = "Kategoria o tej nazwie już istnieje!";
                    }
                }
            }

            if ((strlen($inputCategoryOfExpense) > 35) || (strlen($inputCategoryOfExpense) < 2)) {
                $this->info['addName'] = "Nazwa kategorii powinna zawierać więcej niż 1 znak i mniej niż 35 znaków";
            }

            if (isset($_POST['addExpenseLimit'])) {
                if ($_POST['addExpenseLimit'] < 0) $this->info['addLimit'] = "Limit musi być większy od 0";
            }
        }
    }

    public function validateAddPaymentCategory()
    {
        if (isset($_POST['addPaymentCategory'])) {
            $this->info['inputName'] = $_POST['addPaymentCategory'];
            $inputCategoryOfPayment = $_POST['addPaymentCategory'];

            $inputCategoryOfPayment = mb_strtolower($inputCategoryOfPayment, 'UTF-8');
            $existingPaymentCategories = static::getPaymentMethodsAssignedToUser();

            foreach ($existingPaymentCategories as $paymentsData) {
                foreach ($paymentsData as $name['payment_method']) {

                    $name['payment_method'] = mb_strtolower($name['payment_method'], 'UTF-8');

                    if ($name['payment_method'] == $inputCategoryOfPayment) {
                        $this->info['addPayment'] = "Kategoria o tej nazwie już istnieje!";
                    }
                }
            }

            $existingPaymentCategories = static::changeFromPolishToEnglish($existingPaymentCategories);

            foreach ($existingPaymentCategories as $paymentsData) {
                foreach ($paymentsData as $name['payment_method']) {

                    $name['payment_method'] = mb_strtolower($name['payment_method'], 'UTF-8');

                    if ($name['payment_method'] == $inputCategoryOfPayment) {
                        $this->info['addPayment'] = "Kategoria o tej nazwie już istnieje!";
                    }
                }
            }

            if ((strlen($inputCategoryOfPayment) > 35) || (strlen($inputCategoryOfPayment) < 2)) {
                $this->info['addPayment'] = "Nazwa kategorii powinna zawierać więcej niż 1 znak i mniej niż 35 znaków";
            }
        }
    }

    public function currentMonthExpenses($user_ID)
    {

        $sql = "SELECT expenses_category_assigned_to_users.name, payment_methods_assigned_to_users.name AS payment_method, expenses.id, expenses.amount, expenses.date_of_expense, expenses.expense_comment
        FROM expenses, expenses_category_assigned_to_users, payment_methods_assigned_to_users
        WHERE expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id
        AND expenses.payment_method_assigned_to_user_id = payment_methods_assigned_to_users.id
        AND expenses.user_id = expenses_category_assigned_to_users.user_id
        AND expenses.user_id = payment_methods_assigned_to_users.user_id
        AND expenses.user_id = '$user_ID'
        AND MONTH(date_of_expense) = MONTH(CURRENT_DATE)
        AND YEAR(date_of_expense) = YEAR(CURRENT_DATE)
        ORDER BY DATE(date_of_expense) DESC";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return static::changeFromEnglishToPolish($expenses);
    }

    public function lastMonthExpenses($user_ID)
    {

        $sql = "SELECT expenses_category_assigned_to_users.name, payment_methods_assigned_to_users.name AS payment_method, expenses.id, expenses.amount, expenses.date_of_expense, expenses.expense_comment
            FROM expenses, expenses_category_assigned_to_users, payment_methods_assigned_to_users
            WHERE expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id
            AND expenses.payment_method_assigned_to_user_id = payment_methods_assigned_to_users.id
            AND expenses.user_id = expenses_category_assigned_to_users.user_id
            AND expenses.user_id = payment_methods_assigned_to_users.user_id
            AND expenses.user_id = '$user_ID'
            AND MONTH(date_of_expense) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
            AND YEAR(date_of_expense) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
            ORDER BY DATE(date_of_expense) DESC";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return static::changeFromEnglishToPolish($expenses);
    }

    public function customExpenses($user_ID, $userInputDateStart, $userInputDateEnd)
    {

        $sql = "SELECT expenses_category_assigned_to_users.name, payment_methods_assigned_to_users.name AS payment_method, expenses.id, expenses.amount, expenses.date_of_expense, expenses.expense_comment
            FROM expenses, expenses_category_assigned_to_users, payment_methods_assigned_to_users
            WHERE expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id
            AND expenses.payment_method_assigned_to_user_id = payment_methods_assigned_to_users.id
            AND expenses.user_id = expenses_category_assigned_to_users.user_id
            AND expenses.user_id = payment_methods_assigned_to_users.user_id
            AND expenses.user_id = '$user_ID'
            AND date_of_expense BETWEEN '$userInputDateStart' AND '$userInputDateEnd'
            ORDER BY DATE(date_of_expense) DESC";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return static::changeFromEnglishToPolish($expenses);
    }

    public function currentMonthExpensesByCategory($user_ID)
    {
        $sql = "SELECT SUM(expenses.amount) AS amount, expenses_category_assigned_to_users.name
        FROM expenses, expenses_category_assigned_to_users
        WHERE expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id
        AND expenses.user_id = expenses_category_assigned_to_users.user_id
        AND expenses.user_id = '$user_ID'
        AND MONTH(date_of_expense) = MONTH(CURRENT_DATE)
        AND YEAR(date_of_expense) = YEAR(CURRENT_DATE)
        GROUP BY expenses_category_assigned_to_users.name
        ORDER BY amount DESC";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return static::changeFromEnglishToPolish($expenses);
    }

    public function lastMonthExpensesByCategory($user_ID)
    {
        $sql = "SELECT SUM(expenses.amount) AS amount, expenses_category_assigned_to_users.name
        FROM expenses, expenses_category_assigned_to_users
        WHERE expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id
        AND expenses.user_id = expenses_category_assigned_to_users.user_id
        AND expenses.user_id = '$user_ID'
        AND MONTH(date_of_expense) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
        AND YEAR(date_of_expense) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
        GROUP BY expenses_category_assigned_to_users.name
        ORDER BY amount DESC";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return static::changeFromEnglishToPolish($expenses);
    }

    public function customExpensesByCategory($user_ID, $userInputDateStart, $userInputDateEnd)
    {

        $sql = "SELECT SUM(expenses.amount) AS amount, expenses_category_assigned_to_users.name
        FROM expenses, expenses_category_assigned_to_users
        WHERE expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id
        AND expenses.user_id = expenses_category_assigned_to_users.user_id
        AND expenses.user_id = '$user_ID'
        AND date_of_expense BETWEEN '$userInputDateStart' AND '$userInputDateEnd'
        GROUP BY expenses_category_assigned_to_users.name
        ORDER BY amount DESC";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return static::changeFromEnglishToPolish($expenses);
    }

    public static function changeFromEnglishToPolish($expensesDetailedData)
    {

        if (!$expensesDetailedData) return false;

        foreach ($expensesDetailedData as &$expensesDetailed) {
            foreach ($expensesDetailed as &$name['name']) {

                switch ($name['name']) {
                    case "Books":
                        $name['name'] = "Książki";
                        break;
                    case "Food":
                        $name['name'] = "Jedzenie";
                        break;
                    case "Apartments":
                        $name['name'] = "Mieszkanie";
                        break;
                    case "Telecommunication":
                        $name['name'] = "Telekomunikacja";
                        break;
                    case "Health":
                        $name['name'] = "Opieka zdrowotna";
                        break;
                    case "Clothes":
                        $name['name'] = "Ubranie";
                        break;
                    case "Hygiene":
                        $name['name'] = "Higiena";
                        break;
                    case "Kids":
                        $name['name'] = "Dzieci";
                        break;
                    case "Recreation":
                        $name['name'] = "Rozrywka";
                        break;
                    case "Trip":
                        $name['name'] = "Wycieczka";
                        break;
                    case "Savings":
                        $name['name'] = "Oszczędności";
                        break;
                    case "For Retirement":
                        $name['name'] = "Na złotą jesień, czyli emeryturę";
                        break;
                    case "Debt Repayment":
                        $name['name'] = "Spłata długów";
                        break;
                    case "Gift":
                        $name['name'] = "Darowizna";
                        break;
                    case "Another":
                        $name['name'] = "Inne";
                        break;
                }
            }
        }


        foreach ($expensesDetailedData as &$expensesDetailed) {
            foreach ($expensesDetailed as &$name['payment_method']) {

                switch ($name['payment_method']) {
                    case "Cash":
                        $name['payment_method'] = "Gotówka";
                        break;
                    case "Debit Card":
                        $name['payment_method'] = "Karta Debetowa";
                        break;
                    case "Credit Card":
                        $name['payment_method'] = "Karta Kredytowa";
                        break;
                }
            }
        }

        return $expensesDetailedData;
    }

    public static function changeFromPolishToEnglish($expensesDetailedData)
    {
        if (!$expensesDetailedData) return false;


        foreach ($expensesDetailedData as &$expensesDetailed) {
            foreach ($expensesDetailed as &$name['name']) {

                switch ($name['name']) {
                    case "Książki":
                        $name['name'] = "Books";
                        break;
                    case "Jedzenie":
                        $name['name'] = "Food";
                        break;
                    case "Mieszkanie":
                        $name['name'] = "Apartments";
                        break;
                    case "Telekomunikacja":
                        $name['name'] = "Telecommunication";
                        break;
                    case "Opieka zdrowotna":
                        $name['name'] = "Health";
                        break;
                    case "Ubranie":
                        $name['name'] = "Clothes";
                        break;
                    case "Higiena":
                        $name['name'] = "Hygiene";
                        break;
                    case "Dzieci":
                        $name['name'] = "Kids";
                        break;
                    case "Rozrywka":
                        $name['name'] = "Recreation";
                        break;
                    case "Wycieczka":
                        $name['name'] = "Trip";
                        break;
                    case "Oszczędności":
                        $name['name'] = "Savings";
                        break;
                    case "Na złotą jesień, czyli emeryturę":
                        $name['name'] = "For Retirement";
                        break;
                    case "Spłata długów":
                        $name['name'] = "Debt Repayment";
                        break;
                    case "Darowizna":
                        $name['name'] = "Gift";
                        break;
                    case "Inne":
                        $name['name'] = "Another";
                        break;
                }
            }
        }


        foreach ($expensesDetailedData as &$expensesDetailed) {
            foreach ($expensesDetailed as &$name['payment_method']) {

                switch ($name['payment_method']) {
                    case "Gotówka":
                        $name['payment_method'] = "Cash";
                        break;
                    case "Karta Debetowa":
                        $name['payment_method'] = "Debit Card";
                        break;
                    case "Karta Kredytowa":
                        $name['payment_method'] = "Credit Card";
                        break;
                }
            }
        }
        return $expensesDetailedData;
    }

    public static function getExpense($user_ID, $choice = "", $dateStart = "", $dateEnd = "")
    {
        $expense = new Expenses;

        if (($choice == 'currentMonth') || ($choice == "")) {
            return $expense->currentMonthExpenses($user_ID);
        } else if ($choice == 'lastMonth') {
            return $expense->lastMonthExpenses($user_ID);
        } else if ($choice == 'custom') {
            return $expense->customExpenses($user_ID, $dateStart, $dateEnd);
        }
    }

    public static function getExpenseByCategory($user_ID, $choice = "", $dateStart = "", $dateEnd = "")
    {
        $expenseByCategory = new Expenses;

        if (($choice == 'currentMonth') || ($choice == "")) {
            return $expenseByCategory->currentMonthExpensesByCategory($user_ID);
        } else if ($choice == 'lastMonth') {
            return $expenseByCategory->lastMonthExpensesByCategory($user_ID);
        } else if ($choice == 'custom') {
            return $expenseByCategory->customExpensesByCategory($user_ID, $dateStart, $dateEnd);
        }
    }

    public static function getExpenseByCategoryPieChartData()
    {
        if (isset($_SESSION['expenseByCategory'])) {

            if (($_SESSION['expenseByCategory'])) {
                $expenseByCategory = $_SESSION['expenseByCategory'];
                $dataPoints = [];
                $expenseSum = 0;

                foreach ($expenseByCategory as $expense) {

                    $expenseSum += $expense['amount'];
                }

                foreach ($expenseByCategory as $data) {

                    $expensePercentage = $data['amount'] / $expenseSum * 100;
                    $newArray = array("label" => $data['name'], "y" => $expensePercentage);
                    array_push($dataPoints, $newArray);
                }
                return $dataPoints;
            }
        }
    }

    public static function getExpenseCategoriesAssignedToUser()
    {
        $user = Auth::getUser();

        if ($user) {

            $user_ID = $user->id;

            $sql = "SELECT name, id, expense_limit as expenseLimit
        FROM expenses_category_assigned_to_users
        WHERE expenses_category_assigned_to_users.user_id = '$user_ID'";

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->execute();

            $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return static::changeFromEnglishToPolish($expenses);
        }
    }

    public static function getPaymentMethodsAssignedToUser()
    {
        $user = Auth::getUser();

        if ($user) {

            $user_ID = $user->id;

            $sql = "SELECT name AS payment_method, id
        FROM payment_methods_assigned_to_users
        WHERE payment_methods_assigned_to_users.user_id = '$user_ID'";

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->execute();

            $paymentMethods = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($paymentMethods as &$paymentsDetailed) {
                foreach ($paymentsDetailed as &$name['payment_method']) {

                    switch ($name['payment_method']) {
                        case "Cash":
                            $name['payment_method'] = "Gotówka";
                            break;
                        case "Debit Card":
                            $name['payment_method'] = "Karta Debetowa";
                            break;
                        case "Credit Card":
                            $name['payment_method'] = "Karta Kredytowa";
                            break;
                    }
                }
            }

            return $paymentMethods;
        }
    }

    public static function getExpenseSumAssignedToUser($id, $date)
    {

        $sql = "SELECT SUM(expenses.amount) as category_expenses
        FROM expenses, expenses_category_assigned_to_users
        WHERE expenses.expense_category_assigned_to_user_id = '$id'
        AND expenses.expense_category_assigned_to_user_id = expenses_category_assigned_to_users.id
        AND MONTH(date_of_expense) = MONTH('$date')
        AND YEAR(date_of_expense) = YEAR('$date')";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $expensesSumByCategory = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $expensesSumByCategory[0];
    }

    public static function getExpenseLimitAssignedToUser($id)
    {

        $sql = "SELECT expenses_category_assigned_to_users.expense_limit as category_limit
        FROM expenses_category_assigned_to_users
        WHERE id = '$id'";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $expensesLimitByCategory = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $expensesLimitByCategory[0];
    }
}
