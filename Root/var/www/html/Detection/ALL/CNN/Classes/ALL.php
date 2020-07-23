<?php

    class ALL
    {

        function __construct($_GeniSys)
        {
            $this->_GeniSys = $_GeniSys;

            $this->dataDir = "Data/";
            $this->dataDirFull = "/fserver/var/www/html/Detection/ALL/CNN/";
            $this->dataFiles = $this->dataDir . "*.jpg";
            $this->allowedFiles = ["jpg","JPG"];
            $this->api = $this->_GeniSys->_helpers->oDecrypt($this->_GeniSys->_confs["domainString"])."/Detection/ALL/CNN/API/Inference";
        }

        public function deleteData()
        {
            $images = glob($this->dataFiles);
            foreach( $images as $image ):
                unlink($image);
            endforeach;

            return [
                "Response" => "OK",
                "Message" =>  "Deleted Acute Lymphoblastic Leukemia Image Database for Image Processing Dataset"
            ];

        }

        public function uploadData()
        {
            $dataCells = '';  
            if(is_array($_FILES) && !empty($_FILES['alldata'])):
                foreach($_FILES['alldata']['name'] as $key => $filename):
                    $file_name = explode(".", $filename); 
                    if(in_array($file_name[1], $this->allowedFiles)):
                        $sourcePath = $_FILES["alldata"]["tmp_name"][$key];  
                        $targetPath = $this->dataDir . $filename;  
                        if(!move_uploaded_file($sourcePath, $targetPath)):
                            return [
                                "Response" => "FAILED",
                                "Message" => "Upload failed " . $targetPath
                            ];
                        endif; 
                    else:
                        return [
                            "Response" => "FAILED",
                            "Message" => "Please upload jpg files"
                        ];
                    endif;
                endforeach;

                $images = glob($this->dataFiles);
                $count = 1;
                foreach($images as $image):
                    $dataCells .= "<div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'><img src='" . $image . "' style='width: 100%; cursor: pointer;' class='classify' title='" . $image . "' id='" . $image . "' /></div>";
                    if($count%6 == 0):
                        $dataCells .= "<div class='clearfix'></div>";
                    endif;
                    $count++;
                endforeach;

            else:
                return [
                    "Response" => "FAILED",
                    "Message" => "You must upload some images (jpg)"
                ];
            endif;

            return [
                "Response" => "OK",
                "Message" => "Data upload OK!",
                "Data" => $dataCells
            ];

        }

        public function classifyData()
        {
            $file = $this->dataDirFull . filter_input(INPUT_POST, "im", FILTER_SANITIZE_STRING);
            $mime = mime_content_type($file);
            $info = pathinfo($file);
            $name = $info['basename'];
            $toSend = new CURLFile($file, $mime, $name);

            $ch = curl_init($this->api);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'file'=> $toSend,
            ]);

            $resp = curl_exec($ch);

            return json_decode($resp, true);

        }

    }
    
    $ALL = new ALL($_GeniSys);

    if(filter_input(INPUT_POST, "deleteData", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($ALL->deleteData()));
    endif;

    if(filter_input(INPUT_POST, "uploadAllData", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($ALL->uploadData()));
    endif;

    if(filter_input(INPUT_POST, "classifyData", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($ALL->classifyData()));
    endif;