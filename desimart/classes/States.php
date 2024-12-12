<?php 
    class States {
        private $file;

        public function __construct($file)
        {
            $this->file = $file;
        }

        public function getStates() {
            if(!file_exists($this->file)) {
                return  ["error" => "States file not found."];
            } 

            $jsonData = file_get_contents($this->file);
            $statesArray = json_decode($jsonData, true);

            if(isset($statesArray['states']) && is_array($statesArray['states'])) {
                return $statesArray['states'];
            }

            return  ["error" => "Invalid data."];
        }
    }
?>