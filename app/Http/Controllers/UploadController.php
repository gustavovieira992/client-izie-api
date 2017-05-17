<?php

namespace ClienteIzie\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        $target_path = "img/";

        $target_path = $target_path . basename( $_FILES['file']['name']);

        if(move_uploaded_file($_FILES['file']['name'], $target_path)) {
            echo "Upload and move success";
        } else{
            echo $target_path;
            echo $_FILES['file']['name'];
            echo 1;
            echo "There was an error uploading the file, please try again!";
        }

    }
}
