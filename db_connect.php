<?php

class DB {

	private $servername = "localhost";
	private $username = "root";
	private $password = "";
	private $dbname = "users";

	private $grouPay_username;
	private $grouPay_password;
	private $grouPay_id;

	function register_user($user, $pass) {
		$this->grouPay_username = $user;
		$this->grouPay_password = $pass;
		$conn = mysqli_connect($this->servername, $this->username, $this->password, $this->dbname);

		if (mysqli_connect_errno()) {
		    echo json_encode(array("value" => "error"));
		} 

		$query = "SELECT * FROM users WHERE Username='$this->grouPay_username' 
		AND Password='$this->grouPay_password'";

		$check = mysqli_query($conn, $query);

		if(mysqli_num_rows($check) > 0)
		{
			echo json_encode(array("value" => "Account Exists"));
		}
		else
		{
			$sql = "INSERT INTO users (AccountID, Username, Password)
			VALUES (UUID(), '$this->grouPay_username', '$this->grouPay_password')";

			if(mysqli_query($conn, $sql) == true)
			{
				echo json_encode(array("value" => "Account Created"));
			}
			else
			{
				echo json_encode(array("value" => "error"));
			}
		}

	}

	function log_in_user($user, $pass) {
		$this->grouPay_username = $user;
		$this->grouPay_password = $pass;
		$conn = mysqli_connect($this->servername, $this->username, $this->password, $this->dbname);

		if (mysqli_connect_errno()) {
		    echo json_encode(array("value" => "error"));
		} 

		$sql = "SELECT * FROM users WHERE Username='$this->grouPay_username' 
		AND Password='$this->grouPay_password'";

		$result = mysqli_query($conn, $sql);

		if(mysqli_num_rows($result) > 0)
		{
			$row = mysqli_fetch_assoc($result);
			$id = $row["AccountID"];
			echo json_encode(array("value" => "true", "id" => $id));
		}
		else
		{
			echo json_encode(array("value" => "false"));
		}

	}

	function get_user_id() {
		return $this->grouPay_id;
	}

	function request_data($id) {

		$conn = mysqli_connect($this->servername, $this->username, $this->password, $this->dbname);
		if (mysqli_connect_errno()) {
		    echo json_encode(array("value" => "error"));
		} 

		$sql = "SELECT Username FROM users WHERE AccountID='$id'";

		$result = mysqli_query($conn, $sql);

		if(mysqli_num_rows($result) > 0)
		{
			$row = mysqli_fetch_assoc($result);
			echo json_encode(array("value" => $row["Username"]));
		}
		else
		{
			echo json_encode(array("value" => "false"));
		}
	}

	function save_event($owner, $title, $amount, $people, $description, $due_date, $privacy) {
		$conn = mysqli_connect($this->servername, $this->username, $this->password, $this->dbname);
		if (mysqli_connect_errno()) {
		    echo json_encode(array("value" => "error"));
		} 

		$people_array;
		$ownername = "";

		$name_result = mysqli_query($conn, "SELECT Username FROM users WHERE AccountID='$owner'");

		if(mysqli_num_rows($name_result) > 0)
		{
			$row = mysqli_fetch_assoc($name_result);
			$ownername = $row["Username"];
		}

		if(strlen($people) == 0)
		{
			$people = "";
		}
		else
		{
			$people_array = explode((" "), $people);
		}

		$event_array = array();

		$id = md5($owner.$title.$amount);

		$sql = "INSERT INTO events (Owner, OwnerName, EventId, Title, Description, Created, Due, Amount, Private) 
				VALUES ('$owner', '$ownername', '$id', '$title', '$description', now(), '$due_date', '$amount', '$privacy')";

				if(mysqli_query($conn, $sql) or die(mysqli_error($conn)))
		{
			echo json_encode(array("value" => "success"));
		}
		else
		{
			echo json_encode(array("value" => "error"));
		}

		$sql = "SELECT Events from users WHERE AccountID='$owner'";
		$result = mysqli_query($conn, $sql);

		while($row = mysqli_fetch_assoc($result))
		{
			if(empty($row["Events"]))
			{
				$sql3 = "UPDATE users 
				 SET Events = '$id'
				 WHERE users.AccountID = '$owner'";
				 mysqli_query($conn, $sql3);
			}
			else
			{
				$updated = $row["Events"].','.$id;
				$sql = "UPDATE users 
				 SET Events = '$updated'
				 WHERE users.AccountID = '$owner'";
				 mysqli_query($conn, $sql);
			}
		}

		if(strlen($people) != 0)
		{
		foreach ($people_array as $person) {
			$result = array();
			$result = explode(":", $person);
			$p = $result[0];
			$a = $result[1];

			$query_id = "SELECT EventId FROM events WHERE Owner='$owner' AND Title='$title' AND Amount='$amount' LIMIT 1";
			$query_person_id = "SELECT AccountID from users WHERE Username='$p' LIMIT 1";

			$id_result = mysqli_query($conn, $query_id);
			$person_id_result = mysqli_query($conn, $query_person_id);

			if(mysqli_num_rows($id_result) > 0)
			{

				$row = mysqli_fetch_assoc($id_result);
				$row2 = mysqli_fetch_assoc($person_id_result);

				$eventid = $row["EventId"];
				$person_id = $row2["AccountID"];

				$temp_query = "INSERT INTO invites (EventId, Person, AmountNeed) VALUES ('$eventid', '$person_id', '$a')";

				mysqli_query($conn, $temp_query);
			}
		}
	}

	}

	function retrieve_events($id) {
		$string_events = "";
		$event_array = array();
		$json_event_array = array();

		$conn = mysqli_connect($this->servername, $this->username, $this->password, $this->dbname);
		if (mysqli_connect_errno()) {
		    echo json_encode(array("value" => "error"));
		}

		$sql = "SELECT Events FROM users WHERE AccountID='$id'";

		$result = mysqli_query($conn, $sql);

		if(mysqli_num_rows($result) > 0)
		{
			$row = mysqli_fetch_assoc($result);
			$string_events = $row["Events"];
		}
		$event_array = explode(",", $string_events);

		foreach ($event_array as $event_id) {
			$sql2 = "SELECT * FROM events WHERE EventId='$event_id'";
			$result = mysqli_query($conn, $sql2);
			while($row = mysqli_fetch_assoc($result))
			{
				$json_event_array[] = $row;
			}
		}

		echo json_encode(array("Events"=>$json_event_array));

	}

	function retrieve_invites($id) {
		$invite_array = array();
		$invite_array_ids = array();
		$amounts = array();

		$conn = mysqli_connect($this->servername, $this->username, $this->password, $this->dbname);
		if (mysqli_connect_errno()) {
		    echo json_encode(array("value" => "error"));
		}

		$sql = "SELECT EventId FROM invites WHERE Person='$id'";
		$result = mysqli_query($conn, $sql);

		while($row = mysqli_fetch_assoc($result))
		{
			$invite_array_ids[] = $row["EventId"];
		}

		foreach ($invite_array_ids as $idi) {
			$sql2 = "SELECT events.*, invites.AmountNeed 
					 FROM events
					 INNER JOIN invites ON events.EventId = invites.EventId
					 WHERE events.EventId='$idi' AND invites.Person='$id'";
			$result = mysqli_query($conn, $sql2);

			if(mysqli_num_rows($result) > 0)
			{
				$invite_array[] = mysqli_fetch_assoc($result);
			}
		}

		if(!empty($invite_array))
		{
			echo json_encode(array("Invites"=>$invite_array));
		}
		else
		{
			return;
		}

	}

	function invite_response($eventid, $userid, $response, $amountdue) {
		if($response == "no")
		{
			$conn = mysqli_connect($this->servername, $this->username, $this->password, $this->dbname);
			if (mysqli_connect_errno()) {
			    echo json_encode(array("value" => "error"));
			}

			$sql = "DELETE FROM invites WHERE Eventid='$eventid' AND Person='$userid'";
			mysqli_query($conn, $sql);
			return;
		}

		if($response == "yes")
		{
			$uname = "";

			$conn = mysqli_connect($this->servername, $this->username, $this->password, $this->dbname);
			if (mysqli_connect_errno()) {
			    echo json_encode(array("value" => "error"));
			}

			$sql = "SELECT Username FROM users WHERE AccountID='$userid'";
			$result = mysqli_query($conn, $sql);

			if(mysqli_num_rows($result) > 0)
			{
				$row = mysqli_fetch_assoc($result);
				$uname = $row["Username"];
			}

			$sql2 = "SELECT People FROM events WHERE EventId='$eventid'";
			$result2 = mysqli_query($conn, $sql2);

			while($row = mysqli_fetch_assoc($result2))
			{
				if(empty($row["People"]))
				{
					$value = json_encode(array($uname => $amountdue));
					$sql3 = "UPDATE events SET People='$value' WHERE EventId='$eventid'";
					mysqli_query($conn, $sql3);
				}
				else
				{
					$temp[] = json_decode($row["People"],true);
					$add = array($uname => $amountdue);
					$temp[] = $add;
					$json = json_encode($temp);
					$sql4 = "UPDATE events SET People='$json' WHERE EventId='$eventid'";
					mysqli_query($conn, $sql4);
				}
			}

			$string_event_array;

			$sql4 = "SELECT Events FROM users WHERE AccountID='$userid'";
			$result = mysqli_query($conn, $sql4);

			if($row = mysqli_fetch_assoc($result))
			{
				if(!empty($row["Events"]))
				{
					$string_event_array = $row["Events"];
				}
			}

			if(!empty($string_event_array))
			{
				$updated_string_events_array = $string_event_array.','.$eventid;
				$sql5 = "UPDATE users SET events='$updated_string_events_array' WHERE AccountID='$userid'";
				mysqli_query($conn, $sql5);
			}
			else
			{
				$sql6 = "UPDATE users SET events='$eventid' WHERE AccountID='$userid'";
				mysqli_query($conn, $sql6);
			}

			


			$delete = "DELETE FROM invites WHERE Eventid='$eventid' AND Person='$userid'";
			mysqli_query($conn, $delete);
		}
	}

}
?>