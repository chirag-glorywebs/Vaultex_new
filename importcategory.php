<?php 
require_once('db.php'); 
if(isset($_POST["Import"])){
	$filename=$_FILES["file"]["tmp_name"];	
	if($_FILES["file"]["size"] > 0)
	{
		$allrecord = readCSV($filename);
		    echo '<pre>';
		print_r($allrecord);
		echo '</pre>';  
exit();		
		$updid = $_SESSION['what_adminid'];
		$upddate = date("Y-m-d H:i:s");
		$sysdate = date("Y-m-d");
		$icount = 0;
		if(!empty($allrecord)){
			foreach($allrecord as $value){
				if($icount!= 0){
				$parentcategory=trim($value[0]);
				$catname =	trim($value[1]);
				$description =	$value[2];
				if(!empty($value[3])){
				$is_deal_cat =	$value[3];
				}else{
				$is_deal_cat =	0;	
				}
				$discount_lable =	$value[4];
				$SeName =	$value[5];
				$SeDescription =	$value[6];
				$SeTitle =	$value[7];
				$SeKeyword =	$value[8];
				$DisplayOrder =	$value[8];
				if(!empty($value[9])){
				$Status =	$value[9];
				}else{
				$Status =	1;
				}
				
				if(isset($catname) && !empty($catname)){
				if(!empty($parentcategory)){
					$rsql="select * from category where CategoryName='".$parentcategory."' ";
					$rres=mysqli_query($dbLink,$rsql) or die ('Could Not Select client');
					if(mysqli_num_rows($rres)>0){
						$sqlid = "select CategoryID from category where CategoryName='".$parentcategory."'";
						$resid = mysqli_query($dbLink,$sqlid) or die('not select id');
						$dataid = $resid->fetch_assoc();
						$catid = $dataid['CategoryID'];
						
						$irsql="select * from category where CategoryName='".$catname."' ";
						$irres=mysqli_query($dbLink,$irsql) or die ('Could Not Select client');
						if(mysqli_num_rows($irres) == 0){
							if(!empty($catid)){
							$sql = "INSERT into category (CategoryName,parent,Description,is_deal_cat,discount_lable,SeName,SeDescription,SeTitle,SeKeyword,DisplayOrder,Status,CreatedOn,CreatedBy,UpdatedOn,UpdatedBy) 
							values ('$catname','$catid','$description',$is_deal_cat,$discount_lable,'$SeName','$SeDescription','$SeTitle','$SeKeyword','$DisplayOrder','$Status','$sysdate','$updid','$upddate','$updid')";
							$result = mysqli_query($dbLink, $sql);
							}
						}
					}else{
						$sql_newcat = "INSERT into category (CategoryName,parent,Status,CreatedOn,CreatedBy,UpdatedOn,UpdatedBy) values ('$parentcategory','0','$Status','$sysdate','$updid','$upddate','$updid')";
						$sql_newcatresult = mysqli_query($dbLink, $sql_newcat);
						 
					    $main_cat_id = mysqli_insert_id($dbLink);	 
						
						$ierres="select * from category where CategoryName='".$catname."' ";
						$ierres=mysqli_query($dbLink,$ierres) or die ('Could Not Select client');
						if(mysqli_num_rows($ierres) == 0){
							
							if(!empty($main_cat_id)){
								$sql = "INSERT into category (CategoryName,parent,Description,Status,CreatedOn,CreatedBy,UpdatedOn,UpdatedBy) 
								values ('$catname','$main_cat_id','$description','$Status','$sysdate','$updid','$upddate','$updid')";
								$result = mysqli_query($dbLink, $sql);
								 
							}
						}
					}
				}else{	
					$ersql="select * from category where CategoryName='".$catname."' ";
					$ersql=mysqli_query($dbLink,$ersql) or die ('Could Not Select client');
					if(mysqli_num_rows($ersql) == 0){
						$sql = "INSERT into category (CategoryName,parent,Description,is_deal_cat,discount_lable,SeName,SeDescription,SeTitle,SeKeyword,DisplayOrder,Status,CreatedOn,CreatedBy,UpdatedOn,UpdatedBy) 
						values ('$catname','0','$description',$is_deal_cat,$discount_lable,'$SeName','$SeDescription','$SeTitle','$SeKeyword','$DisplayOrder','$Status','$sysdate','$updid','$upddate','$updid')";
						$result = mysqli_query($dbLink, $sql);
					}
					 
				}
				}
			} 
			$icount++;
			}
		
		}
	}
	/* header("Location: category.php?msg=Data Imported Successfully");
	exit(); */
}

function readCSV($csvFile){
    $file_handle = fopen($csvFile, 'r');
    while (!feof($file_handle) ) {
        $line_of_text[] = fgetcsv($file_handle, 1024);
    }
    fclose($file_handle);
    return $line_of_text;
}
 

 


?>