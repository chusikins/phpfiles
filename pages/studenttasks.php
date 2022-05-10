<?php
session_start();



if ($_SESSION["userPermission"] !== 'student'){
  header("location: ../pages/login.php?error=logerror");
  exit();
}
require_once '../db/database_connection.php';
require_once '../includes/functions.inc.php';
//weird '' and "" shit, look into it.......

$group = $_SESSION["userGroup"];
$jsonTests=displayTests($link,$group);
 ?>
 <html lang="en" dir="ltr">
   <head>
     <meta charset="utf-8">
     <title></title>
   </head>
   <body>
     <p id="test">Привет <?php echo $_SESSION["userName"] ?> !</p>
     <table>

     </table>
   </body>
   <script>
   function generateTableHead(table, data) {
     let thead = table.createTHead();
     let row = thead.insertRow();
     for (let key of data) {
       let th = document.createElement("th");
       let text = document.createTextNode(key);
       th.appendChild(text);
       row.appendChild(th);
     }
   }

   function generateTable(table, data) {
     for (let i=0; i < data["ex_id"].length; i++){
       let row = table.insertRow();
       for (let key in Object.keys(data)){
         if (Object.keys(data)[key].localeCompare("link") == 0){
           let a = document.createElement('a');
           let cell = row.insertCell();
           let linkText = document.createTextNode("link");
           a.appendChild(linkText);
           a.href = data[Object.keys(data)[key]][i];
           cell.appendChild(a);
         } else {
         let cell = row.insertCell();
         let text = document.createTextNode(data[Object.keys(data)[key]][i]);
         cell.appendChild(text);
          }
       }
     }
   }
   let obj = <?php echo $jsonTests; ?>;
   let table = document.querySelector("table");
   let data = Object.keys(obj);
   generateTableHead(table, data);
   generateTable(table, obj);

   </script>
 </html>
