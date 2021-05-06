<?php
session_start();
error_reporting(0);
include 'includes/dbconn.php';
$conn = new mysqli($servername, $username, "", "db1", $sqlport, $socket);

if($conn->connect_error)
{
	die("Connection failed: " . $conn->connect_error);
}

$patientID = $_SESSION['PATIENT_ID'];
$query = mysqli_query($conn, "SELECT * FROM ProductBills WHERE PatientID = '$patientID' AND DueDate >= CURDATE() ORDER BY DueDate");
$result = mysqli_fetch_assoc($query);
$payProductButtons = [];

while($result)
{
	$payProductButtons[] = $result['ProductBillID'];
	$result = mysqli_fetch_assoc($query);
}

foreach($payProductButtons as $payProduct)
{
	if(isset($_POST["$payProduct"]))
	{
		$query = mysqli_query($conn, "DELETE FROM ProductBills WHERE ProductBillID = '$payProduct'");
	}
}

$query = mysqli_query($conn, "SELECT * FROM ServiceBills WHERE PatientID = '$patientID' AND DueDate >= CURDATE() ORDER BY DueDate");
$result = mysqli_fetch_assoc($query);
$payServiceButtons = [];

while($result)
{
	$payServiceButtons[] = $result['ServiceBillID'];
	$result = mysqli_fetch_assoc($query);
}

foreach($payServiceButtons as $payService)
{
	if(isset($_POST["$payService"]))
	{
		$query = mysqli_query($conn, "DELETE FROM ServiceBills WHERE ServiceBillID = '$payService'");
	}
}

$query = mysqli_query($conn, "SELECT * FROM TestBills WHERE PatientID = '$patientID' AND DueDate >= CURDATE() ORDER BY DueDate");
$result = mysqli_fetch_assoc($query);
$payTestButtons = [];

while($result)
{
	$payTestButtons[] = $result['TestBillID'];
	$result = mysqli_fetch_assoc($query);
}

foreach($payTestButtons as $payTest)
{
	if(isset($_POST["$payTest"]))
	{
		$query = mysqli_query($conn, "DELETE FROM TestBills WHERE TestBillID = '$payTest'");
	}
}
?>

<html>
	<head>
		<title>MyHealth Portal</title>
		<link href = 'style.css' rel = 'stylesheet'>
	</head>
	<body>
		<?php
		require "patient-header.php";
		echo "<h2>Product Bills</h2>";
		$query = mysqli_query($conn, "SELECT * FROM ProductBills WHERE PatientID = '$patientID' AND DueDate >= CURDATE() ORDER BY DueDate");
		$result = mysqli_fetch_assoc($query);
		
		if(!$result)
		{
			echo	"<div class = \"infoBox patient\">
						<span>You don't have any product bills to pay.</span>
					</div>";
		}
		else
		{
			while($result)
			{
				$product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Products WHERE ProductID = '" . $result['ProductID'] . "'"));
				$pharmacy = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Pharmacies WHERE PharmacyID = '" . $result['PharmacyID'] . "'"));
				$address = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Addresses WHERE AddressID = '" . $pharmacy['AddressID'] . "'"));
				echo	"<form name = payProductBillsForm class = patient action = patient-bills.php method = post>
							<section class = formHeader>
								<h3>" . $product['Name'] . " Details</h3>
							</section>
							<section class = infoFields>
								<legend>Cost</legend>
								<span>$" . $product['Cost'] * $result['Quantity'] . "</span>
								<legend>Due Date</legend>
								<span>" . $result['DueDate'] . "</span>
							</section>
							</br>
							<section class = formHeader>
								<h3>" . $pharmacy['Name'] . " Details</h3>
							</section>
							<table class = infoFields>
								<tr>
									<td>
										<legend>Email Address</legend>
										<span>" . $pharmacy['EmailAddress'] . "</span>
									</td>
									<td>
										<legend>Phone Number</legend>
										<span>" . $pharmacy['PhoneNumber'] . "</span>
									</td>
								</tr>
								<tr>
									<td>
										<legend>Street Address</legend>
										<span>" . $address['Street'] . "</span>
										</br>
										<span>" . $address['City'] . "</span>
										</br>
										<span>" . $address['StateCode'] . " " . $address['ZipCode'] . "</span>
									</td>
								</tr>
							</table>
							</br>
							<section class = formButtons>
								<input name = " . $result['ProductBillID'] . " class = \"button red\" type = submit value = Pay />
							</section>
						</form>";
				$result = mysqli_fetch_assoc($query);
			}
		}
		
		echo "<h2>Service Bills</h2>";
		$query = mysqli_query($conn, "SELECT * FROM ServiceBills WHERE PatientID = '$patientID' AND DueDate >= CURDATE() ORDER BY DueDate");
		$result = mysqli_fetch_assoc($query);
		
		if(!$result)
		{
			echo	"<div class = \"infoBox patient\">
						<span>You don't have any service bills to pay.</span>
					</div>";
		}
		else
		{
			while($result)
			{
				$service = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Services WHERE ServiceID = '" . $result['ServiceID'] . "'"));
				$doctor = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Doctors WHERE DoctorID = '" . $result['DoctorID'] . "'"));
				$address = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Addresses WHERE AddressID = '" . $doctor['AddressID'] . "'"));
				echo	"<form name = payServiceBillsForm class = patient action = patient-bills.php method = post>
							<section class = formHeader>
								<h3>" . $service['Name'] . " Details</h3>
							</section>
							<section class = infoFields>
								<legend>Cost</legend>
								<span>$" . $service['Cost'] . "</span>
								<legend>Due Date</legend>
								<span>" . $result['DueDate'] . "</span>
							</section>
							</br>
							<section class = formHeader>
								<h3>" . $doctor['FirstName'] . " " . $doctor['LastName'] . " Details</h3>
							</section>
							<table class = infoFields>
								<tr>
									<td>
										<legend>Email Address</legend>
										<span>" . $doctor['EmailAddress'] . "</span>
									</td>
									<td>
										<legend>Phone Number</legend>
										<span>" . $doctor['PhoneNumber'] . "</span>
									</td>
								</tr>
								<tr>
									<td>
										<legend>Street Address</legend>
										<span>" . $address['Street'] . "</span>
										</br>
										<span>" . $address['City'] . "</span>
										</br>
										<span>" . $address['StateCode'] . " " . $address['ZipCode'] . "</span>
									</td>
								</tr>
							</table>
							</br>
							<section class = formButtons>
								<input name = " . $result['ServiceBillID'] . " class = \"button red\" type = submit value = Pay />
							</section>
						</form>";
				$result = mysqli_fetch_assoc($query);
			}
		}
		
		echo "<h2>Test Bills</h2>";
		$query = mysqli_query($conn, "SELECT * FROM TestBills WHERE PatientID = '$patientID' AND DueDate >= CURDATE() ORDER BY DueDate");
		$result = mysqli_fetch_assoc($query);
		
		if(!$result)
		{
			echo	"<div class = \"infoBox patient\">
						<span>You don't have any test bills to pay.</span>
					</div>";
		}
		else
		{
			while($result)
			{
				$test = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Tests WHERE TestID = '" . $result['TestID'] . "'"));
				$lab = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Labs WHERE LabID = '" . $result['LabID'] . "'"));
				$address = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Addresses WHERE AddressID = '" . $lab['AddressID'] . "'"));
				echo	"<form name = payTestBillsForm class = patient action = patient-bills.php method = post>
							<section class = formHeader>
								<h3>" . $test['Name'] . " Details</h3>
							</section>
							<section class = infoFields>
								<legend>Cost</legend>
								<span>$" . $test['Cost'] . "</span>
								<legend>Due Date</legend>
								<span>" . $result['DueDate'] . "</span>
							</section>
							</br>
							<section class = formHeader>
								<h3>" . $lab['Name'] . " Details</h3>
							</section>
							<table class = infoFields>
								<tr>
									<td>
										<legend>Email Address</legend>
										<span>" . $lab['EmailAddress'] . "</span>
									</td>
									<td>
										<legend>Phone Number</legend>
										<span>" . $lab['PhoneNumber'] . "</span>
									</td>
								</tr>
								<tr>
									<td>
										<legend>Street Address</legend>
										<span>" . $address['Street'] . "</span>
										</br>
										<span>" . $address['City'] . "</span>
										</br>
										<span>" . $address['StateCode'] . " " . $address['ZipCode'] . "</span>
									</td>
								</tr>
							</table>
							</br>
							<section class = formButtons>
								<input name = " . $result['TestBillID'] . " class = \"button red\" type = submit value = Pay />
							</section>
						</form>";
				$result = mysqli_fetch_assoc($query);
			}
		}
		?>
	</body>
</html>