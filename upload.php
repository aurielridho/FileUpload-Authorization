<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    
    // Jika email adalah "admin", atur peran sebagai "premium"
    $role = ($email === "admin") ? "premium" : "user";

    // ... validasi email ...

    if ($_FILES["file"]["error"] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png'];

        if ($role === "premium") {
            $allowedTypes[] = 'application/pdf';
        }

        if (in_array($_FILES["file"]["type"], $allowedTypes)) {
            $targetDir = "fileuploads/";
            $targetFile = $targetDir . basename($_FILES["file"]["name"]);

            if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                $pdo = new PDO('mysql:host=localhost;dbname=fileuploads', 'root');

                $stmt = $pdo->prepare("INSERT INTO fileuploads (email, file_name, role) VALUES (?, ?, ?)");
                $stmt->execute([$email, $_FILES["file"]["name"], $role]);

                echo "File berhasil diunggah dan data disimpan di database.";
            } else {
                echo "Gagal mengunggah file.";
            }
        } else {
            echo "Hanya file JPEG dan PNG yang diizinkan.";
        }
    } else {
        echo "Terjadi kesalahan saat mengunggah file.";
    }
}
?>
