<?php
session_start();
require_once "pdo.php";
require_once "util.php";

 
 flashMessages();
?>
<html>
<title>Daniel Vásquez</title>
<body>
<div class="container">

<h1> Resume Registry</h1>



<?php

    $stmt = $pdo->query("SELECT first_name, headline, profile_id FROM profile");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    

        
       
            if(isset($_SESSION['name'])){
                echo('<p><a href="logout.php"> log out</a></p>');
                echo('<p><a href="add.php"> Add New Entry</a></p>');
                if(!$rows)
        {
            echo"No rows found\n ";
        }
    else
        {
                echo('<table border="3">'."\n");
                echo"<tr><td>";
                echo" Name ";
                echo"</td><td>";
                echo" Headline ";
                echo("</td><td>");
                echo" Action ";
                echo("</td></tr>\n");
                echo"</td></tr>";

                foreach ( $rows as $row )
                {
                    echo"<tr><td>";
                    echo('<a href="view.php?profile_id='.$row['profile_id'].'">'.$row['first_name'].'</a>');
                    echo"</td><td>";
                    echo($row['headline']);
                  
                    echo("</td><td>");
                    echo('<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a> / ');
                    echo('<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>');
                    echo("</td></tr>\n");
              
                }
                echo'</table>';
            }
            }
            else{
                if(!$rows)
        {
            echo"No rows found\n ";
        }
    else
        {
                echo('<table border="3">'."\n");
                echo"<tr><td>";
                echo" Name ";
                echo"</td><td>";
                echo" Headline ";
             
                echo"</td></tr>";

                foreach ( $rows as $row )
                {
                    echo"<tr><td>";
                    echo('<a href="view.php?profile_id='.$row['profile_id'].'">'.$row['first_name'].'</a>');
                    echo"</td><td>";
                    echo($row['headline']);
                  
                    echo"</td></tr>";
              
                }
                echo'</table>';
            }
       
                echo('<p><a href="login.php"> Please log in</a></p>');
            }
        
    ?>


</div>
</body>