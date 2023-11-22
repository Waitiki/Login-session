<?php
$host = "localhost";
$user = "root";
$password = "";
$db = "dairy";

// Create a database connection
$conn = new mysqli($host, $user, $password, $db);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch farmer ID from the redirection link
$loggedInFarmerId = isset($_GET['farmer_id']) ? $_GET['farmer_id'] : null;

// Verify that the farmer ID is not empty and is a valid integer (you may need additional validation)
if (empty($loggedInFarmerId) || !is_numeric($loggedInFarmerId)) {
    die("Invalid farmer ID.");
}



// Fetch all records for the logged-in farmer
$allRecordsQuery = "SELECT farmer_Id, farmer_name, quantity, date_time FROM records WHERE farmer_Id = '$loggedInFarmerId'";
$allRecordsResult = $conn->query($allRecordsQuery);

// Check if the query was successful
if ($allRecordsResult) {
    $allRecords = $allRecordsResult->fetch_all(MYSQLI_ASSOC);
} else {
    echo "Error in fetching records: " . $conn->error;
    $allRecords = array();
}

// Fetch farmer names and IDs from the database
$query = "SELECT id, name FROM farmers";
$result = $conn->query($query);

// Check if the query was successful
if ($result) {
    $farmerData = $result->fetch_all(MYSQLI_ASSOC);
} else {
    echo "Error in fetching farmer data: " . $conn->error;
    $farmerData = array();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $farmerId = $_POST['farmerId'];
    $farmerName = $_POST['farmerName'];
    $quantity = $_POST['quantity'];
    $dateTime = $_POST['dateTime'];

    // Prepare and execute the SQL query to insert data
    $query = "INSERT INTO records (farmer_Id, farmer_name, quantity, date_time) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Error: " . $conn->error);
    }

    $stmt->bind_param("ssss", $farmerId, $farmerName, $quantity, $dateTime);

    if ($stmt->execute()) {
        echo "Record added successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Build the SQL query for fetching records with filtering
$filterQuery = "SELECT farmer_Id, farmer_name, quantity, date_time FROM records WHERE 1";

if (isset($_POST['filterName']) && !empty($_POST['filterName'])) {
    $filterName = $_POST['filterName'];
    $filterQuery .= " AND farmer_name LIKE '%$filterName%'";
}

if (isset($_POST['filterDate']) && !empty($_POST['filterDate'])) {
    $filterDate = $_POST['filterDate'];
    $filterQuery .= " AND DATE(date_time) = '$filterDate'";
}

$result = $conn->query($filterQuery);

// Check if the query was successful
if ($result) {
    $numRows = $result->num_rows;
} else {
    echo "Error in fetching records: " . $conn->error;
    $numRows = 0;
}

// Initialize total income variable
$totalIncome = 0;

// Check if there are rows before entering the loop
if ($numRows > 0) {
    // Calculate total income for filtered records
    while ($row = $result->fetch_assoc()) {
        $quantity = $row['quantity'];
        $income = $quantity * 50; // Calculate income based on quantity
        $totalIncome += $income; // Accumulate income for each row
    }
}

// Display specific total income for an individual farmer
if (isset($_POST['specificFarmerId']) && !empty($_POST['specificFarmerId'])) {
    $specificFarmerId = $_POST['specificFarmerId'];
    $specificTotalIncomeQuery = "SELECT SUM(quantity * 50) as totalIncome FROM records WHERE farmer_Id = '$specificFarmerId'";
    $specificTotalIncomeResult = $conn->query($specificTotalIncomeQuery);

    if ($specificTotalIncomeResult) {
        $specificTotalIncomeRow = $specificTotalIncomeResult->fetch_assoc();
        $specificTotalIncome = $specificTotalIncomeRow['totalIncome'];
        echo "Total Income for Farmer ID $specificFarmerId: $specificTotalIncome";
    } else {
        echo "Error in fetching specific total income: " . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('background.jpg'); /* Replace with your background image */
            background-size: cover;
            background-position: center;
            margin: 0;
            padding: 0;
            color: #333;
        }

        h2 {
            text-align: center;
            color: #4CAF50;
        }

        .table-container {
            margin: 20px auto;
			margin-left:200px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            background: rgba(255, 255, 255, 0.9);
            border-radius: 5px;
            overflow: hidden;
            width: 80%; /* Adjust the width as needed */
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #4CAF50;
            color: white;
        }

        .add-record{
			margin-left:300px;
			margin-top:100px;
		    font-size:40px;
			
		}
        .update-record,
        .delete-record {
            font-size: 20px;
            cursor: pointer;
            margin: 5px;
            color: #4CAF50;
        }

        .add-record:hover,
        .update-record:hover,
        .delete-record:hover {
            color: #45a049;
        }

        .search-bar,
        .filter-bar {
            margin: 20px;
            float: right;
        }

        .search-input,
        .filter-input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .add-record-form,
        .update-record-form,
        .delete-record-form {
            display: none;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 2;
        }

        .blurred-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            z-index: 1;
        }

        .close-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            cursor: pointer;
        }

        .symbol {
            font-size: 18px;
            margin-right: 5px;
        }

        /* Style for update and delete buttons in the table */
        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .update-record-button,
        .delete-record-button {
            font-size: 14px;
            padding: 8px 12px;
            cursor: pointer;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            outline: none;
        }

        .update-record-button:hover,
        .delete-record-button:hover {
            background-color: #45a049;
        }
		 @media screen and (max-width: 768px) {
    .table-container {
        width: 100%;
        max-width: none;
        margin: 10px; /* Add some margin to improve spacing */
    }

    table {
        font-size: 14px; /* Decrease font size for better readability */
    }

    th, td {
        padding: 8px 10px; /* Reduce padding for better spacing */
    }

    .search-bar,
    .filter-bar {
        margin: 10px; /* Adjust margin for better spacing */
        text-align: center; /* Center the search and filter bars */
    }

    .search-input,
    .filter-input {
        width: 100%; /* Make search and filter inputs full width */
        box-sizing: border-box; /* Include padding and border in the width */
        margin-bottom: 10px; /* Add some bottom margin for spacing */
    }

    .add-record,
    .update-record,
    .delete-record {
        font-size: 16px; /* Increase button font size for better tap targets */
    }

    .add-record-form,
    .update-record-form,
    .delete-record-form {
        padding: 10px; /* Adjust padding for better spacing */
    }
}

    </style>
	<link rel="stylesheet" href="style.css">
</head>

<body>
<input  type="checkbox" id="check">
<label for="check">
<i class="fas fa-bars" id="btn"></i>
<i class="fas fa-times" id="cancel"></i>
</label>
<div class="sidebar">
<header>FARMER DASHBOARD</header>
<ul>
<li><a href="reports.php"><i class="fas fa-question-circle"></i>reports</a></li>
<li><a href="logout.php">Logout</a></li>
</ul>
</div>

    
	<h2>Records</h2><br>

  

    <div class="table-container">
        <div class="filter-bar">
            <input type="text" class="filter-input" id="filterName" placeholder="Filter by ID..." oninput="filterTable()">
            <input type="date" class="filter-input" id="filterDate" oninput="filterTable()">
        </div>

        <table id="recordsTable">
            <tr>
                <th>ID</th>
                <th>Farmer Name</th>
                <th>Quantity</th>
                <th>Date/Time Taken</th>
                <th>Income</th>
                <th>Action</th>
            </tr>
            <!-- Display records fetched from the database -->
            <?php
            if ($numRows > 0) {
                $result->data_seek(0); // Reset result set pointer to the beginning
                while ($row = $result->fetch_assoc()) {
                    $farmerId = $row['farmer_Id'];
                    $farmerName = $row['farmer_name'];
                    $quantity = $row['quantity'];
                    $dateTime = $row['date_time'];
                    $income = $quantity * 50; // Calculate income based on quantity

                    echo "<tr>";
                    echo "<td>" . $farmerId . "</td>";
                    echo "<td>" . $farmerName . "</td>";
                    echo "<td>" . $quantity . " kg</td>";
                    echo "<td>" . $dateTime . "</td>";
                    echo "<td>" . $income . "</td>"; // Display calculated income
                    echo "<td class='action-buttons'>
                            <button onclick=\"openUpdateForm('$farmerId', '$quantity', '$dateTime')\" class='update-record-button'>Update</button>
                            <button onclick=\"deleteRecord('$farmerId')\" class='delete-record-button'>Delete</button>
                          </td>";
                    echo "</tr>";
                }

                // Display total income row
                echo "<tr>";
                echo "<td colspan='4' style='text-align: right;'>Total Income:</td>";
                echo "<td>" . $totalIncome . "</td>";
                echo "<td></td>"; // Empty column for actions in total income row
                echo "</tr>";
            } else {
                echo "<tr><td colspan='6'>No records found</td></tr>";
            }
            ?>
        </table>
    </div>

    <!-- Blurred Background -->
    <div class="blurred-background" id="blurredBackground"></div>

   
    <script>
      
 let originalData = <?php echo json_encode($allRecords); ?>;
        let filteredData = originalData.slice(); // Copy the original data for initial display

        function filterTable() {
            const filterName = document.getElementById('filterName').value.toLowerCase();
            const filterDate = document.getElementById('filterDate').value;

            filteredData = originalData.filter(row => {
                const farmerId = row.farmer_Id.toLowerCase();
                const dateTime = row.date_time.split(' ')[0].toLowerCase();

                const idMatch = farmerId.includes(filterName);
                const dateMatch = filterDate === '' || dateTime === filterDate;

                return idMatch && dateMatch;
            });

            updateTable();
            updateTotalIncome();
        }

        function updateTable() {
            const recordsTable = document.getElementById('recordsTable');
            const totalIncomeElement = document.getElementById('totalIncome');
            let totalIncome = 0;

            // Clear the table
            recordsTable.innerHTML = '';

            // Display filtered records
            if (filteredData.length > 0) {
                let tableHTML = '<tr><th>ID</th><th>Farmer Name</th><th>Quantity</th><th>Date/Time Taken</th><th>Income</th></tr>';
                for (let i = 0; i < filteredData.length; i++) {
                    const row = filteredData[i];
                    const farmerId = row.farmer_Id;
                    const farmerName = row.farmer_name;
                    const quantity = row.quantity;
                    const dateTime = row.date_time;
                    const income = quantity * 50;

                    tableHTML += `<tr><td>${farmerId}</td><td>${farmerName}</td><td>${quantity} kg</td><td>${dateTime}</td><td>${income}</td></tr>`;
                    totalIncome += income;
                }

                // Display total income row
                tableHTML += `<tr><td colspan='4' style='text-align: right;'>Total Income:</td><td>${totalIncome}</td></tr>`;
                recordsTable.innerHTML = tableHTML;
            } else {
                recordsTable.innerHTML = "<tr><td colspan='5'>No records found</td></tr>";
            }

            // Display total income for filtered data
            if (totalIncomeElement) {
                totalIncomeElement.textContent = totalIncome.toFixed(2);
            }
        }

        function updateTotalIncome() {
            let totalIncome = 0;

            // Calculate total income based on the filtered data
            for (let i = 0; i < filteredData.length; i++) {
                const row = filteredData[i];
                const quantity = parseFloat(row.quantity);
                totalIncome += quantity * 50;
            }

            // Display total income for filtered data
            const totalIncomeElement = document.getElementById('totalIncome');
            if (totalIncomeElement) {
                totalIncomeElement.textContent = totalIncome.toFixed(2);
            }
        }

        // Initial display of the table and total income
        filterTable();

</script>
</body>

</html>