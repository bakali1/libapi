<?php
// (A) LOAD USER LIBRARY
require "users-lib.php";

// (B) STANDARD JSON RESPONSE
function respond ($status, $message, $more=null, $http=null) {
  if ($http !== null) { http_response_code($http); }
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

  // (D2) SAVE USER
  case "save": lcheck();
    $pass = $USR->save(
      $_POST["email"], $_POST["password"],
      isset($_POST["id"]) ? $_POST["id"] : null
    );
    respond($pass, $pass?"OK":$USR->error);
    break;

  // (D3) DELETE USER
  case "del": lcheck();
    $pass = $USR->del($_POST["id"]);
    respond($pass, $pass?"OK":$USR->error);
    break;

  // (D4) GET USER
  case "get": lcheck();
    respond(true, "OK", $USR->get($_POST["id"]));
    break;

  // (D5) LOGIN
  case "in":
    // ALREADY SIGNED IN
    if (isset($_SESSION["user"])) { respond(true, "OK"); }

    // CREDENTIALS CHECK
    $pass = $USR->verify($_POST["email"], $_POST["password"]);
    respond($pass, $pass?"OK":"Invalid email/password");
    break;

  // (D6) LOGOUT
  case "out":
    unset($_SESSION["user"]);
    respond(true, "OK");
    break;
}}