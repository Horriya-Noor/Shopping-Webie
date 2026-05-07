<?php
include 'db_connect.php';

// Functions for customers

function getAllCustomers() {
    global $conn;
    $sql = "SELECT * FROM customers";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        return $result;
    } else {
        return false;
    }
}

function getCustomerById($id) {
    global $conn;
    $sql = "SELECT * FROM customers WHERE id = $id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
}

function addCustomer($name, $email, $password) {
    global $conn;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO customers (name, email, password) VALUES ('$name', '$email', '$hashed_password')";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

function updateCustomer($id, $name, $email, $password) {
    global $conn;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE customers SET name='$name', email='$email', password='$hashed_password' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

function deleteCustomer($id) {
    global $conn;
    $sql = "DELETE FROM customers WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

function banCustomer($id) {
    global $conn;
    $sql = "UPDATE customers SET banned=1 WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

function unbanCustomer($id) {
    global $conn;
    $sql = "UPDATE customers SET banned=0 WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}
?>