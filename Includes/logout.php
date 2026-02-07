<?php
session_name('agro_buyer');
session_start();
session_destroy();
echo "<script>window.open('../BuyerPortal2/bhome.php','_self')</script>";
