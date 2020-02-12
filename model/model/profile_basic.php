<?php 
include "../address.php";
include $APP_ROOT.'assets/linker/db.php' ; 

// $id__ = 1;
$data =  file_get_contents('php://input');
$d1 = json_decode($data);




function verify_change_request($user_id){

  $conn = get_mysqli_connection();
  $sql = "select `full_name`, `mobile`, `institution_id`,`nid_or_passport`, `fathers_name`, `mother_name`, `spouse_name`, `number_of_children`, `profession`, `designation`, `institution`, `blood_group`, `date_of_birth`, `present_line1`, `present_district`, `present_post_code`, `present_country`, `parmanent_line1`,  `parmanent_district`, `parmanent_post_code`, `parmanent_country`  from all_info_together where  id = ".$user_id." ";

  $result = mysqli_query($conn, $sql);
  if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
  }

  $arrayValueString =  implode(',' ,$row);
  $arrayKeyString =  implode(',' , array_keys($row));
  $newArrayString = $arrayKeyString .'@#$'.$arrayValueString;
  $arrayTogether = explode('@#$' , $newArrayString);
  $array1keyString = $arrayTogether[0];
  $array2ValueString = $arrayTogether[1];
//final pair1
  $arrayKey_tobeUpdated =  $stringToArrayKey = explode(',' , $array1keyString);
  $arrayValue_tobeUpdated = $stringToArrayValue = explode(',' , $array2ValueString);
  $arrayAssoc;
  $i = 0;
  for($i =0; $i<count($stringToArrayKey); $i++){

   $arrayAssoc[$stringToArrayKey[$i]] = $stringToArrayValue[$i];
 }
 $sql = "select last_verified_info from all_info_together where  id = ".$user_id." ";

 $result = mysqli_query($conn, $sql);
 if (mysqli_num_rows($result) > 0) {
  $row = mysqli_fetch_assoc($result);
}


$array =  explode('@#$' , $row['last_verified_info']);
//desired array
$arrayKey_from_database =  $stringToArrayKey_from_verified_info = explode(',' , $array[0] );
$arrayValue_from_database = $stringToArrayValue_from_verified_info = explode(',' , $array[1] );
$arrayNew = [];
for($i=0; $i<count($arrayValue_from_database) ; $i++){
  if($arrayValue_from_database[$i] != $arrayValue_tobeUpdated[$i]){
    $arrayNew[$arrayKey_from_database[$i]] = [$arrayValue_tobeUpdated[$i], $arrayValue_from_database[$i]] ; 
  }
}

if(count($arrayNew)==0){

  $sql = "update all_info_together set change_request = 'not_requested'  where id = ".$user_id." ";
  mysqli_query($conn , $sql);

}

$conn->close();
}








if($d1->purpose == 'basic'){

  $GLOBALS['id__'] = $d1->id;
  $email = $d1->email;
  $full_name= $d1->full_name;
  $mobile= $d1->mobile;
  $institution_id= $d1->institution_id;
  $nid_or_passport= $d1->nid_or_passport;
  $blood_group= $d1->blood_group;
  $dob= $d1->dob;

  $conn = get_mysqli_connection();


  $sql = "select `full_name`, `mobile`, `institution_id`,  `nid_or_passport`, `fathers_name`, `mother_name`, `spouse_name`, `number_of_children`, `profession`, `designation`, `institution`, `blood_group`, `date_of_birth`, `present_line1`, `present_district`, `present_post_code`, `present_country`, `parmanent_line1`,  `parmanent_district`, `parmanent_post_code`, `parmanent_country` from all_info_together where  id = ".$id__."";
  $result = mysqli_query($conn, $sql);
  if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
  }

  $arrayValueString =  implode(',' ,$row);
  $arrayKeyString =  implode(',' , array_keys($row));
  $verified_info = $arrayKeyString .'@#$'.$arrayValueString;


  $sql = "call update_profile_basic(?,?,?,?,?,?,?,?,@result)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('isssssss' , $id__ , $verified_info , $full_name, $mobile , $institution_id , $blood_group , $nid_or_passport , $dob );
  $stmt->execute();
  $stmt->close();


  $sql = 'select @result as st'; 
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);

  verify_change_request($id__);


  echo $row['st'];



}else if($d1->purpose == 'getProfileBasicInfo'){
// $id__ = 1;
  $email = $d1->email;
  $conn = get_mysqli_connection();
  $sql = "select * from all_info_together where email = '".$email."' ";

  $result = mysqli_query($conn, $sql);
  if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    echo json_encode($row);
  } else {
    echo "0";
  }
  mysqli_close($conn);


  // $i = 0;
// echo json_encode(var_dump($row));
}else if($d1->purpose == 'profile_completeness_100'){
  
  $user_type = $d1->user_type;
  $conn = get_mysqli_connection();
  $sql = '';
  if($user_type == 'admin'){
    $sql = "call user_request( ".$id__." , @result)";
  }else if($user_type == 'user'){
    $sql = "update all_info_together set completeness = 100 where id = ".$id__." ";
  }
  $result = mysqli_query($conn, $sql);
  
  $conn->close();

}