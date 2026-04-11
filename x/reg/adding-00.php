<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<title>POCS: Registration</title>
		<style>
			* {
				font-family: monospace;
			}
		</style>
	</head>
	<body>

<a href="register.php">register</a>
<a href="adding.php">adding</a>
<a href="dropping.php">dropping</a>

<?php

require_once("errors.php");
require_once("connect.php");



function convertTimeToMinutes($time) {
    list($hours, $minutes) = explode(':', $time);
    return $hours * 60 + $minutes;
}

function conflicts($inputSchedule, $existingSchedules) {
	$conflicts = [];
    $inputDay = $inputSchedule['dow'];
    $inputStart = convertTimeToMinutes($inputSchedule['beg']);
    $inputEnd = convertTimeToMinutes($inputSchedule['end']);

    foreach ($existingSchedules as $schedule) {
        if ($schedule['dow'] === $inputDay) {
            $existingStart = convertTimeToMinutes($schedule['beg']);
            $existingEnd = convertTimeToMinutes($schedule['end']);

            // Check for overlap
            if ($inputStart < $existingEnd && $existingStart < $inputEnd) {
            	$conflicts[] = $schedule;
            }
        }
    }
    return $conflicts;
}


$iscnf = TRUE;

$inp=[];
$stu = '';
$sbj = '';
$dow = '';
$beg = '';
$end = '';


if(
	isset($_POST['stu']) &&
	isset($_POST['sbj']) &&
	isset($_POST['dow']) &&
	isset($_POST['beg']) &&
	isset($_POST['end'])) {

	$stu = $_POST['stu'];
	$sbj = $_POST['sbj'];
	$dow = $_POST['dow'];
	$beg = $_POST['beg'];
	$end = $_POST['end'];

	if(	isset($_POST['submit']) &&
		$_POST['submit']=='Add') {

		$sql="insert into schedule(student_id,subject_id,dayOfWeek,beginTime,endTime) values(?,?,?,?,?)";

		$stm=mysqli_prepare($con,$sql);
		mysqli_stmt_bind_param($stm,"sssss",$stu,$sbj,$dow,$beg,$end);
		if(mysqli_stmt_execute($stm)) {
			echo "Record added";
		} else {
			echo "Error adding record";
		}
		mysqli_stmt_close($stm);

	} else if(	isset($_POST['submit']) &&
			 	$_POST['submit']=='Check') {

		$inp = [
			'stu' => $_POST['stu'],
			'sbj' => $_POST['sbj'],
			'dow' => $_POST['dow'],
			'beg' => $_POST['beg'],
			'end' => $_POST['end']
		];

		$sql = "select * from student where id=?";
		$stm=mysqli_prepare($con,$sql);
		mysqli_stmt_bind_param($stm,"s",$inp['stu']);
		mysqli_stmt_execute($stm);
		$res=mysqli_stmt_get_result($stm);
		mysqli_stmt_close($stm);

		if(mysqli_num_rows($res)>0) {

			if(convertTimeToMinutes($inp['beg']) < convertTimeToMinutes($inp['end'])) {

				$sch = [];
				$sql = "select * from schedule";
				$res = mysqli_query($con, $sql);
				if (mysqli_num_rows($res) > 0) {
					while($row = mysqli_fetch_assoc($res)) {
						$sch[]=[
							'idn' => $row['id'],
							'stu' => $row['student_id'],
							'sbj' => $row['subject_id'],
							'dow' => $row['dayOfWeek'],
							'beg' => $row['beginTime'],
							'end' => $row['endTime']
						];
					}
				}

				$cnf=conflicts($inp,$sch);
				if(count($cnf)>0) {
					echo "<table border>";
					echo "<caption><b>Conflict Schedule</b></caption>";
					echo "<tr>";
					echo "<th>ID</th>";
					echo "<th>Student&nbsp;ID</th>";
					echo "<th>Subject&nbsp;ID</th>";
					echo "<th>Day</th>";
					echo "<th>Begin&nbsp;Time</th>";
					echo "<th>End&nbsp;Time</th>";
					echo "</tr>";
					foreach($cnf as $row) {
						echo "<tr>";
						echo "<td>" . $row["idn"] . "</td>";
						echo "<td>" . $row["stu"] . "</td>";
						echo "<td>" . $row["sbj"] . "</td>";
						echo "<td>" . $row["dow"] . "</td>";
						echo "<td>" . date("g:i A",strtotime($row["beg"])) . "</td>";
						echo "<td>" . date("g:i A",strtotime($row["end"])) . "</td>";
						echo "</tr>";
					}
					echo "</table>";
					$iscnf = TRUE;
				} else {
					echo "No conflict schedule";
					$iscnf = FALSE;
				}

			} else {
				echo "Invalid Time";
			}
		} else {
			echo "Invalid Student ID";
		}
 	}
}



echo "<form method='post' action='".$_SERVER['PHP_SELF']."'>";

echo "<table>";


echo "<tr>";
echo "<td>Student ID</td>";
echo "<td><input name='stu' type='text' value='".$stu."' pattern='^[A-L]\d{4}$' size='5' maxlength='5'></td>";
echo "</tr>";


echo "<tr>";
$sql = "select id,description from subject";
$res = mysqli_query($con, $sql);
if (mysqli_num_rows($res) > 0) {
	echo "<td>Select&nbsp;Subject</td>";
	echo "<td><select name='sbj' required>";
	echo "<option value=''></value>";
	while($row = mysqli_fetch_assoc($res)) {
		echo "<option value='".$row['id']."'".($row['id']==$sbj?'selected':'').">".$row['id']." --- ".$row['description']."</option>";
	}
	echo "</select></td>";
}
echo "</tr>";



echo "<tr>";
$dows=array("M","T","W","T","F","S","D");
echo "<td>Select&nbsp;Day</td>";
echo "<td><select name='dow' required>";
echo "<option value=''></value>";
foreach($dows as $d) {
	echo "<option value='".$d."' ".($dow==$d?'selected':'').">".$d."</option>";
}
echo "</select></td></tr>";



echo "<tr><td>Begin&nbsp;Time</td><td><input name='beg' type='time' value='".$beg."' required></td></tr>";

echo "<tr><td>End&nbsp;Time</td><td><input name='end' type='time' value='".$end."' required></td></tr>";

echo "<tr><td colspan='2' align='right'><input name='submit' type='submit' value='Check'></td></tr>";

if(!$iscnf) {
	echo "<tr><td colspan='2' align='right'><input name='submit' type='submit' value='Add'></td></tr>";
}


echo "</form>";


?>



	</body>
</head>
