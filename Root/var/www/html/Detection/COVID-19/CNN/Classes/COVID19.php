<?php

    class COVID19
    {

        function __construct($_GeniSys)
        {
            $this->_GeniSys = $_GeniSys;

            $this->dataDir = "Data/";
            $this->dataDirFull = "/fserver/var/www/html/Detection/COVID-19/CNN/";
            $this->dataFiles = $this->dataDir . "*.png";
            $this->allowedFiles = ["png","PNG"];
            $this->api = $this->_GeniSys->_helpers->oDecrypt($this->_GeniSys->_confs["domainString"])."/Detection/COVID-19/CNN/API/Inference";
        }

        public function deleteData()
        {
            $images = glob($this->dataFiles);
            foreach( $images as $image ):
                unlink($image);
            endforeach;

            return [
                "Response" => "OK",
                "Message" =>  "Deleted SARS-COV-2 Ct-Scan Dataset"
            ];

        }

        public function uploadData()
        {
            $dataCells = '';  
            if(is_array($_FILES) && !empty($_FILES['covdata'])):
                foreach($_FILES['covdata']['name'] as $key => $filename):
                    $file_name = explode(".", $filename); 
                    if(in_array($file_name[1], $this->allowedFiles)):
                        $sourcePath = $_FILES["covdata"]["tmp_name"][$key];  
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
                            "Message" => "Please upload png files"
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
                    "Message" => "You must upload some images (png)"
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
    
    $COVID19 = new COVID19($_GeniSys);

    if(filter_input(INPUT_POST, "deleteData", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($COVID19->deleteData()));
    endif;

    if(filter_input(INPUT_POST, "uploadCovData", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($COVID19->uploadData()));
    endif;

    if(filter_input(INPUT_POST, "classifyData", FILTER_SANITIZE_NUMBER_INT)):
        die(json_encode($COVID19->classifyData()));
    endif;