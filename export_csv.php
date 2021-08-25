<?php require_once('../configuration/config.php'); 
 
   require '../PHPMailer/PHPMailerAutoload.php';
	
	$sql = "SELECT * FROM clientdetails WHERE companyid='" . $_SESSION['companyid'] . "' AND status != 2 ORDER BY fullname ASC";
    
    $query = mysqli_query($con, $sql) or die("could not select");
    $field = mysqli_num_fields($query);

	header('Content-Type: text/csv; charset=utf-8');  
      header('Content-Disposition: attachment; filename=export-clients-data.csv');  
      $output = fopen("php://output", "w");  
      fputcsv($output, array('ID', 'Company ID', 'Full Name', 'User Name', 'Company Name', 'Occupation', 'Job Title', 'Phone', 'Founded', 'Website', 'Email', 'Bio', 'Additional Text','Address', 'Keywords', 'Photo'));  
    //  $query = "SELECT * from employeeinfo ORDER BY emp_id DESC";  
      $result = mysqli_query($con, $sql);  
	  $csv_data = array();
	   $i=0;
      while($row = mysqli_fetch_assoc($result))  
      {  
		 
			
		$csv_data[$i] = array($row['clientid'],$row['companyid'],$row['fullname'],$row['username'],$row['companyname'],$row['occupation'],$row['jobtitle'],$row['phone'],$row['founded'],$row['website'],$row['email'],$row['bio'],$row['additionaltext'], $row['address'], $row['keywords'],$row['photo']);		
		 
           //fputcsv($output, $row);  
		 $i++;
      }  
	  foreach ($csv_data as $fields) {
		//fputcsv($fp, $fields);
		fputcsv($output, $fields);
	}
	  
      fclose($output);  

 
?>