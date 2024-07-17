<?php
function validate() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        session_start();
    }

}