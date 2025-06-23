<?php
// (A) LOAD USER LIBRARY
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


// (C) LOGIN CHECK
function lcheck () {
  if (!isset($_SESSION["user"])) {
    respond(0, "Please sign in first", null, 403);
  }
}

// (D) HANDLE REQUEST
if (isset($_POST["req"])) { switch ($_POST["req"]) {
  // (D1) BAD REQUEST
  default:
    respond(false, "Invalid request", null, null, 400);
    break;

  // (D2) SAVE BOOK
  case "save": lcheck();
    $pass = $Book->save(
      $_POST["book_title"] ?? '',
      $_POST["author"] ?? '',
      $_POST["year"] ?? '',
      $_POST["number_of_books"] ?? 0,
      $_POST["level_of_privilege"] ?? 0,
      $_POST["id"] ?? null
    );


    respond($pass, $pass?"OK":$Book->error);
    break;

  // (D3) DELETE BOOK
  case "del": lcheck();
    $pass = $Book->del($_POST["id"]);
    respond($pass, $pass?"OK":$Book->error);
    break;

  // (D4) GET BOOK
  case "get": lcheck();
    respond(true, "OK", $Book->get($_POST["id"]));
    break;
  // (D5) GET ALL BOOKS
  case "all": lcheck();
    respond(true, "OK", $Book->all());
    break;

}}