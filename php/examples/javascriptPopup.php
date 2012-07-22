<?php

$myMsg = "Suck a fatty";

echo "
<script language='JavaScript'>

<!-- Hide the script from old browsers --

function loadalert () 

        {alert('$myMsg')

}

// --End Hiding Here -->

</script>";

?>

<body onLoad="loadalert()">
