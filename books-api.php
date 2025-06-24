<?php
// (A) LOAD BOOK LIBRARY
require "books-lib.php";

// (B) STANDARD JSON RESPONSE
function respond ($status, $message, $more=null, $http=null) {
    if ($http !== null) { http_response_code($http); }
    header("Content-Type: application/json");
    exit(json_encode([
        "status" => $status,
        "message" => $message,
        "more" => $more
    ]));
}

// (C) LOGIN CHECK (KEPT FROM ORIGINAL FOR AUTHENTICATION)
function lcheck () {
    if (!isset($_SESSION["user"])) {
        respond(0, "Please sign in first", null, 403);
    }
}

// (D) HANDLE REQUEST
if (isset($_POST["req"])) { 
   $isAdmin = isset($_SESSION["user"]["privilege"]) && $_SESSION["user"]["privilege"] === 'A';
  switch ($_POST["req"]) {
    // (D1) BAD REQUEST
    default:
        respond(false, "Invalid request", null, 400);
        break;

    // (D2) SAVE BOOK
    case "save": lcheck();
        $pass = $LIB->save(
            $_POST["title"],
            $_POST["author"],
            $_POST["year"],
            $_POST["quantity"],
            $_POST["privilege"],
            isset($_POST["id"]) ? $_POST["id"] : null
        );
        respond($pass, $pass?"OK":$LIB->error);
        break;

    // (D3) DELETE BOOK
    case "del": lcheck();
        if (!$isAdmin) {
                respond(false, "Admin privileges required", null, 403);
            }
        $pass = $LIB->del($_POST["id"]);
        respond($pass, $pass?"OK":$LIB->error);
        break;

    // (D4) GET BOOK
    case "get": lcheck();
        respond(true, "OK", $LIB->get($_POST["id"]));
        break;

    // (D5) GET ALL BOOKS
    case "getAll": lcheck();
        respond(true, "OK", $LIB->getAll());
        break;

    // (D6) SEARCH BOOKS
    case "search": lcheck();
        respond(true, "OK", $LIB->search($_POST["query"]));
        break;

}}