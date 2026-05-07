<?php
include 'db_connect.php';

// Functions for products (shopping lists)

function getAllProducts() {
    global $conn;
    $sql = "SELECT * FROM products";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        return $result;
    } else {
        return false;
    }
}

function getProductById($id) {
    global $conn;
    $sql = "SELECT * FROM products WHERE id = $id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
}

function addProduct($name, $price, $description, $category) {
    global $conn;
    $sql = "INSERT INTO products (name, price, description, category) VALUES ('$name', '$price', '$description', '$category')";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

function updateProduct($id, $name, $price, $description, $category) {
    global $conn;
    $sql = "UPDATE products SET name='$name', price='$price', description='$description', category='$category' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

function deleteProduct($id) {
    global $conn;
    $sql = "DELETE FROM products WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}

function addSalesTag($id, $tag) {
    global $conn;
    $sql = "UPDATE products SET sales_tag='$tag' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        return true;
    } else {
        return false;
    }
}
?>