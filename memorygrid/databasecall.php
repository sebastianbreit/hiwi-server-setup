<?php
// Adds MySQL support for experiment
//check which domain experiment is hosted on

function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 return $pageURL;
}

$curURL = curPageURL();
$parse = parse_url($curURL);
$domain = $parse['host'];

$host = 'db';
$user = 'root';
$pass = 'root';
$dbname = 'memorygrid';

if ($domain == 'localhost'){
	//MAMP MySQL settings
	$host = 'db';
	$user = 'root';
	$pass = 'root';
	$dbname = 'memorygrid';
}
elseif ($domain == 'www.causal-bayes.net'){
	//Björn's server
	$host = 'localhost';
	$user = 'db1019463-gp';
	$pass = 'kernelspace';
	$dbname = 'db1019463-gridsearch';
}
elseif ($domain == 'experiments.hmc-lab.com'){
	$host = 'localhost';
	$user = 'technical_user';
	$pass = 'password_placeholder';
	$dbname = 'memorygrid';
}

//	WHICH FUNCTION TO PERFORM? ////
if( isset($_GET['action']) ){
    $action = $_GET['action'];

    //1. Intialize the database with $iter iterations
    if ($action == 'initializeDB'){
    	$iterations = $_GET['iter'];
    	$handshake = $_GET['pass'];
    	if ($handshake=='reproducingkernelhilbertspace'){
    		initializeDB($host, $user, $pass, $dbname, $iterations);
    	}

    }
    //2. Assign a scenario
    elseif ($action == 'assignScenario'){
    	assignScenario($host, $user, $pass, $dbname);
    }
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST'){
  //3. Complete scenario
  $scenarioId = $_POST['scenarioId'];
	$MTurkID = $_POST['MTurkID'];
	$assignmentID = $_POST['assignmentID'];
	$scale = $_POST['scale'];
	$envOrder= $_POST['envOrder'];
	$searchHistory= $_POST['searchHistory'];
	$optimaGuess = $_POST['optimaGuess'];
	$reward = $_POST['reward'];
	$age = $_POST['age'];
	$gender = $_POST['gender'];
	$processDescription = $_POST['processDescription'];
  //TODO: update based on variables above
	completeScenario($host, $user, $pass, $dbname, $MTurkID, $assignmentID, $scale, $optimaGuess, $envOrder, $searchHistory, $reward, $age, $gender, $processDescription, $scenarioId);

}





///////////initial creation of scenarios///////////

function initializeDB($host, $user, $pass, $dbname, $iterations){
	//connect to database
	$conn = new mysqli($host, $user, $pass, $dbname);
	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
		};
	//create scenario array
	$scenarioArr = array();
	//loop through kernel parameterizations, and memory scenarios;
	for($iter = 0; $iter < $iterations; $iter ++){
		for($scenario = 0; $scenario <2; $scenario ++){ //two scenarios: Memory vs Non-Memory
		    for($kernel=0;$kernel<2; $kernel ++){//two kernel parameterizations
	    		$value = array('scenario' => $scenario, 'kernel' => $kernel);
	    		array_push ($scenarioArr, $value);
		}}};

	//Check if scenarios table exists
	$querycheck =  "SELECT 1 FROM scenarios2D";
	$query_result=mysqli_query($conn, $querycheck);

	//if table exists
	if ($query_result !== FALSE){
		//wipe table
		$deleteRows = "TRUNCATE TABLE scenarios2D";
		if (mysqli_query($conn,$deleteRows)) {
	    	echo "db table wiped";
		}
		else {
	    	echo "Error wiping table: " . $conn->error;
		}
	}
	//if table does not exist
	else{
		//Create scenarios table
		$createScenarios = "CREATE TABLE scenarios2D(
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		MTurkID VARCHAR(255) NULL,
		assignmentID VARCHAR(255) NULL,
		scenario INT(2) NULL,
		kernel INT(2) NULL,
		scale BLOB,
		envOrder BLOB,
		searchHistory BLOB,
		optimaGuess BLOB,
		reward DECIMAL(3,2) DEFAULT 0.00,
		start DATETIME DEFAULT NULL,
		end DATETIME DEFAULT NULL,
		age INT(3) NULL,
		gender INT(2) NULL,
		processDescription BLOB,
		assigned INT(2) DEFAULT 0,
		completed INT(2) DEFAULT 0
		)
		";

  	if (mysqli_query($conn, $createScenarios)) {
  	    echo "Table created successfully";
  	}
    else {
  	    echo "Error creating table: " . $conn->error;
  	}
	}

	//fill scenario table with data
	foreach($scenarioArr as $condArr){
		$scenario = $condArr['scenario'];
		$kernel = $condArr['kernel'];
		$assigned = 0;
		$completed = 0;
		$sql = "INSERT INTO scenarios2D (scenario, kernel,  assigned, completed)
		VALUES ($scenario, $kernel, $assigned, $completed)";
		if ($conn->query($sql) === TRUE) {
	} 	else {
	    	echo "Error: " . $sql . "<br>" . $conn->error;
	}
	}

	//check number of entries
	$countQuery = "SELECT COUNT(*) FROM scenarios2D";
	$countResult = mysqli_query($conn,$countQuery);
	$count = mysqli_fetch_row($countResult);
	echo $count[0];
	$conn->close();

};

/////////retrieve random scenario that has not yet been assigned////////
function assignScenario($host, $user, $pass, $dbname){
	//connect to database
	$conn = new mysqli($host, $user, $pass, $dbname);
	// Check connection
	if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
	} ;
	//1:randomly find a scenario where assigned<1 and completed <1
	$query = "SELECT * FROM scenarios2D WHERE assigned<1 AND completed<1 ORDER BY RAND() LIMIT 1";
	$result = mysqli_query($conn, $query);
	if ($result->num_rows !== 0){
		$scenarioRow = $result->fetch_array(MYSQLI_ASSOC);
		$id = $scenarioRow['id'];
		$scenario = $scenarioRow['scenario'];
		$kernel = $scenarioRow['kernel'];
		//update 'assigned' to 1
		$update = $conn -> prepare("UPDATE scenarios2D SET start=now(), assigned=assigned + 1 WHERE id=?");
		$update -> bind_param("i", $id);
		if ($result = $update->execute()){
		  	$update->free_result();
		}
		echo json_encode(array('scenarioId' => $id, 'scenario'=>$scenario, 'kernel'=>$kernel));
		$conn->close();
	}
	//2: if all scenarios are assigned, create a new one
	else{
		$result->close();
		$stmt = $conn->prepare("INSERT INTO scenarios2D (scenario, kernel,  start, assigned) VALUES (?, ?, ?, NOW(), '1')");
		$stmt->bind_param("iii", $scenario, $kernel); //bind parameters to mysql query
		$scenario = rand(0,1); //generate a new scenario number
		$kernel = rand(0,1); //generate a new kernel parameterization number
		$stmt->execute();
		$id = mysqli_insert_id($conn);
		echo json_encode(array('scenarioId' => $id, 'scenario'=>$scenario, 'kernel'=>$kernel));
		$stmt->close();
		$conn->close();

	}
}


////////////mark scenario as completed/////////////////
function completeScenario($host, $user, $pass, $dbname, $MTurkID, $assignmentID, $scale, $optimaGuess, $envOrder, $searchHistory, $reward, $age, $gender, $processDescription, $scenarioId){
	//connect to database
	$conn = new mysqli($host, $user, $pass, $dbname);
	// Check connection
	if ($conn->connect_error) {
    	die("Connection failed: " . $conn->connect_error);
	}
	//sanitize text fields
	$MTurkID = mysqli_real_escape_string($conn, $MTurkID);
	$assignmentID = mysqli_real_escape_string($conn, $assignmentID);
	$processDescription = mysqli_real_escape_string($conn, $processDescription);
	$query = $conn->prepare("UPDATE scenarios2D set end=now(), MTurkID=?, assignmentID=?, scale=?, optimaGuess = ?, envOrder = ?, searchHistory=?, reward = ?, age= ?, gender = ?, processDescription=?, completed=completed + 1 WHERE id=?");
	$query -> bind_param("ssssssdiisi", $MTurkID, $assignmentID, $scale, $optimaGuess, $envOrder, $searchHistory, $reward, $age, $gender, $processDescription, $scenarioId);
	if ($result = $query->execute()){
  	echo "success";
  	$query->free_result();
	}
	else {
  	echo "error";
	}
	$conn->close();

}
?>
