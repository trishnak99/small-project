<?php

	$inData = getRequestInfo();

	$passwordLocation="/home/david/SQLPassword.txt";
        $passwordFile=fopen($passwordLocation, "r") or die("unable to open file!");
        $sqlpassword=trim(fread($passwordFile, filesize($passwordLocation)));
        fclose($passwordFile);


	$sql="SELECT * FROM users WHERE username=? and password_hash=?";
	$conn= new mysqli("localhost", "root", $sqlpassword, "poosdsmall");

	if ($conn->connect_error) {
		returnWithError($conn->connect_error);
		return;
	}


	$prepared=$conn->prepare($sql);
	$prepared->bind_param("ss", $username, $password_hash);

	$username=$inData["username"];
	$password_hash=$inData["password_hash"];

	$prepared->execute();
	$returned=$prepared->get_result();
	$data=$returned->fetch_all();

	if (count($data)==0) {
		returnWithError();
		return;
	}

	returnWithInfo($data[0][0], $data[0][1], $data[0][2]);

	function getRequestInfo() {
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}

	function returnWithError( $error ) {
		$retValue='{"user_id":0, "username":"", "password_hash":"", "error": "'.$error.'"}';
		return sendResultInfoAsJson($retValue);
		//Echo($retValue);
	}

	function returnWithInfo( $id, $username, $password_hash )
	{
 		$retValue = '{"user_id":' . $id . ',"username":"' . $username . '","password_hash":"' . $password_hash . '"}';
		//Echo($retValue);
		sendResultInfoAsJson( $retValue );
	}

	function returnArray($array) {
		$retValue='[';
		$length=sizeof($array);
		$i=0;

		while ($i<$length) {
			$object=$array[$i];
			$retValue=$retValue.$object[3];

			if ($i+1<$length)
				$retValue = $retValue.", ";

			$i++;
		}
		$retValue=$retValue . ']';
		//Echo($retValue);

		sendResultInfoAsJson($retValue);

	}

?>
