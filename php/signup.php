<?php
    error_reporting(0);
    session_start();
    include_once "config.php";
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    if(!empty($fname) && !empty($lname) && !empty($email) && !empty($password)) {
        //to check if user email is valid or not
        if(filter_var($email, FILTER_VALIDATE_EMAIL)) {      //if email is valid
            //to check if email already exists in the database
            $sql = mysqli_query($conn, "SELECT email FROM users WHERE email ='$email'");
            if(mysqli_num_rows($sql) > 0) {
                echo "$email - This email already exists!";
            }
            else {      //to check if user had uploaded file or not
                if(isset($_FILES['image'])) {       //if file is uploaded
                    $img_name = $_FILES['image']['name'];    //getting user uploaded img name
                    $img_type = $_FILES['image']['type'];    //getting type of image
                    $tmp_name = $_FILES['image']['tmp_name'];    //temporary name is used to save file in our folder

                    //explode image and get the extension like .jpg or .png

                    $img_explode = explode('.', $img_name);
                    $img_ext = end($img_explode);    //to get the extension of uploaded image
                    $extensions = ['png', 'jpeg', 'jpg'];   //valid extensions

                    if(in_array($img_ext, $extensions) === true) {
                        $time = time();         //this will return the current time
                                                //when storing uploaded images in our folder, they will be renamed with the current time which will be unique always
                        //moving image to our folder
                        $new_img_name = $time.$img_name;
                        if(move_uploaded_file($tmp_name, "images/".$new_img_name)) {
                            $status = "Active now";
                            $random_id = rand(time(), 10000000);    //creating random id for user
                            
                            //inserting user data into the table 'users'
                            $sql2 = mysqli_query($conn, "INSERT INTO users (unique_id, fname, lname, email, password, img, status)
                                                VALUES ({$random_id}, '{$fname}', '{$lname}', '{$email}', '{$password}', '{$new_img_name}', '{$status}')");
                            if($sql2) {
                                $sql3 = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
                                if(mysqli_num_rows($sql3) > 0) {
                                    $row = mysqli_fetch_assoc($sql3);
                                    $_SESSION['unique_id'] = $row['unique_id'];
                                    echo "success";
                                }
                            }
                            else {
                                echo "Something went wrong!!!";
                            }
                        }
                    }
                    else {
                        echo "Please select an Image file - jpeg, jpg, png .";
                    }
                }
                else {
                    echo "Please upload an Image file.";
                }
            }
        }   
        else {
            echo "$email - This is not a valid email!";
        }
    }
    else {
        echo "All input fields are required";
    }
?>