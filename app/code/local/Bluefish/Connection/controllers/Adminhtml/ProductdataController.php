<?php
/*******************************/
##### This File Is used For Calling The Customer And Sale POST Method
/*******************************/

class Bluefish_Connection_Adminhtml_ProductdataController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
    {
		$connection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$prefix 	= Mage::getConfig()->getTablePrefix();
			extract($_GET);
			extract($_REQUEST);
			$error = "";
			$success = "";
			$errorupdate = "";
			#### Add Condition
			if($_GET["hdnCmd"] == "Add")
			{
				if(trim($txtAddCode) == "")
					$error .= "Please enter Bluestore product Code.<br>";
				if(trim($txtAddMagentoCategoryID) == "")
					$error .= "Please enter Magento Category ID.<br>";
				if(trim($txtAddCategoryID) == "")
					$error .= "Please enter Magento Product ID.<br>";

				if($error == "")	
				{
					$resultExist 	 = $connection->query("select * from ".$prefix."bluefish_product WHERE (product_code = '".$txtAddCode."' OR product_id = '".$txtAddCategoryID."')");
					$resultExistSet  = $resultExist->fetchAll(PDO::FETCH_ASSOC);
					$numberExistRows = count($resultExistSet);
					
					if($numberExistRows == 0)
					{
						$connection->query("INSERT INTO ".$prefix."bluefish_product(id,product_code,category_id,product_id,created_time,update_time)
											VALUES('','".$txtAddCode."' ,'".$txtAddMagentoCategoryID."' ,'".$txtAddCategoryID."','".now()."','')");
						$txtAddCode 		= "";
						$txtAddCategoryID	= "";
						$success .= "Record successfully Added.<br>";
					}
					else
						$error .= "Bluestore Product Code or Magento Product ID already Exists.<br>";
				}
			}

			##### Update Condition
			if(isset($_GET["hdnCmd"]) && ($_GET["hdnCmd"] == "Update"))
			{
				if(trim($txtEditCode) == "")
					$errorupdate .= "Please enter Bluestore Product Code.<br>";
				if(trim($txtMagentoCategoryID) == "")
					$errorupdate .= "Please enter Magento Category ID.<br>";
				if(trim($txtEditCategoryID) == "")
					$errorupdate .= "Please enter Magento Product ID.<br>";
					
				if($errorupdate == "")	
				{
					$resultupdate 	 = $connection->query("select * from ".$prefix."bluefish_product WHERE (product_code = '".$txtEditCode."' OR product_id = '".$txtEditCategoryID."') and id != '".$hdnEditCustomerID."'");
					$resultUpdateSet  = $resultupdate->fetchAll(PDO::FETCH_ASSOC);
					$numberUpdateRows = count($resultUpdateSet);
					
					if($numberUpdateRows == 0)
					{			
						$connection->query("UPDATE ".$prefix."bluefish_product SET product_code= '".$txtEditCode."',category_id= '".$txtMagentoCategoryID."',product_id= '".$txtEditCategoryID."' where id = '".$hdnEditCustomerID."'");
						$success .= "Record successfully Updated.<br>";
					}
					else
						$errorupdate .= "Bluestore Product Code or Magento Product ID already Exists.<br>";
				}
			}

			##### Delete Condition
			if($_GET["Action"] == "Del")
			{
				$connection->query("DELETE FROM ".$prefix."bluefish_product where id = '".$_GET[ConnectionID]."'");
			}
		
		$result = $connection->query("select * from ".$prefix."bluefish_product");
		$resultSet = $result->fetchAll(PDO::FETCH_ASSOC);
		$numberRows = count($resultSet);
		?>
		<style>
		input[type="button"] {
			background: url("/images/btn_bg.gif") repeat-x scroll 0 100% #FFAC47;
			border-color: #ED6502 #A04300 #A04300 #ED6502;
			border-style: solid;
			border-width: 1px;
			color: #FFFFFF;
			cursor: pointer;
			font: bold 12px arial,helvetica,sans-serif;
			padding: 1px 7px 2px;
			text-align: center !important;
			white-space: nowrap;
		}
		</style>
		
		<script type="text/javascript" >
			function updateCategory(value)
			{	
				document.getElementById("hdnCmd").value = value;
				document.frmMain.submit();
			}
			function editButton(id)
			{	
				window.location = "<?=$_SERVER['PHP_SELF'];?>?Action=Edit&ConnectionID="+id;
			}
			function deleteButton(id)
			{	
				if(confirm('Confirm Delete?')==true)
				{
					window.location = "<?=$_SERVER["PHP_SELF"];?>?Action=Del&ConnectionID="+id;
				}
			}
		</script>
		
		<?php 
			if($error != "" || $errorupdate != "")
			{
				echo "<div align='left' style='font-family: arial;font-size: 12px;color:red;'>$error.$errorupdate</div>";
			}
			if($success != "")
			{
				echo "<div align='left' style='font-family: arial;font-size: 12px;color:blue;'>$success</div>";
			}		?>
		<br/>
		<form name="frmMain" method="GET" action="">
		<input type="hidden" name="hdnCmdtest" id="hdnCmdtest" value="test">
		<input type="hidden" name="hdnCmd" id="hdnCmd" value="">
		<table width="100%" border="1" style="border-collapse:collapse;">
		  <tr style="font-family: arial;font-size: 12px;background-color:#6F8992;color:white;">
			<th width="79"> <div align="center">Bluestore Product Code</div></th>
			<th width="100"> <div align="center">Magento Category ID</div></th>
			<th width="100"> <div align="center">Magento Product ID</div></th>
			<th width="2"> <div align="center">Created Time</div></th>
			<th width="148"> <div align="center">Action</div></th>
		  </tr>
		<?php    
		for($i=0;$i<$numberRows;$i++)
		{
			if($i%2 == 0)
				$trcolor = "#DDFFDD";
			else
				$trcolor = "#EEFFEE";		
				
			if($resultSet[$i][id] == $_GET["ConnectionID"] and ($_GET["Action"] == "Edit" || $errorupdate == ""))
			{
			
		  ?>
			  <tr style="font-family: arial;font-size: 13px;background-color:<?php echo $trcolor;?>">
				<td align="center">
				<input type="hidden" name="hdnEditCustomerID" size="5" value="<?=$resultSet[$i][id];?>">
				<input type="text" name="txtEditCode" size="5" value="<?=$resultSet[$i][product_code];?>"></td>
				<td align="center"><input type="text" name="txtMagentoCategoryID" size="5" value="<?=$resultSet[$i][category_id];?>"></td>				
				<td align="center"><input type="text" name="txtEditCategoryID" size="5" value="<?=$resultSet[$i][product_id];?>"></td>
				 <td align="center"><?=$resultSet[$i][created_time];?></td>
				<td align="right"><div align="center">
				  <input name="btnAdd" type="button" id="btnUpdate" value="Update" OnClick="updateCategory('Update');">
				  <input name="btnAdd" type="button" id="btnCancel" value="Cancel" OnClick="window.location='<?=$_SERVER["PHP_SELF"];?>';">
				</div></td>
			  </tr>
		  <?php
			}
		  else
			{
		  ?>
			  <tr style="font-family: arial;font-size: 13px;background-color:<?php echo $trcolor;?>">
				<td><div align="center"><?=$resultSet[$i][product_code];?></div></td>
				<td><div align="center"><?=$resultSet[$i][category_id];?></div></td>
				<td align="center"><?=$resultSet[$i][product_id];?></td>
				<td align="center"><?=$resultSet[$i][created_time];?></td>
				<td align="center"><input name="btnEdit" type="button" id="btnEdit" value="Edit" OnClick="editButton('<?=$resultSet[$i][id];?>');"> &nbsp;&nbsp;&nbsp; <input name="btnDelete" type="button" id="btnDelete" value="Delete" OnClick="deleteButton('<?=$resultSet[$i][id];?>')"></a></td>
			  </tr>
		  <?php
			}
		}
		?>
		  <tr style="font-family: arial;font-size: 13px;background-color:6F8992;">
			<td><div align="center"><input type="text" name="txtAddCode" value="<?=$txtAddCode?>" size="5"></div></td>
			<td align="center"><input type="text" name="txtAddMagentoCategoryID" size="5" value="<?=$txtAddMagentoCategoryID?>"></td>			
			<td align="center"><input type="text" name="txtAddCategoryID" size="5" value="<?=$txtAddCategoryID?>"></td>
			<td align="center"><input type="text" value="<?=date("Y-m-d H:i:s")?>" readonly="readonly" name="txtAddCreatedTime" size="20"></td>
			<td align="right"><div align="center">
			  <input name="btnAdd" type="button" id="btnAdd" value="Add" OnClick="frmMain.hdnCmd.value='Add';frmMain.submit();">
			</div></td>
		  </tr>
		</table>
		</form>
		<br/><br/>
	<?php		
		
    }
}
?>
