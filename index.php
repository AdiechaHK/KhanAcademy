<html>
  <head>
    <title>Test</title>
    <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
  </head>
  <body>
    <h1>Hello</h1>
    <a href="http://localhost:9000/test.html">Create Quiestion</a>
<?php

if ($handle = opendir('./json')) {
    // echo "Directory handle: $handle\n";
    // echo "Entries:\n";
    echo "<ul>";
    /* This is the correct way to loop over the directory. */
    while (false !== ($entry = readdir($handle))) {
        if($entry != "." && $entry != "..") {
            echo "<li><a href='http://localhost:9000/codegeeks.html#".$entry."'>".$entry."</a></li>";
        }
    }
    echo "</ul>";

    // /* This is the WRONG way to loop over the directory. */
    // while ($entry = readdir($handle)) {
    //     echo "$entry\n";
    // }

    closedir($handle);
}
?>

  </body>
</html>