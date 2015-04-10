<?php
$username = "gygi_magento";
$password = "03alpqasZA!@";
$hostname = "localhost"; 

//connection to gygicookingclasses.com database
$dbhandle = mysql_connect($hostname, $username, $password) 
  or die("Unable to connect to MySQL");
echo "Connected to MySQL<br>";
//select a database to work with
$selected = mysql_select_db("gygi_magento",$dbhandle) 
or die("Could not select examples");

require_once 'app/Mage.php';
umask(0);
Mage::app('default');
//$orders->orders[0];


$query=mysql_query('SELECT `sales_flat_order_grid`.`customer_id` FROM `sales_flat_order_item`,`sales_flat_order_grid` WHERE `sales_flat_order_grid`.`entity_id` =`sales_flat_order_item`.`order_id` AND `sales_flat_order_grid`.`customer_id`!=0 AND`sales_flat_order_item`.`product_id`=1892 GROUP BY `sales_flat_order_grid`.`customer_id`');

while($row=mysql_fetch_array($query))
{


echo $row['customer_id'].'<br>';



#customer id here
$customerId = $row['customer_id'];
#load customer object
$customer = Mage::getModel('customer/customer')->load($customerId); //insert cust ID
#create customer address array
$customerAddress = array();
#loop to create the array
foreach ($customer->getAddresses() as $address)
{
   $customerAddress = $address->toArray();
}
#displaying the array

$customer_email=mysql_fetch_array(mysql_query('SELECT `email` FROM `customer_entity` WHERE `entity_id`='.$row['customer_id']));

echo 'F Name' =.$customerAddress['firstname'].', L Name ='.$customerAddress['lastname'].', Email ='.$customer_email['email'];

}



?>