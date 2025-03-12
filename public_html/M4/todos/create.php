<?php
//jjc88 03/12/2025 
require_once(__DIR__ . "/../../../lib/db.php"); ?>

<?php
// don't edit - this
$expected_fields = ["task", "due", "assigned"];
$diff = array_diff($expected_fields, array_keys($_GET));

if (empty($diff)) {

    // data variables, don't edit
    $task = $_GET["task"];
    $due = $_GET["due"]; //hint: must be a valid MySQL date format
    $assigned = $_GET["assigned"]; // Must be "self" or a valid format (not empty or equivalent)

    $is_valid = true;
    // TODO Validate the incoming data for correct format based on the SQL table definition.
    // When not valid, provide a user-friendly message of what specifically was wrong and set $is_valid to false.
    // Assigned should check for "self" if a valid format/value isn't provided.
    // Start validations
    // jjc88 03/12/2025 Using if statements to catch invalid input from users. Also setting default inputs if certain conditions are met. Also outputting what is wrong with input
    //checks if task input is empty or exceeds 128 char, if it does it will carry out the statement
    if (empty($task) or strlen($task) > 128)
    {
        $is_valid = false;
        $error = "Task is empty or exceeds 128 characters.";
    }
    //checks if date is empty, if true it will carry out statement
    if (empty($due))
    {
        $is_valid = false;
        $error = "Date is empty.";
    }
    //if assigned it empty or exceeds 60 char, it will carry out statment
    if (empty($assigned) or strlen($assigned) > 60)
    {
        $is_valid = false;
        $error = "Assigned is empty of exceeds 60 characters.";
        $assigned = "self";
    }
    // if the $is_valid value is set to false it means there is an error and it will echo the related fault
    if (!$is_valid)
    {
        
        echo $error;
    }
    // End validations

    
    if ($is_valid) {
        /*
        Design a query to insert the incoming data to the proper columns.
        Ensure valid and proper PDO named placeholders are used.
        https://phpdelusions.net/pdo
        */
        //jjc88 03/12/2025 making a query to sort data in proper places
        //Inserts data to the M4_Todo sql table with the appopriate name and values
        $query = "INSERT INTO M4_Todos (task, due, assigned) VALUES (:task, :due, :assigned)"; // edit this
        //links the values to the associated variable
        $params = [":task"=>$task, ":due"=>$due, ":assigned"=>$assigned]; // Apply the proper PDO placeholder to variable mapping here
        try {
            $db = getDB();
            $stmt = $db->prepare($query);
            $r = $stmt->execute($params);
            if ($r) {
                echo "Inserted new Todo with id " . $db->lastInsertId();
            } else {
                echo "Failed to insert";
            }
        } catch (PDOException $e) {
            // extra credit
            // check if the exception was related to a unique constraint
            // provide an appropriate user-friendly message for this scenario
            // Otherwise show the default message below
            echo "There was an error inserting the record; check the logs (terminal)";
            error_log("Insert Error: " . var_export($e, true)); // shows in the terminal
        }
    } else {
        error_log("Creation input wasn't valid");
    }
}
?>
<html>

<body>
    <?php require_once(__DIR__ . "/../nav.php"); ?>
    <section>
        <h2>Create ToDo </h2>
        <form>
            <!-- design the form with proper labels and input fields with the correct types based on the SQL table.
             Wrap each label/input pair in a div tag.
             For "Assigned" ensure the default value is "self". -->
<!--jjc88 03/12/2025 Using appropriate div, label, and input to space out and have input fields that associate with that data -->
             
             <div>
                <label for ="task">Task:</label>
                <input type="text" id="task" name="task"/>
             </div>

            <div>
                <label for ="due">Due:</label>
                <input type="date" id="due" name="due"/>
            </div>

            <div>
                <label for="assigned">Assigned:</label>
                <input type="text" id="assigned" name="assigned" value="self"/>
            </div>

          
            <div>
                <input type="submit" />
            </div>
        </form>
    </section>
</body>
</body>

</html>