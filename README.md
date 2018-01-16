# PHP PDO Database Library (dbPDO)
A PDO class by which you can access mysql Databse easily using PHP Data Objects (PDO) extension.

## Initialization
First import library (dbPDO.php) into your project, and include file dbPDO.php. 
```
<php require('dbPDO.php'); ?>
```
After that, create a new instance of the class.
```
<php $dbPDO = new dbPDO($host, $dbname, $dbuser, $dbpass); ?> 
```
By using the object $dbPDO you can call available methhods described below. Above $host, $dbname, $dbuser, $dbpass are the variables relates to database Host, Database Name, Database User and Password of database user repectively. 

## Insert Query
For insert you need table and data to insert into the table. Below is the example for insert method:-
```
<php
require('dbPDO.php');
$dbPDO = new dbPDO($host, $dbname, $dbuser, $dbpass);
$insertdata = array("name"=>"Yash", "email"=>"yash@localhost.com", "password"=>"newpass");
$tablename = 'emp';
$empid = $dbPDO->insert($tablename, $insertdata);
if($empid){ echo "Data Inserted"; }
else{ echo "Failed"; }
?>
```

## Update Query
Before call where method (Described below) for create where condition part of the update query. Below is the example for update method:-
```
<php
require('dbPDO.php');
$dbPDO = new dbPDO($host, $dbname, $dbuser, $dbpass);
$updatedata = array("name"=>"Yash", "password"=>"newpass");
$tablename = 'emp';
$dbPDO->where("email", "yash@localhost.com");
$up = $dbPDO->update($tablename, $updatedata);
if($up){ echo "Update Success"; }
else{ echo "Failed"; }
?>
```

## Delete Query

Before call where method (Described below) for create where condition part of the update query. Below is the example for update method:-
```
<php
require('dbPDO.php');
$dbPDO = new dbPDO($host, $dbname, $dbuser, $dbpass);
$tablename = 'emp';
$dbPDO->where("email", "yash@localhost.com");
$del = $dbPDO->delete($tablename);
if($del){ echo "Delete Success"; }
else{ echo "Failed"; }
?>
```


## Select Query

For select data from the table we have get() method. Below is the example how get() method works:- 

1) Select all records from database:-
```
<php
require('dbPDO.php');
$dbPDO = new dbPDO($host, $dbname, $dbuser, $dbpass);
$tablename = 'emp';
$records = $dbPDO->get($tablename);
print_r($records);
?>
```
2) Select records with where condition. Below is the example to select user whos id is 5:-
```
<php
require('dbPDO.php');
$dbPDO = new dbPDO($host, $dbname, $dbuser, $dbpass);
$tablename = 'emp';
$dbPDO->where("id", "5");
$records = $dbPDO->get($tablename);
print_r($records);
?>
```
3) Select records with sort by any column. Below is example to select records sort by id desc:-
```
<php
require('dbPDO.php');
$dbPDO = new dbPDO($host, $dbname, $dbuser, $dbpass);
$tablename = 'emp';
$dbPDO->orderBy("id", "desc");
$records = $dbPDO->get($tablename);
print_r($records);
?>
```
4) Select records with limit. Below is example to select limited 5 records only:-
```
<php
require('dbPDO.php');
$dbPDO = new dbPDO($host, $dbname, $dbuser, $dbpass);
$tablename = 'emp';
$dbPDO->limit(5);
$records = $dbPDO->get($tablename);
print_r($records);
?>
```
5) Select records with limit and offset. Below is example to select limited 5 records only with offset 3:-
```
<php
require('dbPDO.php');
$dbPDO = new dbPDO($host, $dbname, $dbuser, $dbpass);
$tablename = 'emp';
$dbPDO->limit(array(3,5));
$records = $dbPDO->get($tablename);
print_r($records);
?>
```


## Select Single Query

Select single query returns single record in array format from the table. Single query can be work with where condition. Below is the example to select single records from emp table.
```
<php
require('dbPDO.php');
$dbPDO = new dbPDO($host, $dbname, $dbuser, $dbpass);
$tablename = 'emp';
$records = $dbPDO->getOne($tablename);
print_r($records);
?>
```


## Run Query

runQuery() method use to run any select query statement. Below is the example of runQuery():-
```
<php
require('dbPDO.php');
$dbPDO = new dbPDO($host, $dbname, $dbuser, $dbpass);
$query = 'SELECT `name` FROM `emp` WHERE `id`=5';
$records = $dbPDO->runQuery($query);
print_r($records);
?>
```


## Where Method

The where() method is use for create where condition of the query. Where method works with operator, as well as without operator. If there is no any operator pass with where() method then Bydefault regular equal (==) operator will use. Below is the working of where()s method.

1) Without Pass the operator with where() method
```
<php
require('dbPDO.php');
$dbPDO = new dbPDO($host, $dbname, $dbuser, $dbpass);
$tablename = 'emp';
$dbPDO->where("email", "yash@localhost.com");
$records = $dbPDO->get($tablename);
print_r($records);
?>
```
2) With Pass the operator in where() method as third parameter. Available operators that can be use are <,>,=,<=,>=,<>
```
<php
require('dbPDO.php');
$dbPDO = new dbPDO($host, $dbname, $dbuser, $dbpass);
$tablename = 'emp';
$dbPDO->where("id", "10", ">");
$records = $dbPDO->get($tablename);
print_r($records);
?>
```
3) Create Between condition with where() method.
```
<php
require('dbPDO.php');
$dbPDO = new dbPDO($host, $dbname, $dbuser, $dbpass);
$tablename = 'emp';
$dbPDO->where("id", array("5", "10"), "BETWEEN");
$records = $dbPDO->get($tablename);
print_r($records);
?>
```
or
```
<php
require('dbPDO.php');
$dbPDO = new dbPDO($host, $dbname, $dbuser, $dbpass);
$tablename = 'emp';
$dbPDO->where("id", array ('BETWEEN' => array(5, 10)));
$records = $dbPDO->get($tablename);
print_r($records);
?>
```
4) Create where condition with IN operator.
```
<php
require('dbPDO.php');
$dbPDO = new dbPDO($host, $dbname, $dbuser, $dbpass);
$tablename = 'emp';
$dbPDO->where("id", array(1, 5, 7, 9, 15), "IN");
$records = $dbPDO->get($tablename);
print_r($records);
?>
```
or
```
<php
require('dbPDO.php');
$dbPDO = new dbPDO($host, $dbname, $dbuser, $dbpass);
$tablename = 'emp';
$dbPDO->where("id", array("IN"=>array(1, 5, 7, 9, 15)));
$records = $dbPDO->get($tablename);
print_r($records);
?>
```
5) Create or where condition with where() method.
```
<php
require('dbPDO.php');
$dbPDO = new dbPDO($host, $dbname, $dbuser, $dbpass);
$tablename = 'emp';
$dbPDO->where("email", "yash@localhost.com");
$dbPDO->orWhere("username", "yash");
$records = $dbPDO->get($tablename);
print_r($records);
?>
```

## OrderBy Conditions

orderBy() method used to create order condition part the select query. Below is the example to select records order by name ascending
```
<php
require('dbPDO.php');
$dbPDO = new dbPDO($host, $dbname, $dbuser, $dbpass);
$tablename = 'emp';
$dbPDO->orderBy("name", "asc");
$records = $dbPDO->get($tablename);
print_r($records);
?>
```


## GroupBy Conditions

groupBy() method used to create GROUP BY part of the select query. Below is the example to select records group by name.
```
<php
require('dbPDO.php');
$dbPDO = new dbPDO($host, $dbname, $dbuser, $dbpass);
$tablename = 'emp';
$dbPDO->groupBy("name");
$records = $dbPDO->get($tablename);
print_r($records);
?>
```

## Limit Conditions

limit() method used to create Limit part of the select query. Below is the examples how limit() method works.

1) Example to select limited 5 records only:-
```
<php
require('dbPDO.php');
$dbPDO = new dbPDO($host, $dbname, $dbuser, $dbpass);
$tablename = 'emp';
$dbPDO->limit(5);
$records = $dbPDO->get($tablename);
print_r($records);
?>
```
2) Limit with offset. Below is example to select limited 5 records only with offset 3:-
```
<php
require('dbPDO.php');
$dbPDO = new dbPDO($host, $dbname, $dbuser, $dbpass);
$tablename = 'emp';
$dbPDO->limit(array(3,5));
$records = $dbPDO->get($tablename);
print_r($records);
?>
```

## Developer Team
Hello, Folks! If you have any suggestion or problem to use or any query regarding the dbPDO library, Do not hesitate, feel free to contact us anytime. Our contact details are described below:-
* [Yashwant Mehar](mailto:yashwantmehar@gmail.com) - *Initial Development & Support* -
* [Rohit Mandawar](mailto:rohitmandawara@gmail.com) - *Contributor & Support* -
* [Prasoon Bajpai](mailto:prasoonb.22nov@gmail.com) - *Contributor & Suport* -
