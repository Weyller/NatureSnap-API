<?php
session_start();
if(empty($_SESSION['username']))
  echo "false";
else
  echo "true";
