<?php

$connect = mysqli_connect("localhost", "root", "", "voting_system") or die("connection failed!");

if($connect){

echo "Connected!";

}

else{

echo "Not Connected!";
}

?>